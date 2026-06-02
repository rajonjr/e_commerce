<?php

namespace App\Models;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'order_number',
        'customer_id',
        'coupon_id',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'tax_amount',
        'total',
        'shipping_full_name',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'payment_method',
        'payment_status',
        'transaction_id',
        'status',
        'tracking_number',
        'customer_notes',
        'admin_notes'
])]
class Order extends Model
{
    use SoftDeletes;

    #[Scope]
    protected function ofStatus(Builder $builder)
    {
        $builder->where('status', true);
    }

    #[Scope]
    protected function paymentStatus(Builder $builder, string $status)
    {
        $builder->where('payment_status', $status);
    }

    #[Scope]
    protected function pending(Builder $builder)
    {
        $builder->where('status', 'pending');
    }

    #[Scope]
    protected function processing(Builder $builder)
    {
        $builder->where('status', 'processing');
    }

    #[Scope]
    protected function stripped(Builder $builder)
    {
        $builder->where('status', 'stripped');
    }

    #[Scope]
    protected function delivered(Builder $builder)
    {
        $builder->where('status', 'delivered');
    }

    //Relationship
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    //Helper Method
    public function getShippingAddressAttribute()
    {
        return implode(',', array_filter([
            $this->shipping_address_line_1,
            $this->shipping_address_line_2,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code,
            $this->shipping_country
        ]));
    }

    public function updateStatus($newStatus, $notes = null, $userId = null)
    {
        $this->update(['status' => $newStatus]);

        $this->sstatusStories()->create([
            'status' => $newStatus,
            'notes' => $notes,
            'user_id' => $userId
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($this->order_number)) {
                $this->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });

        static::created(function ($order) {
            $order->statusHistories()->create([
                'status' => $order->status,
                'notes' => 'Order created'
            ]);
            //Order Confirmation Email
        });
    }
}