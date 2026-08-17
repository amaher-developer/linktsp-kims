<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.order_2') }} {{ $order->order_number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="text-sm text-green-600">{{ __('admin.updated') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">{{ __('admin.branch') }}:</span> {{ $order->branch->name_en }}</div>
                <div><span class="text-gray-500">{{ __('admin.customer') }}:</span> {{ $order->customer->first_name }} {{ $order->customer->last_name }}</div>
                <div><span class="text-gray-500">{{ __('admin.type') }}:</span> {{ $order->order_type }}</div>
                <div><span class="text-gray-500">{{ __('admin.placed') }}:</span> {{ $order->placed_at->format('Y-m-d H:i') }}</div>
                <div><span class="text-gray-500">{{ __('admin.subtotal') }}:</span> {{ number_format($order->subtotal, 2) }}</div>
                <div><span class="text-gray-500">{{ __('admin.tax') }}:</span> {{ number_format($order->tax_amount, 2) }}</div>
                <div><span class="text-gray-500">{{ __('admin.total') }}:</span> {{ number_format($order->total_amount, 2) }}</div>
                <div><span class="text-gray-500">{{ __('admin.status') }}:</span> {{ $order->status->value }}</div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('admin.update_status') }}</h3>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PUT')
                    <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($order->status === $status)>{{ ucfirst($status->value) }}</option>
                        @endforeach
                    </select>
                    <x-primary-button>{{ __('admin.update') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('admin.items') }}</h3>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.product') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.qty') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.unit_price') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-2 pe-4">
                                    {{ $item->product_name_en }}
                                    @if ($item->options->isNotEmpty())
                                        <div class="text-xs text-gray-500">
                                            @foreach ($item->options as $option)
                                                {{ $option->option_group_name_en }}: {{ $option->option_name_en }}@if (!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2 pe-4">{{ $item->quantity }}</td>
                                <td class="py-2 pe-4">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-2 pe-4">{{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('admin.status_history') }}</h3>
                <ul class="text-sm space-y-1">
                    @forelse ($order->statusHistory as $history)
                        <li>{{ $history->created_at->format('Y-m-d H:i') }} — {{ $history->from_status ?? '—' }} → {{ $history->to_status }}</li>
                    @empty
                        <li class="text-gray-500">{{ __('admin.no_history_yet') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
