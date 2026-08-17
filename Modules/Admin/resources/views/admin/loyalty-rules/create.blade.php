<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.new_loyalty_rule') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.loyalty-rules.store') }}" class="space-y-4">
                    @csrf
                    @include('admin::admin.loyalty-rules._form')
                    <x-primary-button>{{ __('admin.create_rule') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
