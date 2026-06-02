<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name','slug','description','image','is_active','sort_order','meta_title','meta_description'])]
class Category extends Model
{
    use HasFactory;

    #[Scope]
    protected function active(Builder $builder)
    {
        $builder->where('is_active', true);
    }

    #[Scope]
    protected function sorted(Builder $builder)
    {
        $builder->orderby('sort_order', 'asc');
    }

    //Relationship
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function($category) {
            if ($category->isDirty('name') && empty($category->empty)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}