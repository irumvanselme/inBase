<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property mixed fields
 * @property mixed __data_counter
 * @property mixed published
 * @property mixed forms
 * @property mixed api
 * @property mixed entries
 */
class Table extends Model
{
    protected $fillable = [
        "name",
        "published",
        "description",
        "__data_counter"
    ];

    public $timestamps = false;

    protected $hidden = ["__data_counter"];

    public function fields(){
        return $this->hasMany(Field::class);
    }

    public function entries(){
        return $this->hasMany(Entry::class);
    }

    public function database(){
        return $this->belongsTo(Database::class);
    }

    public function forms(){
        return $this->hasMany(Form::class);
    }

    public function api(){
        return $this->hasOne(Api::class);
    }

    public function __id(){
        $__id = $this->__data_counter + 1;
        $this->increment("__data_counter");
        return $__id;
    }
}