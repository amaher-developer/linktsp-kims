<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.edit_branch') }}: {{ $branch->name_en }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="text-sm text-green-600">{{ __('admin.saved') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.branches.update', $branch) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('admin::admin.branches._form')

                    <h3 class="text-md font-medium text-gray-900 pt-4">{{ __('admin.opening_hours') }}</h3>
                    @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                    <div class="space-y-2">
                        @foreach ($days as $i => $day)
                            @php $hour = $branch->hours->firstWhere('day_of_week', $i); @endphp
                            <div class="grid grid-cols-4 gap-2 items-center text-sm">
                                <span class="text-gray-700">{{ __($day) }}</span>
                                <input type="time" name="hours[{{ $i }}][open_time]" value="{{ $hour?->open_time }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <input type="time" name="hours[{{ $i }}][close_time]" value="{{ $hour?->close_time }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="hours[{{ $i }}][is_closed]" value="1" class="rounded border-gray-300" @checked($hour?->is_closed)>
                                    <span class="ms-2 text-gray-600">{{ __('admin.closed') }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <x-primary-button>{{ __('admin.save_branch') }}</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" onsubmit="return confirm('{{ __('admin.delete_this_branch') }}');">
                @csrf
                @method('DELETE')
                <x-danger-button>{{ __('admin.delete_branch') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
