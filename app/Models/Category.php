<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }

    protected $fillable = ['title', 'vi_title', 'parent_id', 'css_classes'];

    /**
     * Tạo danh mục mới. Không chọn mục cha thì là danh mục gốc (parent_id = 0).
     */
    public function createCategory($data)
    {
        $title = trim($data['title']);

        return Category::create([
            'title' => $title,
            'vi_title' => $title,
            'parent_id' => (int)($data['parent_id'] ?? 0),
        ]);
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
