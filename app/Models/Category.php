<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Category extends Model
{
    protected $fillable = ['name','slug','photo','is_featured','image','meta_keys', 'meta_description'];
    public $timestamps = false;

    public function subs()
    {
    	return $this->hasMany('App\Models\Subcategory')->where('status','=',1);
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->where('status','=',1);
    }

    public static function top_products()
    {
        $db = DB::select('SELECT categories.*, products.ordered_count from categories, products where categories.is_featured = 1  and products.status = 1 and  categories.id = products.category_id   GROUP By categories.id ORDER BY products.ordered_count DESC LIMIT 8');

        return $db;
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_replace(' ', '-', $value);
    }

    public function attributes() {
        return $this->morphMany('App\Models\Attribute', 'attributable');
    }
}
