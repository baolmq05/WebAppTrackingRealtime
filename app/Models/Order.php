<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'customer_id',
    'shipper_id',
    'product_id',
    'quantity',
    'status',
    'delivery_address',
    'delivery_lat',
    'delivery_lng',
    'shipper_lat',
    'shipper_lng'
])]
class Order extends Model
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipper_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'customer_id' => 'integer',
            'shipper_id' => 'integer',
            'product_id' => 'integer',
            'quantity' => 'integer',
            'delivery_lat' => 'double',
            'delivery_lng' => 'double',
            'shipper_lat' => 'double',
            'shipper_lng' => 'double',
        ];
    }
}
