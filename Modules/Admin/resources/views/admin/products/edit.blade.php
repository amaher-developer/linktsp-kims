<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.edit_product') }}: {{ $product->name_en }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="text-sm text-green-600">{{ __('admin.saved') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('admin::admin.products._form')

                    <h3 class="text-md font-medium text-gray-900 pt-4">{{ __('admin.branch_availability_price_overrides') }}</h3>
                    <div class="space-y-2">
                        @foreach ($branches as $branch)
                            @php $pivot = $product->branches->firstWhere('id', $branch->id)?->pivot; @endphp
                            <div class="grid grid-cols-3 gap-2 items-center text-sm">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="branch_available[{{ $branch->id }}]" value="1" class="rounded border-gray-300" @checked($pivot)>
                                    <span class="ms-2 text-gray-700">{{ $branch->name_en }}</span>
                                </label>
                                <input type="number" step="0.01" name="branch_price[{{ $branch->id }}]" value="{{ $pivot?->price_override }}" placeholder="{{ __('admin.price_override') }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                        @endforeach
                    </div>

                    <h3 class="text-md font-medium text-gray-900 pt-4">{{ __('admin.option_groups') }}</h3>
                    <div class="space-y-1">
                        @foreach ($optionGroups as $group)
                            <label class="inline-flex items-center text-sm">
                                <input type="checkbox" name="option_groups[]" value="{{ $group->id }}" class="rounded border-gray-300" @checked($product->optionGroups->contains('id', $group->id))>
                                <span class="ms-2 text-gray-700">{{ $group->name_en }}</span>
                            </label><br>
                        @endforeach
                    </div>

                    <x-primary-button>{{ __('admin.save_product') }}</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('{{ __('admin.delete_this_product') }}');">
                @csrf
                @method('DELETE')
                <x-danger-button>{{ __('admin.delete_product') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
