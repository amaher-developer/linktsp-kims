<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Catalog\Models\Option;
use Modules\Catalog\Models\OptionGroup;

class OptionController extends Controller
{
    public function store(Request $request, OptionGroup $optionGroup): RedirectResponse
    {
        $data = $this->validated($request);
        $data['option_group_id'] = $optionGroup->id;

        Option::create($data);

        return redirect()->route('admin.option-groups.edit', $optionGroup)->with('status', 'saved');
    }

    public function update(Request $request, OptionGroup $optionGroup, Option $option): RedirectResponse
    {
        $option->update($this->validated($request));

        return redirect()->route('admin.option-groups.edit', $optionGroup)->with('status', 'saved');
    }

    public function destroy(OptionGroup $optionGroup, Option $option): RedirectResponse
    {
        $option->delete();

        return redirect()->route('admin.option-groups.edit', $optionGroup)->with('status', 'deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'foodics_id' => ['nullable', 'integer'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'price_delta' => ['required', 'numeric'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
