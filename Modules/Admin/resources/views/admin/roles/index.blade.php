<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.roles') }}</h2>
            <a href="{{ route('admin.roles.create') }}" class="text-sm text-indigo-600 hover:underline">{{ __('admin.new_role_2') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600">{{ $errors->first() }}</div>
                @endif
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.name') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.staff') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($roles as $role)
                            <tr>
                                <td class="py-2 pe-4">{{ ucfirst($role->name) }}</td>
                                <td class="py-2 pe-4">{{ $role->staff_count }}</td>
                                <td class="py-2 pe-4 text-end space-x-3 rtl:space-x-reverse">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.roles.edit', $role) }}">{{ __('admin.edit') }}</a>
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('{{ __('admin.delete_this_role') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">{{ __('admin.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
