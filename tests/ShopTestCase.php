<?php

namespace Tests;

use App\Models\Business;
use App\Models\Customer;
use App\Models\User;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Shared setup for tests that need a shop with a customer.
 * RefreshDatabase wipes the test database, so phpunit.xml MUST point at a separate
 * database (sqlite :memory:), never your real MySQL one.
 */
abstract class ShopTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // pages render without needing `npm run build`
    }

    /** Creates a verified user who owns a shop with one customer. Returns [user, business, customer]. */
    protected function shop(string $customerName = 'Test Customer'): array
    {
        $user = User::factory()->create();

        $business = Business::create([
            'owner_id' => $user->id,
            'name' => 'Test Shop',
            'business_type' => 'grocery',
        ]);

        $customer = Customer::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'name' => $customerName,
        ]);

        return [$user, $business, $customer];
    }

    protected function ledger(): LedgerService
    {
        return app(LedgerService::class);
    }
}