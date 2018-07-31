<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public function user() {
        return $this->hasOne('App\User');
    }
    public function build() {
        return $this->belongsTo('App\Build');
    }
}
