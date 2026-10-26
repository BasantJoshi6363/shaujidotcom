<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['business_id', 'user_id', 'action', 'entity_type', 'entity_id', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}