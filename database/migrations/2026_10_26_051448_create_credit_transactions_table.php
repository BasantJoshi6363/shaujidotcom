<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            // restrict: a customer with ledger history can never be deleted by accident
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('type', 20); // opening_balance, credit, payment, adjustment, reversal
            // Signed integer in paisa: credit/opening = positive (customer owes), payment = negative
            $table->bigInteger('amount');
            $table->string('description')->nullable();
            $table->timestamp('occurred_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // A transaction can be reversed only once (enforced by this unique index)
            $table->foreignId('reversal_of_id')->nullable()->unique()
                ->constrained('credit_transactions')->nullOnDelete();
            $table->string('idempotency_key', 64)->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'idempotency_key']);
            $table->index(['customer_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};