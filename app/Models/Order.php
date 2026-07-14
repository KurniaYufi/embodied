<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    public const PAYMENT_TIMEOUT_MINUTES = 10;

    protected $fillable = [
        'user_id',
        'number',
        'access_token',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'notes',
        'subtotal',
        'status',
        'payment_method_id',
        'payment_proof_path',
        'payment_proof_uploaded_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_proof_uploaded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->number ??= 'ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
            $order->access_token ??= Str::random(40);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function formattedSubtotal(): Attribute
    {
        return Attribute::get(fn () => 'Rp '.number_format($this->subtotal, 0, ',', '.'));
    }

    protected function paymentProofUrl(): Attribute
    {
        return Attribute::get(fn () => $this->payment_proof_path ? Storage::disk('supabase')->url($this->payment_proof_path) : null);
    }

    public function hasPaymentProof(): bool
    {
        return filled($this->payment_proof_path);
    }

    protected function paymentDeadline(): Attribute
    {
        return Attribute::get(fn () => $this->created_at->addMinutes(self::PAYMENT_TIMEOUT_MINUTES));
    }

    public function isPaymentExpired(): bool
    {
        return $this->status === OrderStatus::PendingPayment && now()->greaterThan($this->payment_deadline);
    }

    public function cancelDueToExpiredPayment(): void
    {
        if ($this->isPaymentExpired()) {
            $this->update(['status' => OrderStatus::Cancelled]);
        }
    }
}
