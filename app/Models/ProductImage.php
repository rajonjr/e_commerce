<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_id','product_variant_id','image_path','alt_text','is_primary','sort_order'])]
class ProductImage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer'
        ];
    }

    #[Scope]
    protected function primary(Builder $builder)
    {
        $builder->where('is_primary', true);
    }

    //Relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    //Helper Method
    public function getUrlAttribute(): string
    {
        return asset('storage/' .  $this->image_path);
    }
}