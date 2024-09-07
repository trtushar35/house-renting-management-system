<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['flat_id', 'room_id', 'tenant_id', 'owner_id', 'rent', 'booking_status', 'payment_status', 'status', 'created_at', 'updated_at', 'deleted_at'];

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


    public function flat()
    {
        return $this->belongsTo(Flat::class, "flat_id", "id");
    }

    public function flats()
    {
        return $this->hasMany(Flat::class, "flat_id", "id");
    }
    public function room()
    {
        return $this->belongsTo(Room::class, "room_id", "id");
    }

    public function house()
    {
        return $this->belongsTo(House::class, "house_id", "id");
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, "room_id", "id");
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, "tenant_id", "id");
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class, "tenant_id", "id");
    }
    public function houseOwner()
    {
        return $this->belongsTo(HouseOwner::class);
    }

    public function houseOwners()
    {
        return $this->hasMany(HouseOwner::class);
    }

    public function payments()
{
    return $this->hasMany(Payment::class);
}
}
