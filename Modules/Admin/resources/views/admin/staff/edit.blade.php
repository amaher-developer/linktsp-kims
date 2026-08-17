<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.edit_staff') }}: {{ $staffMember->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="text-sm text-green-600">{{ __('admin.saved') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.staff.update', $staffMember) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('admin::admin.staff._form')
                    <x-primary-button>{{ __('admin.save_staff') }}</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.staff.destroy', $staffMember) }}" onsubmit="return confirm('{{ __('admin.delete_this_staff_member') }}');">
                @csrf
                @method('DELETE')
                <x-danger-button>{{ __('admin.delete_staff') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
