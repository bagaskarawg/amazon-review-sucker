<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public static $states = [ 'draft', 'in_progress', 'completed' ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
