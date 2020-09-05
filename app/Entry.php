<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property mixed data
 * @property mixed table
 */
class Entry extends Model
{
    protected $fillable = [
        "id", "user_id"
    ];

    public function data(){
        return $this->hasMany(Data::class);
    }

    public function table(){
        return $this->belongsTo(Table::class);
    }
}
