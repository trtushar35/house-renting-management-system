<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'payment_date', 'payment_month', 'transaction_id', 'amount', 'paid_amount', 'due', 'payment_method', 'status', 'created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_at = now();
        });

        static::updating(function ($model) {
            $model->updated_at = now();
        });
    }


    public function booking()
    {
        return $this->belongsTo(Booking::class, "booking_id", "id");
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, "booking_id", "id");
    }
}
