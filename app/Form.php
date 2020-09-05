<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ["auth_key", "host", "title"];

    public $timestamps = false;

    public function table(){
        return $this->belongsTo(Table::class);
    }
}