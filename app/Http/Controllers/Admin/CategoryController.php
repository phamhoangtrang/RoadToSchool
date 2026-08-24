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
        $parentCategories = Category::where('parent_id', 0)->pluck('title', 'id');
        $parentCategories[0] = 'No choice';

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
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:categories,title'],
            'parent_id' => ['required', 'integer', 'min:0'],
        ]);

        $this->ensureParentExists((int) $data['parent_id']);
        $data['vi_title'] = $data['title'];
        $result = $this->modelCategory->create($data);

        if ($result) {
            flash(__('create status') . $result->id)->success();
        } else {
            flash(__('something wrong'))->error();
        }

        return redirect()->route('admin.categories.index');
    }

    public function edit($id)
    {
        $category = $this->modelCategory->findOrFail($id);
        $parentCategories = Category::where('parent_id', 0)
            ->whereKeyNot($category->id)
            ->pluck('title', 'id');
        $parentCategories[0] = 'No choice';

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = $this->modelCategory->findOrFail($id);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:categories,title,'.$category->id],
            'parent_id' => ['required', 'integer', 'min:0'],
        ]);

        $this->ensureParentExists((int) $data['parent_id']);
        $category->update($data);
        flash(__('update status').$category->id)->success();

        return redirect()->route('admin.categories.index');
    }

    public function destroy($id)
    {
        $category = $this->modelCategory->findOrFail($id);

        if ($category->courses()->exists() || Category::where('parent_id', $category->id)->exists()) {
            flash('A category with courses or child categories cannot be deleted.')->error();

            return redirect()->route('admin.categories.index');
        }

        $category->delete();
        flash(__('delete status').$category->id)->success();

        return redirect()->route('admin.categories.index');
    }

    private function ensureParentExists(int $parentId): void
    {
        if ($parentId !== 0) {
            Category::where('parent_id', 0)->findOrFail($parentId);
        }
    }
}
