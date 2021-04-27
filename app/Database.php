<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Database extends Model
{
    protected $fillable = [
        "name", "name", "description", "key"
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tables(){
        return $this->hasMany(Table::class);
    }
}
