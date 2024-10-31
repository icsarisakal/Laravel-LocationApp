<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'lat',
        'long',
        'created_user_id',
        'marker_color',
    ];
}
