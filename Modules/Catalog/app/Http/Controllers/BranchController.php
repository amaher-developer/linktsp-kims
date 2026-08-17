<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Catalog\Http\Resources\BranchResource;
use Modules\Catalog\Models\Branch;

class BranchController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BranchResource::collection(
            Branch::where('is_active', true)->orderBy('name_en')->get()
        );
    }

    public function show(Branch $branch): BranchResource
    {
        abort_unless($branch->is_active, 404);

        return new BranchResource($branch->load('hours'));
    }
}
