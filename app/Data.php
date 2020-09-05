<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Data extends Model
{
    protected $fillable = [
        "data", "field_id"
    ];

    public $timestamps = false;

    public function entry(){
        return $this->belongsTo(Entry::class);
    }
}
