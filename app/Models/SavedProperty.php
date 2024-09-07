<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedProperty extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'owner_id', 'flat_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

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


    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function houseOwner()
    {
        return $this->belongsTo(HouseOwner::class, 'owner_id', 'id');
    }

    public function flat()
    {
        return $this->belongsTo(Flat::class, 'flat_id', 'id');
    }

    public function house()
    {
        return $this->belongsTo(House::class, 'house_id', 'id');
    }

    public function flatImages()
    {
        return $this->hasMany(FlatImage::class);
    }

    public function getsquareFootageAttribute($value)
    {
        return '<img src="' . asset('/public/flatImage/' . $value) . '" height="100" width="100"/>';
    }

}
