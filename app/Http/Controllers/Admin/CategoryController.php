<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    protected $modelCategory;

    /**
     * Create a new controller instance.
     *
     * @param Specialize $specialize
     * @return void
     */
    public function __construct(Category $category)
    {
        $this->modelCategory = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        $parentCategories = Category::all()->where('parent_id', 0)->pluck('title', 'id');
        $parentCategories[0] = 'Không chọn (danh mục gốc)';

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'parent_id' => ['nullable', 'integer', function ($attribute, $value, $fail) {
                // 0 = danh mục gốc; còn lại phải là một danh mục gốc đang tồn tại
                if ((int)$value !== 0 && !Category::where('id', $value)->where('parent_id', 0)->exists()) {
                    $fail('Danh mục cha không hợp lệ.');
                }
            }],
        ], [
            'title.required' => 'Vui lòng nhập tên danh mục.',
            'title.max' => 'Tên danh mục tối đa 191 ký tự.',
        ]);

        $result = $this->modelCategory->createCategory($request->all());

        if ($result) {
            flash('Đã thêm danh mục "' . $result->title . '" thành công.')->success();
        } else {
            flash('Không thể thêm danh mục, vui lòng thử lại.')->error();
        }

        return redirect(route('admin.categories.index'));
    }
}
