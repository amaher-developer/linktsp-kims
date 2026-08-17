<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Catalog\Models\Branch;

class BranchController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.branches.index', [
            'branches' => Branch::orderBy('name_en')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin::admin.branches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $branch = Branch::create($data);

        $this->syncHours($branch, $request);

        return redirect()->route('admin.branches.edit', $branch)->with('status', 'branch-saved');
    }

    public function edit(Branch $branch): View
    {
        $branch->load('hours');

        return view('admin::admin.branches.edit', ['branch' => $branch]);
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $branch->update($this->validated($request, $branch));

        $this->syncHours($branch, $request);

        return redirect()->route('admin.branches.edit', $branch)->with('status', 'branch-saved');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()->route('admin.branches.index')->with('status', 'branch-deleted');
    }

    private function validated(Request $request, ?Branch $branch = null): array
    {
        return $request->validate([
            'foodics_id' => ['required', 'integer'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'phone' => ['nullable', 'string', 'max:50'],
            'accepts_grab_go' => ['sometimes', 'boolean'],
            'accepts_dine_in' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    private function syncHours(Branch $branch, Request $request): void
    {
        if (! $request->has('hours')) {
            return;
        }

        foreach ($request->input('hours', []) as $day => $hour) {
            $branch->hours()->updateOrCreate(
                ['day_of_week' => $day],
                [
                    'open_time' => $hour['open_time'] ?: null,
                    'close_time' => $hour['close_time'] ?: null,
                    'is_closed' => isset($hour['is_closed']),
                ]
            );
        }
    }
}
