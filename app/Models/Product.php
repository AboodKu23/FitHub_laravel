<?php

namespace App\Models;

use Decimal\Decimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'description',
        'price',
        'discount',
        'quantity',
        'sku',
        'images',
        'specifications',
        'status',
        'is_active',
        'brand',
    ];

    protected $casts = [
      'images' => 'array',
      'specifications' => 'array',
      'price' => 'decimal:2',
      'discount' => 'decimal:2',
      'is_active' => 'boolean',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function getDiscountPercentAttribute(): float
    {
        if ($this->discount){
            return round((($this->price - $this->discount) / $this->price) * 100);
        }
        return 0;
    }

    public function getPriceAttribute()
    {
        return $this->discount ?: $this->attributes['price'];
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}
