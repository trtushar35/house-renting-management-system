<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['house_id','room_number','type','rent','availability', 'status','created_at','updated_at','deleted_at'];

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
                    return $this->belongsTo(House::class,"house_id","id");
                }

                public function houses()
                {
                    return $this->hasMany(House::class,"house_id","id");
                }
}
