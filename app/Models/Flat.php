<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flat extends Model
{
    use HasFactory;

    protected $fillable = ['house_id', 'address', 'available_date', 'floor_number', 'flat_number', 'num_bedrooms', 'num_bathrooms', 'square_footage', 'rent', 'availability', 'status', 'created_at', 'updated_at', 'deleted_at'];

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


    public function house()
    {
        return $this->belongsTo(House::class, "house_id", "id");
    }

    public function houses()
    {
        return $this->hasMany(House::class, "house_id", "id");
    }

    public function getsquareFootageAttribute($value)
    {
        $images = explode(',', $value);
        $imageHtml = '';
        foreach ($images as $image) {
            $imageHtml .= '<img src="' . env('APP_URL') . '/public/storage/' . $image . '" height="50" width="50"/>';
        }
        return $imageHtml;
    }

    public function flatImages()
    {
        return $this->hasMany(FlatImage::class);
    }
}
