<?php

namespace Tests\Feature;

use App\Models\CreditTransaction;
use App\Models\Customer;
use Tests\ShopTestCase;

/**
 * "A user cannot access another business's private financial records."
 * Every test here logs in as shop B and tries to touch shop A by changing IDs.
 */
class TenantIsolationTest extends ShopTestCase
{
    private $userB;
    private $customerA;
    private $customerB;
    private $txA;

    protected function setUp(): void
    {
        parent::setUp();

        [$userA, , $this->customerA] = $this->shop('Zorblax Customer');
        [$this->userB, , $this->customerB] = $this->shop('Quillon Customer');

        $this->txA = $this->ledger()->credit($this->customerA, 99900, $userA, 'secret-note-A');
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('customers.show', $this->customerA->id))->assertRedirect(route('login'));
        $this->get(route('customers'))->assertRedirect(route('login'));
    }

    public function test_other_shops_ledger_page_is_not_found(): void
    {
        $this->actingAs($this->userB)->get(route('customers.show', $this->customerA->id))->assertNotFound();
    }

    public function test_cannot_add_credit_or_payment_to_another_shops_customer(): void
    {
        $before = CreditTransaction::count();

        $this->actingAs($this->userB)
            ->post(route('customers.credit', $this->customerA->id), ['amount' => '100', 'idempotency_key' => 'x1'])
            ->assertNotFound();

        $this->actingAs($this->userB)
            ->post(route('customers.payments', $this->customerA->id), ['amount' => '100', 'idempotency_key' => 'x2'])
            ->assertNotFound();

        $this->assertSame($before, CreditTransaction::count());
    }

    public function test_cannot_reverse_another_shops_transaction(): void
    {
        $before = CreditTransaction::count();

        $this->actingAs($this->userB)
            ->post(route('transactions.reverse', $this->txA->id), ['idempotency_key' => 'x3'])
            ->assertNotFound();

        $this->assertSame($before, CreditTransaction::count());
    }

    public function test_cannot_edit_update_or_delete_another_shops_customer(): void
    {
        $this->actingAs($this->userB)->get(route('edit-customer', $this->customerA->id))->assertNotFound();

        $this->actingAs($this->userB)
            ->put(route('update-customer', $this->customerA->id), ['name' => 'Hacked'])
            ->assertNotFound();

        $this->actingAs($this->userB)->delete(route('delete-customer', $this->customerA->id))->assertNotFound();

        $this->assertSame('Zorblax Customer', Customer::find($this->customerA->id)->name);
    }

    public function test_customer_list_and_search_only_show_own_customers(): void
    {
        $this->actingAs($this->userB)->get(route('customers'))
            ->assertOk()->assertSee('Quillon Customer')->assertDontSee('Zorblax Customer');

        // The old orWhere bug leaked other shops' customers through search
        $this->actingAs($this->userB)->get(route('search-customers', ['search-customer' => 'Zorblax']))
            ->assertOk()->assertDontSee('Zorblax Customer');

        $this->actingAs($this->userB)->get(route('search-customers', ['search-customer' => 'Customer']))
            ->assertOk()->assertSee('Quillon Customer')->assertDontSee('Zorblax Customer');
    }

    public function test_transactions_report_never_shows_other_shops_entries(): void
    {
        $this->actingAs($this->userB)->get(route('transactions'))
            ->assertOk()->assertDontSee('secret-note-A')->assertDontSee('Zorblax Customer');
    }

    public function test_transactions_filter_rejects_another_shops_customer_id(): void
    {
        $this->actingAs($this->userB)
            ->get(route('transactions', ['customer_id' => $this->customerA->id]))
            ->assertSessionHasErrors('customer_id');
    }

    public function test_outstanding_report_never_shows_other_shops_debtors(): void
    {
        $this->actingAs($this->userB)->get(route('reports.outstanding'))
            ->assertOk()->assertDontSee('Zorblax Customer');
    }

    public function test_same_email_is_allowed_in_two_different_shops(): void
    {
        $this->actingAs($this->userB)->post(route('create-customer.post'), [
            'name' => 'Shared Email', 'email' => 'same@example.com',
        ])->assertSessionHasNoErrors();

        // Shop A already has a customer with that email -> shop B must not be blocked by it
        $this->customerA->update(['email' => 'dup@example.com']);

        $this->actingAs($this->userB)->post(route('create-customer.post'), [
            'name' => 'Another', 'email' => 'dup@example.com',
        ])->assertSessionHasNoErrors();
    }
}