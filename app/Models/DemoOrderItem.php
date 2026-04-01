<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoOrderItem extends Model
{
    protected $fillable = [
        'demo_order_id',
        'product_name',
        'quantity',
        'unit_price',
        'note',
    ];

    public function demoOrder(): BelongsTo
    {
        return $this->belongsTo(DemoOrder::class);
    }
}
