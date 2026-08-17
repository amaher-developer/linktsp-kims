<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.foodics_integration') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">{{ __('admin.saved') }}</div>
            @endif
            <div class="bg-white shadow sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('admin.credentials_are_stored_encrypted_and') }}
                </p>
                <form method="POST" action="{{ route('admin.integrations.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="name" :value="__('admin.integration_name')" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $integration->name)" required />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('admin.status')" />
                        <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                            <option value="active" @selected($integration->status === 'active')>{{ __('admin.active') }}</option>
                            <option value="inactive" @selected($integration->status === 'inactive')>{{ __('admin.inactive') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="client_id" :value="__('admin.foodics_client_id')" />
                        <x-text-input id="client_id" name="client_id" type="text" class="block mt-1 w-full" placeholder="{{ __('admin.leave_blank_to_keep_current_value') }}" />
                    </div>
                    <div>
                        <x-input-label for="client_secret" :value="__('admin.foodics_client_secret')" />
                        <x-text-input id="client_secret" name="client_secret" type="password" class="block mt-1 w-full" autocomplete="new-password" placeholder="{{ __('admin.leave_blank_to_keep_current_value') }}" />
                    </div>
                    <x-primary-button>{{ __('admin.save_integration') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
