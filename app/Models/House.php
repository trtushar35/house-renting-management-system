<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = ['house_owner_id', 'division', 'house_name', 'house_number', 'address', 'city', 'country', 'status', 'created_at', 'updated_at', 'deleted_at'];

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


    public function houseOwner()
    {
        return $this->belongsTo(HouseOwner::class, "house_owner_id", "id");
    }

    public function houseOwners()
    {
        return $this->hasMany(HouseOwner::class, "house_owner_id", "id");
    }
}
