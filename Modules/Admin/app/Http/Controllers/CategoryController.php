<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Catalog\Models\Category;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.categories.index', [
            'categories' => Category::with('parent')->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin::admin.categories.create', ['categories' => Category::orderBy('name_en')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('status', 'category-saved');
    }

    public function edit(Category $category): View
    {
        return view('admin::admin.categories.edit', [
            'category' => $category,
            'categories' => Category::where('id', '!=', $category->id)->orderBy('name_en')->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request));

        return redirect()->route('admin.categories.index')->with('status', 'category-saved');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'category-deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'foodics_id' => ['required', 'integer'],
            'parent_id' => ['nullable', 'exists:kims_categories,id'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
