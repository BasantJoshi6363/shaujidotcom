<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\CreditTransaction as Tx;
use App\Models\Customer;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The only place that writes to the ledger. Web controllers and the future
 * mobile API both call this, so the rules live in one place.
 *
 * Sign convention (amounts in paisa):
 *   opening_balance, credit  -> positive (customer owes more)
 *   payment                  -> negative (customer owes less)
 *   reversal                 -> the opposite of the entry it cancels
 *   balance = SUM(amount). A negative balance means the customer has paid in advance.
 */
class LedgerService
{
    public function balance(Customer $customer): int
    {
        return (int) Tx::where('customer_id', $customer->id)->sum('amount');
    }

    public function openingBalance(Customer $customer, int $paisa, User $by, ?CarbonInterface $at = null, ?string $key = null): Tx
    {
        return $this->record($customer, Tx::OPENING, $this->positive($paisa), $by, 'Opening balance', $at, $key);
    }

    public function credit(Customer $customer, int $paisa, User $by, ?string $note = null, ?CarbonInterface $at = null, ?string $key = null): Tx
    {
        return $this->record($customer, Tx::CREDIT, $this->positive($paisa), $by, $note, $at, $key);
    }

    public function payment(Customer $customer, int $paisa, User $by, ?string $note = null, ?CarbonInterface $at = null, ?string $key = null): Tx
    {
        return $this->record($customer, Tx::PAYMENT, -$this->positive($paisa), $by, $note, $at, $key);
    }

    public function reverse(Tx $original, User $by, ?string $reason = null, ?string $key = null): Tx
    {
        if ($original->type === Tx::REVERSAL) {
            throw ValidationException::withMessages(['transaction' => 'A reversal cannot be reversed.']);
        }

        $note = 'Reversal' . ($reason ? ': ' . $reason : '');

        return $this->record(
            $original->customer, Tx::REVERSAL, -$original->amount, $by, $note, null, $key, $original->id
        );
    }

    private function positive(int $paisa): int
    {
        if ($paisa <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        return $paisa;
    }

    private function record(
        Customer $customer,
        string $type,
        int $signedAmount,
        User $by,
        ?string $description,
        ?CarbonInterface $at,
        ?string $key,
        ?int $reversalOf = null
    ): Tx {
        try {
            return DB::transaction(function () use ($customer, $type, $signedAmount, $by, $description, $at, $key, $reversalOf) {
                // Lock this customer's row so simultaneous entries for them run one at a time
                Customer::whereKey($customer->id)->lockForUpdate()->firstOrFail();

                // Same form submitted twice -> return the first entry instead of creating another
                if ($key && ($existing = $this->findByKey($customer->business_id, $key))) {
                    return $existing;
                }

                if ($reversalOf && Tx::where('reversal_of_id', $reversalOf)->exists()) {
                    throw ValidationException::withMessages(['transaction' => 'This entry has already been reversed.']);
                }

                $tx = Tx::create([
                    'business_id' => $customer->business_id,
                    'customer_id' => $customer->id,
                    'type' => $type,
                    'amount' => $signedAmount,
                    'description' => $description,
                    'occurred_at' => $at ?? now(),
                    'created_by' => $by->id,
                    'reversal_of_id' => $reversalOf,
                    'idempotency_key' => $key,
                ]);

                AuditLog::create([
                    'business_id' => $customer->business_id,
                    'user_id' => $by->id,
                    'action' => 'transaction.' . $type,
                    'entity_type' => 'credit_transaction',
                    'entity_id' => $tx->id,
                    'metadata' => [
                        'customer_id' => $customer->id,
                        'amount' => $signedAmount,
                        'reversal_of_id' => $reversalOf,
                    ],
                ]);

                return $tx;
            });
        } catch (QueryException $e) {
            // Two identical requests raced each other: the unique index stopped the second one
            if ($key && ($existing = $this->findByKey($customer->business_id, $key))) {
                return $existing;
            }
            throw $e;
        }
    }

    private function findByKey(int $businessId, string $key): ?Tx
    {
        return Tx::where('business_id', $businessId)->where('idempotency_key', $key)->first();
    }
}