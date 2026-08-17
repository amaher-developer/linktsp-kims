<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Catalog\Models\OptionGroup;

class OptionGroupController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.option-groups.index', [
            'optionGroups' => OptionGroup::withCount('options')->orderBy('name_en')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin::admin.option-groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $group = OptionGroup::create($this->validated($request));

        return redirect()->route('admin.option-groups.edit', $group)->with('status', 'saved');
    }

    public function edit(OptionGroup $optionGroup): View
    {
        $optionGroup->load('options');

        return view('admin::admin.option-groups.edit', ['optionGroup' => $optionGroup]);
    }

    public function update(Request $request, OptionGroup $optionGroup): RedirectResponse
    {
        $optionGroup->update($this->validated($request));

        return redirect()->route('admin.option-groups.edit', $optionGroup)->with('status', 'saved');
    }

    public function destroy(OptionGroup $optionGroup): RedirectResponse
    {
        $optionGroup->delete();

        return redirect()->route('admin.option-groups.index')->with('status', 'deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'foodics_id' => ['nullable', 'integer'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'min_select' => ['required', 'integer', 'min:0'],
            'max_select' => ['required', 'integer', 'min:1'],
            'is_required' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
