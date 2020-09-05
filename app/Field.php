<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property mixed required
 * @property mixed type
 * @property mixed minimum
 * @property mixed maximum
 * @property mixed data_in
 * @property mixed data_not_in
 */

class Field extends Model
{
    protected $fillable = [
        "name",
        "type",
        "required",
        "default",
        "minimum",
        "maximum",
        "data_in",
        "unique",
        "data_not_in",
        "slug"
    ];

    public function table(){
        return $this->belongsTo(Table::class);
    }

    public function data(){
        return $this->hasMany(Data::class);
    }

    public function validations(){
        $validations = [];
        $this->required === true ? $validations[] = "required" : false;
        $validations[] =  $this->type;
        $validations[] =  "min:".$this->minimum;
        $validations[] =  "max:".$this->maximum;

        isset($this->data_in) && sizeof($this->data_in) ? $validations[] = "in:".implode(",",$this->data_in) : false;
        isset($this->data_not_in) && sizeof($this->data_not_in) > 0 ? $validations[] = "not_in:".implode(",",$this->data_not_in) : false;

        return $validations;
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