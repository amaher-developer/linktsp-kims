<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\Catalog\Models\Branch;
use Modules\Staff\Models\Role;
use Modules\Staff\Models\Staff;

class StaffController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.staff.index', ['staff' => Staff::with('role')->orderBy('name')->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin::admin.staff.create', ['roles' => Role::orderBy('name')->get(), 'branches' => Branch::orderBy('name_en')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($request->validate(['password' => ['required', 'string', 'min:8']])['password']);

        $staff = Staff::create($data);
        $staff->branches()->sync($request->input('branch_ids', []));

        return redirect()->route('admin.staff.edit', $staff)->with('status', 'saved');
    }

    public function edit(Staff $staffMember): View
    {
        $staffMember->load('branches');

        return view('admin::admin.staff.edit', [
            'staffMember' => $staffMember,
            'roles' => Role::orderBy('name')->get(),
            'branches' => Branch::orderBy('name_en')->get(),
        ]);
    }

    public function update(Request $request, Staff $staffMember): RedirectResponse
    {
        $data = $this->validated($request, $staffMember);

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:8']]);
            $data['password'] = Hash::make($request->string('password'));
        }

        $staffMember->update($data);
        $staffMember->branches()->sync($request->input('branch_ids', []));

        return redirect()->route('admin.staff.edit', $staffMember)->with('status', 'saved');
    }

    public function destroy(Staff $staffMember): RedirectResponse
    {
        $staffMember->delete();

        return redirect()->route('admin.staff.index')->with('status', 'deleted');
    }

    private function validated(Request $request, ?Staff $staff = null): array
    {
        return $request->validate([
            'role_id' => ['required', 'exists:kims_roles,id'],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150', 'unique:kims_staff,email,'.($staff?->id ?? 'NULL').',id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
