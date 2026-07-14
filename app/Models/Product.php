<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'description',
        'price',
        'gradient',
        'image',
        'is_bestseller',
        'is_new',
        'stock',
    ];

    protected $casts = [
        'is_bestseller' => 'boolean',
        'is_new' => 'boolean',
        'stock' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    protected function averageRating(): Attribute
    {
        return Attribute::get(fn () => round((float) ($this->reviews_avg_rating ?? $this->reviews()->avg('rating') ?? 0), 1));
    }

    protected function reviewsCount(): Attribute
    {
        return Attribute::get(fn () => $this->reviews_count ?? $this->reviews()->count());
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::get(fn () => 'Rp '.number_format($this->price, 0, ',', '.'));
    }

    protected function badge(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->is_bestseller => 'Bestseller',
            $this->is_new => 'New',
            default => null,
        });
    }

    protected function inStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock > 0);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? Storage::disk('supabase')->url($this->image) : null);
    }
}
