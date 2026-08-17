@php $staffMember = $staffMember ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" :value="__('admin.name')" />
        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $staffMember?->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="role_id" :value="__('admin.role')" />
        <select id="role_id" name="role_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $staffMember?->role_id) == $role->id)>{{ ucfirst($role->name) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="email" :value="__('admin.email')" />
        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $staffMember?->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>
    <div>
        <x-input-label for="phone" :value="__('admin.phone')" />
        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $staffMember?->phone)" />
    </div>
    <div>
        <x-input-label for="password" :value="$staffMember ? __('admin.new_password_leave_blank_to_keep_current') : __('admin.password')" />
        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" :required="! $staffMember" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>
</div>

<div class="pt-2">
    <x-input-label :value="__('admin.branches')" />
    <div class="space-y-1 mt-1">
        @foreach ($branches as $branch)
            <label class="inline-flex items-center text-sm me-4">
                <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" class="rounded border-gray-300" @checked($staffMember?->branches->contains('id', $branch->id))>
                <span class="ms-2 text-gray-700">{{ $branch->name_en }}</span>
            </label>
        @endforeach
    </div>
</div>

<label class="inline-flex items-center pt-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $staffMember?->is_active ?? true))>
    <span class="ms-2 text-sm text-gray-700">{{ __('admin.active') }}</span>
</label>
