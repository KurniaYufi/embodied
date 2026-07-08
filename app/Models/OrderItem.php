<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'size',
        'price',
        'quantity',
        'gradient',
        'image',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function lineTotal(): Attribute
    {
        return Attribute::get(fn () => $this->price * $this->quantity);
    }

    protected function formattedLineTotal(): Attribute
    {
        return Attribute::get(fn () => 'Rp '.number_format($this->price * $this->quantity, 0, ',', '.'));
    }
}
