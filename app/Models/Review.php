<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_id','customer_id','order_id','rating','title','comment','is_verified_purchase','is_approved'])]
class Review extends Model
{
    use HasFactory;

    protected function casts()
    {
        return [
            'rating' => 'integer',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean'
        ];
    }

    #[Scope]
    protected function approved(Builder $builder)
    {
        $builder->where('is_approved', true);
    }

    #[Scope]
    protected function verified(Builder $builder)
    {
        $builder->where('is_verified_purchase', true);
    }

    #[Scope]
    protected function rating(Builder $builder, int $rating)
    {
        $builder->where('rating', $rating);
    }

    //Relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}