<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Api extends Model
{

    protected $fillable = [
        "header_key",
        "slug",
        "can_read",
        "can_write",
        "can_delete",
        "active"
    ];

    public $timestamps = false;
    
    public function table(){
        return $this->belongsTo(Table::class);
    }
}