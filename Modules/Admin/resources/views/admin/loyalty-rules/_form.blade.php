@php $rule = $rule ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <x-input-label for="name" :value="__('admin.name')" />
        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $rule?->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="priority" :value="__('admin.priority')" />
        <x-text-input id="priority" name="priority" type="number" class="block mt-1 w-full" :value="old('priority', $rule?->priority ?? 0)" required />
    </div>
    <div></div>
    <div>
        <x-input-label for="earn_points_rate" :value="__('admin.earn_points_rate')" />
        <x-text-input id="earn_points_rate" name="earn_points_rate" type="number" step="0.0001" class="block mt-1 w-full" :value="old('earn_points_rate', $rule?->earn_points_rate)" required />
    </div>
    <div>
        <x-input-label for="earn_amount_unit" :value="__('admin.earn_amount_unit')" />
        <x-text-input id="earn_amount_unit" name="earn_amount_unit" type="number" step="0.0001" class="block mt-1 w-full" :value="old('earn_amount_unit', $rule?->earn_amount_unit ?? 1)" required />
    </div>
    <div>
        <x-input-label for="redeem_points_unit" :value="__('admin.redeem_points_unit')" />
        <x-text-input id="redeem_points_unit" name="redeem_points_unit" type="number" step="0.0001" class="block mt-1 w-full" :value="old('redeem_points_unit', $rule?->redeem_points_unit)" required />
    </div>
    <div>
        <x-input-label for="redeem_value" :value="__('admin.redeem_value')" />
        <x-text-input id="redeem_value" name="redeem_value" type="number" step="0.0001" class="block mt-1 w-full" :value="old('redeem_value', $rule?->redeem_value)" required />
    </div>
    <div>
        <x-input-label for="minimum_redeem_points" :value="__('admin.minimum_redeem_points')" />
        <x-text-input id="minimum_redeem_points" name="minimum_redeem_points" type="number" class="block mt-1 w-full" :value="old('minimum_redeem_points', $rule?->minimum_redeem_points ?? 0)" required />
    </div>
    <div>
        <x-input-label for="starts_at" :value="__('admin.starts_at')" />
        <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="block mt-1 w-full" :value="old('starts_at', $rule?->starts_at?->format('Y-m-d\TH:i'))" />
    </div>
    <div>
        <x-input-label for="ends_at" :value="__('admin.ends_at')" />
        <x-text-input id="ends_at" name="ends_at" type="datetime-local" class="block mt-1 w-full" :value="old('ends_at', $rule?->ends_at?->format('Y-m-d\TH:i'))" />
    </div>
</div>

<label class="inline-flex items-center pt-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $rule?->is_active ?? true))>
    <span class="ms-2 text-sm text-gray-700">{{ __('admin.active') }}</span>
</label>
