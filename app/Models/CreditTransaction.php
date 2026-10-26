<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * Append-only ledger row. Never edited, never deleted: mistakes are fixed with a reversal.
 */
class CreditTransaction extends Model
{
    public const OPENING = 'opening_balance';
    public const CREDIT = 'credit';
    public const PAYMENT = 'payment';
    public const ADJUSTMENT = 'adjustment';
    public const REVERSAL = 'reversal';

    protected $fillable = [
        'business_id', 'customer_id', 'type', 'amount', 'description',
        'occurred_at', 'created_by', 'reversal_of_id', 'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Ledger entries cannot be edited. Use a reversal.'));
        static::deleting(fn () => throw new LogicException('Ledger entries cannot be deleted. Use a reversal.'));
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}