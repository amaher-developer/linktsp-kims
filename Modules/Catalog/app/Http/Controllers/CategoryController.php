<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Catalog\Http\Resources\CategoryResource;
use Modules\Catalog\Models\Category;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            Category::where('is_active', true)->orderBy('sort_order')->get()
        );
    }
}
