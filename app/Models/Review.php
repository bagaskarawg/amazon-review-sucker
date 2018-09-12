<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use \Spatie\Tags\HasTags;

    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
