<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemoOrder extends Model
{
    protected $fillable = [
        'name',
        'email',
        'description',
        'notes',
        'status',
        'started_at',
        'ended_at',
        'base_price',
        'tax_rate',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'base_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(DemoOrderItem::class);
    }
}
