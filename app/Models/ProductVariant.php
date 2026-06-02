<?php

namespace App\Models;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['product_id','sku','name','options','price','compare_price','stock_quantity','stock_status','is_active','sort_order'])]
class ProductVariant extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ];
    }

    #[Scope]
    protected function active(Builder $builder)
    {
        $builder->where('is_active', true);
    }

    protected function inStock(Builder $builder)
    {
        $builder->where('stock_status', 'in_stock')
        ->where('stock_quantity', '>', 0);
    }

    //Relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    //Helper Method
    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round(($this->compare_price - $this->price) / $this->compare_price * 100);
        }

        return 0;
    }

    //Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(Str::random(8));
            }
        });
    }
}