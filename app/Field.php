<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        "name",
        "validations"
    ];

    public function table(){
        return $this->belongsTo(Table::class);
    }

    public function data(){
        return $this->hasMany(Data::class);
    }

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model){
            $model->slug = strtolower(str_replace(' ', '_', $model->name));
        });

        self::creating(function ($model){
            $model->slug = strtolower(str_replace(' ', '_', $model->name));
        });
    }
}