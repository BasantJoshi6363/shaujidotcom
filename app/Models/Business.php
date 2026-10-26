<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    public const TYPES = [
        'pharmacy'  => 'Medical store / Pharmacy',
        'grocery'   => 'General store / Grocery',
        'hardware'  => 'Hardware / Building material',
        'clothing'  => 'Clothing / Retail',
        'wholesale' => 'Wholesaler / Distributor',
        'other'     => 'Other',
    ];

    protected $fillable = ['owner_id', 'name', 'business_type', 'phone', 'address', 'status'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function transactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }
}