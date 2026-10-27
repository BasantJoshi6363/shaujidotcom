<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Services\ReportService;
use Tests\ShopTestCase;

class ReportTest extends ShopTestCase
{
    public function test_dashboard_totals(): void
    {
        [$user, $business, $ram] = $this->shop('Ram Customer');
        $sita = Customer::create(['business_id' => $business->id, 'user_id' => $user->id, 'name' => 'Sita Customer']);

        $this->ledger()->credit($ram, 500000, $user);   // Ram owes Rs. 5,000
        $this->ledger()->payment($sita, 20000, $user);  // Sita paid Rs. 200 in advance

        $stats = app(ReportService::class)->dashboard($business);

        $this->assertSame(500000, $stats['total_outstanding']);
        $this->assertSame(1, $stats['debtor_count']);
        $this->assertSame(20000, $stats['advance_total']);
        $this->assertSame(2, $stats['customer_count']);
        $this->assertSame('Ram Customer', $stats['top_debtors'][0]['customer']->name);
    }

    public function test_opening_balance_is_not_counted_as_new_credit_today(): void
    {
        [$user, $business, $customer] = $this->shop();

        $this->ledger()->openingBalance($customer, 300000, $user);
        $this->ledger()->credit($customer, 100000, $user);

        $today = app(ReportService::class)->movement($business, now()->startOfDay(), now()->endOfDay());

        $this->assertSame(100000, $today['credit']);
    }

    public function test_reversing_a_payment_lowers_payments_not_raises_credit(): void
    {
        [$user, $business, $customer] = $this->shop();

        $this->ledger()->credit($customer, 500000, $user);
        $payment = $this->ledger()->payment($customer, 100000, $user);
        $this->ledger()->reverse($payment, $user);

        $today = app(ReportService::class)->movement($business, now()->startOfDay(), now()->endOfDay());

        $this->assertSame(500000, $today['credit']);
        $this->assertSame(0, $today['payments']);
    }

    public function test_outstanding_report_lists_only_people_who_owe(): void
    {
        [$user, $business, $ram] = $this->shop('Ram Customer');
        $sita = Customer::create(['business_id' => $business->id, 'user_id' => $user->id, 'name' => 'Sita Customer']);
        $gita = Customer::create(['business_id' => $business->id, 'user_id' => $user->id, 'name' => 'Gita Customer']);

        $this->ledger()->credit($ram, 100000, $user);
        $this->ledger()->payment($sita, 5000, $user);   // advance: not a debtor
        $this->ledger()->credit($gita, 4000, $user);
        $this->ledger()->payment($gita, 4000, $user);   // settled: not a debtor

        $this->actingAs($user)->get(route('reports.outstanding'))
            ->assertOk()
            ->assertSee('Ram Customer')
            ->assertDontSee('Sita Customer')
            ->assertDontSee('Gita Customer');
    }

    public function test_transactions_can_be_filtered_by_customer_and_type(): void
    {
        [$user, $business, $ram] = $this->shop('Ram Customer');
        $sita = Customer::create(['business_id' => $business->id, 'user_id' => $user->id, 'name' => 'Sita Customer']);

        $this->ledger()->credit($ram, 100000, $user, 'ram-credit-note');
        $this->ledger()->payment($sita, 5000, $user, 'sita-payment-note');

        $this->actingAs($user)->get(route('transactions', ['customer_id' => $ram->id]))
            ->assertOk()->assertSee('ram-credit-note')->assertDontSee('sita-payment-note');

        $this->actingAs($user)->get(route('transactions', ['type' => 'payment']))
            ->assertOk()->assertSee('sita-payment-note')->assertDontSee('ram-credit-note');
    }

    public function test_dashboard_page_loads_with_the_numbers(): void
    {
        [$user, , $customer] = $this->shop();
        $this->ledger()->credit($customer, 250000, $user);

        $this->actingAs($user)->get(route('welcome'))
            ->assertOk()
            ->assertSee('Total outstanding')
            ->assertSee('NPR 2,500');
    }
}