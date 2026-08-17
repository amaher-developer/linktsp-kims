<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Staff\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.roles.index', ['roles' => Role::withCount('staff')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin::admin.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Role::create($this->validated($request));

        return redirect()->route('admin.roles.index')->with('status', 'saved');
    }

    public function edit(Role $role): View
    {
        return view('admin::admin.roles.edit', ['role' => $role]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $role->update($this->validated($request, $role));

        return redirect()->route('admin.roles.index')->with('status', 'saved');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->staff()->exists()) {
            return back()->withErrors(['role' => __('Cannot delete a role that still has staff assigned.')]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', 'deleted');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:kims_roles,name,'.($role?->id ?? 'NULL').',id'],
        ]);
    }
}
