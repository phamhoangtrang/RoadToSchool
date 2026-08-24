<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = ['title', 'vi_title', 'parent_id', 'css_classes'];

    protected $casts = ['parent_id' => 'integer'];

    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }

    public function getAllCategory()
    {
        return Category::all();
    }

    public function getParentCategoryById($id)
    {
        $parentCategory = Category::findOrFail($id);

        return $parentCategory;
    }
}
