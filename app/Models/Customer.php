<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name','email','email_verified_at','password','phone','date_of_birth','gender','is_active','remember_token'])]
#[Hidden(['password','remember_token'])]
class Customer extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean'
        ];
    }

    #[Scope]
    protected function active(Builder $builder)
    {
        $builder->where('is_active', true);
    }

    //Relationship
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    //Helper Methods
    public function getTotalSpenAttribute()
    {
        return $this->orders()->where('payment_status', 'paid')->sun('total');
    }

    public function getOrdersCountAttribute()
    {
        return $this->orders()->count();
    }
}