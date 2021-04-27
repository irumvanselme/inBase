<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property mixed name
 * @property mixed description
 * @property mixed __data_counter
 * @property mixed fields
 * @property mixed entries
 */

class Table extends Model
{
    protected $fillable = [
        "name",
        "description",
        "__data_counter"
    ];

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