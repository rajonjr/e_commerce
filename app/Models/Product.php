<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'sort_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'manage_stock',
        'stock_status',
        'is_active',
        'is_featured',
        'has_variants',
        'weight',
        'meta_title',
        'meta_description',
        'views_count'
])]
class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected function casts()
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'views_count' => 'integer',
            'manage_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'has_variants' => 'boolean'
        ];
    }

    #[Scope]
    protected function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }

     #[Scope]
    protected function scopeFeatured(Builder $query)
    {
        $query->where('is_featured', true);
    }

    #[Scope]
    protected function lowStock(Builder $builder)
    {
        $builder->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
        ->where('stock_quantity', '>', 0);
    }

    #[Scope]
    protected function inCategory(Builder $builder, int $categoryId)
    {
        return $builder->where('category_id', $categoryId);
    }

    #[Scope]
    protected function ofBrand(Builder $builder, int $brandId)
    {
        return $builder->where('brand_id', $brandId);
    }

    #[Scope]
    protected function inPriceRange(Builder $builder, float $min, float $max)
    {
        return $builder->whereBetween('price', [$min, $max]);
    }

    //Relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    //Helper Method
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function incrementViews()
    {
        return $this->increment('views_count');
    }

    //Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}