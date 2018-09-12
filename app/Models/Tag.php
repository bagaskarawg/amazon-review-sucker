<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'slug'];

    public function reviews()
    {
        return $this->morphedByMany('App\Models\Review', 'taggable');
    }

    public function scopeContaining(Builder $query, string $name, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $query->whereRaw('LOWER(JSON_EXTRACT(name, "$.' . $locale .'")) like ?', ['"%' . strtolower($name) . '%"']);
    }
}
