<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = ['user_id','product_id','total_price','order_number','product_name','discount_price',"name","email","phone","zip","address_line",
    "city",
    "same_as_shipping",
    "billing_name",
    "billing_zip",
    "billing_city",
    "billing_address_line",
    "company_name",
    "tax_id"
    
    ];
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted()
{
    static::created(function ($order) {
        $order->order_number = 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $order->save();
    });
}
      
}
