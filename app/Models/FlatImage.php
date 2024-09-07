<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlatImage extends Model
{
    use HasFactory;

    protected $fillable = ['flat_id', 'square_footage', 'status', 'created_at', 'updated_at', 'deleted_at'];

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

    public function getsquareFootageAttribute($value)
    {
        return '<img src="' . asset('/public/flatImage/' . $value) . '" height="100" width="100"/>';
    }
}
