<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\CreditTransaction;
use App\Models\Customer;
use App\Support\Money;
use Illuminate\Validation\ValidationException;
use LogicException;
use Tests\ShopTestCase;

class LedgerTest extends ShopTestCase
{
    // ---------- balance rules ----------

    public function test_balance_matches_the_example_in_the_spec(): void
    {
        [$user, , $customer] = $this->shop();

        $this->ledger()->openingBalance($customer, 200000, $user); // Rs. 2,000
        $this->ledger()->credit($customer, 150000, $user);         // Rs. 1,500
        $this->ledger()->payment($customer, 100000, $user);        // Rs. 1,000

        $this->assertSame(250000, $this->ledger()->balance($customer)); // Rs. 2,500
    }

    public function test_partial_payments_reduce_the_balance_step_by_step(): void
    {
        [$user, , $customer] = $this->shop();

        $this->ledger()->credit($customer, 100000, $user);
        $this->ledger()->payment($customer, 30000, $user);
        $this->assertSame(70000, $this->ledger()->balance($customer));

        $this->ledger()->payment($customer, 70000, $user);
        $this->assertSame(0, $this->ledger()->balance($customer));
    }

    public function test_overpayment_becomes_a_negative_balance(): void
    {
        [$user, , $customer] = $this->shop();

        $this->ledger()->credit($customer, 50000, $user);
        $this->ledger()->payment($customer, 80000, $user);

        $this->assertSame(-30000, $this->ledger()->balance($customer));
    }

    public function test_zero_and_negative_amounts_are_rejected(): void
    {
        [$user, , $customer] = $this->shop();

        foreach ([0, -100] as $bad) {
            try {
                $this->ledger()->credit($customer, $bad, $user);
                $this->fail("Amount {$bad} should have been rejected");
            } catch (\InvalidArgumentException) {
                $this->assertTrue(true);
            }
        }

        $this->assertSame(0, CreditTransaction::count());
    }

    // ---------- money parsing ----------

    public function test_money_parsing_and_formatting(): void
    {
        $this->assertSame(150050, Money::toPaisa('1,500.50'));
        $this->assertSame(150000, Money::toPaisa('1500'));
        $this->assertSame(1050, Money::toPaisa('10.5'));
        $this->assertNull(Money::toPaisa('abc'));
        $this->assertNull(Money::toPaisa('-5'));
        $this->assertNull(Money::toPaisa('1.234'));
        $this->assertNull(Money::toPaisa(''));

        $this->assertSame('NPR 1,500', Money::format(150000));
        $this->assertSame('NPR 1,500.50', Money::format(150050));
        $this->assertSame('-NPR 30', Money::format(-3000));
    }

    // ---------- duplicate protection ----------

    public function test_same_idempotency_key_creates_only_one_entry(): void
    {
        [$user, , $customer] = $this->shop();

        $first = $this->ledger()->credit($customer, 100000, $user, null, null, 'key-1');
        $second = $this->ledger()->credit($customer, 100000, $user, null, null, 'key-1');

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, CreditTransaction::count());
        $this->assertSame(100000, $this->ledger()->balance($customer));
    }

    public function test_double_form_submission_creates_only_one_entry(): void
    {
        [$user, , $customer] = $this->shop();
        $payload = ['amount' => '1,500', 'idempotency_key' => 'form-abc'];

        $this->actingAs($user)->post(route('customers.credit', $customer->id), $payload)->assertRedirect();
        $this->actingAs($user)->post(route('customers.credit', $customer->id), $payload)->assertRedirect();

        $this->assertSame(1, CreditTransaction::count());
        $this->assertSame(150000, $this->ledger()->balance($customer));
    }

    public function test_two_shops_may_use_the_same_key(): void
    {
        [$userA, , $customerA] = $this->shop('Customer A');
        [$userB, , $customerB] = $this->shop('Customer B');

        $this->ledger()->credit($customerA, 1000, $userA, null, null, 'same-key');
        $this->ledger()->credit($customerB, 2000, $userB, null, null, 'same-key');

        $this->assertSame(2, CreditTransaction::count());
    }

    // ---------- reversals ----------

    public function test_reversing_a_payment_restores_the_balance(): void
    {
        [$user, , $customer] = $this->shop();

        $this->ledger()->credit($customer, 100000, $user);
        $payment = $this->ledger()->payment($customer, 40000, $user);
        $this->assertSame(60000, $this->ledger()->balance($customer));

        $reversal = $this->ledger()->reverse($payment, $user, 'entered by mistake');

        $this->assertSame(100000, $this->ledger()->balance($customer));
        $this->assertSame('reversal', $reversal->type);
        $this->assertSame($payment->id, $reversal->reversal_of_id);
        $this->assertSame(3, CreditTransaction::count()); // nothing was deleted
    }

    public function test_an_entry_cannot_be_reversed_twice(): void
    {
        [$user, , $customer] = $this->shop();
        $payment = $this->ledger()->payment($customer, 10000, $user);

        $this->ledger()->reverse($payment, $user);

        $this->expectException(ValidationException::class);
        $this->ledger()->reverse($payment, $user);
    }

    public function test_a_reversal_cannot_be_reversed(): void
    {
        [$user, , $customer] = $this->shop();
        $payment = $this->ledger()->payment($customer, 10000, $user);
        $reversal = $this->ledger()->reverse($payment, $user);

        $this->expectException(ValidationException::class);
        $this->ledger()->reverse($reversal, $user);
    }

    public function test_ledger_entries_cannot_be_edited_or_deleted(): void
    {
        [$user, , $customer] = $this->shop();
        $tx = $this->ledger()->credit($customer, 5000, $user);

        try {
            $tx->update(['amount' => 1]);
            $this->fail('Editing a ledger entry should be blocked');
        } catch (LogicException) {
            $this->assertTrue(true);
        }

        try {
            $tx->delete();
            $this->fail('Deleting a ledger entry should be blocked');
        } catch (LogicException) {
            $this->assertTrue(true);
        }

        $this->assertSame(5000, $tx->fresh()->amount);
    }

    // ---------- audit trail ----------

    public function test_every_entry_is_written_to_the_audit_log(): void
    {
        [$user, $business, $customer] = $this->shop();

        $tx = $this->ledger()->credit($customer, 5000, $user);

        $log = AuditLog::where('action', 'transaction.credit')->first();
        $this->assertNotNull($log);
        $this->assertSame($tx->id, $log->entity_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame($business->id, $log->business_id);
    }

    // ---------- through the website ----------

    public function test_invalid_amounts_are_rejected_by_the_form(): void
    {
        [$user, , $customer] = $this->shop();

        foreach (['abc', '-50', '0', '1.999', ''] as $bad) {
            $this->actingAs($user)
                ->post(route('customers.credit', $customer->id), ['amount' => $bad, 'idempotency_key' => 'k-' . $bad])
                ->assertSessionHasErrorsIn('credit', 'amount');
        }

        $this->assertSame(0, CreditTransaction::count());
    }

    public function test_future_dates_are_rejected(): void
    {
        [$user, , $customer] = $this->shop();

        $this->actingAs($user)
            ->post(route('customers.payments', $customer->id), [
                'amount' => '100',
                'occurred_on' => now()->addDays(3)->toDateString(),
                'idempotency_key' => 'future-1',
            ])
            ->assertSessionHasErrorsIn('payment', 'occurred_on');

        $this->assertSame(0, CreditTransaction::count());
    }

    public function test_recording_credit_and_payment_through_the_website(): void
    {
        [$user, , $customer] = $this->shop();

        $this->actingAs($user)->post(route('customers.credit', $customer->id), ['amount' => '2000', 'idempotency_key' => 'a1']);
        $this->actingAs($user)->post(route('customers.payments', $customer->id), ['amount' => '500.50', 'idempotency_key' => 'a2']);

        $this->assertSame(149950, $this->ledger()->balance($customer));
        $this->actingAs($user)->get(route('customers.show', $customer->id))->assertOk()->assertSee('NPR 1,499.50');
    }

    public function test_customer_with_history_cannot_be_deleted(): void
    {
        [$user, , $customer] = $this->shop();
        $this->ledger()->credit($customer, 1000, $user);

        $this->actingAs($user)
            ->delete(route('delete-customer', $customer->id))
            ->assertSessionHasErrors('delete');

        $this->assertNotNull(Customer::find($customer->id));
    }

    public function test_customer_without_history_can_be_deleted(): void
    {
        [$user, , $customer] = $this->shop();

        $this->actingAs($user)->delete(route('delete-customer', $customer->id))->assertRedirect(route('customers'));

        $this->assertNull(Customer::find($customer->id));
    }

    public function test_opening_balance_is_recorded_when_a_customer_is_created(): void
    {
        [$user] = $this->shop();

        $this->actingAs($user)->post(route('create-customer.post'), [
            'name' => 'New Person',
            'opening_balance' => '2,000',
        ])->assertRedirect();

        $customer = Customer::where('name', 'New Person')->firstOrFail();
        $this->assertSame(200000, $this->ledger()->balance($customer));
        $this->assertSame('opening_balance', $customer->transactions()->first()->type);
    }
}