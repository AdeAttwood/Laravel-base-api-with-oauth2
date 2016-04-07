<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Oauth2;

class People extends Model
{
    public static function all($columns = ['*'])
    {

        return parent::all($columns);
    }

 

    public function comments()
    {
        return $this->hasMany('App\Comments', 'userID', 'id');
    }
}
