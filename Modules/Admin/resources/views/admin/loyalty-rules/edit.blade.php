<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.edit_loyalty_rule') }}: {{ $rule->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.loyalty-rules.update', $rule) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('admin::admin.loyalty-rules._form')
                    <x-primary-button>{{ __('admin.save_rule') }}</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.loyalty-rules.destroy', $rule) }}" onsubmit="return confirm('{{ __('admin.delete_this_rule') }}');">
                @csrf
                @method('DELETE')
                <x-danger-button>{{ __('admin.delete_rule') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
