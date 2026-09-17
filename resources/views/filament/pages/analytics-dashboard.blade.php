<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Public model activity for the selected period.') }}</p>
            <select wire:model.live="period" wire:change="refreshStats" class="rounded-lg border-gray-300 bg-white text-sm dark:border-gray-700 dark:bg-gray-900">
                <option value="7">{{ __('Last 7 days') }}</option>
                <option value="30">{{ __('Last 30 days') }}</option>
                <option value="90">{{ __('Last 90 days') }}</option>
            </select>
        </div>
        <div class="grid gap-4 md:grid-cols-4">
            @foreach([['label' => __('Model views'), 'value' => $totalViews, 'color' => 'cyan'], ['label' => __('Unique visitors'), 'value' => $uniqueVisitors, 'color' => 'violet'], ['label' => __('Saved configurations'), 'value' => $savedConfigurations, 'color' => 'emerald'], ['label' => __('Quote requests'), 'value' => $quoteRequests, 'color' => 'amber']] as $stat)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ number_format($stat['value']) }}</p>
                </div>
            @endforeach
        </div>
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">{{ __('Top models') }}</h2>
                <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($topItems as $item)
                        <div class="flex items-center justify-between py-3 text-sm"><span class="text-gray-700 dark:text-gray-300">{{ $item['name'] }}</span><span class="font-semibold text-gray-950 dark:text-white">{{ number_format($item['total']) }}</span></div>
                    @empty
                        <p class="py-4 text-sm text-gray-500">{{ __('No model activity yet.') }}</p>
                    @endforelse
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">{{ __('Activity breakdown') }}</h2>
                <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($eventBreakdown as $event => $total)
                        <div class="flex items-center justify-between py-3 text-sm"><span class="text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', ucfirst($event)) }}</span><span class="font-semibold text-gray-950 dark:text-white">{{ number_format($total) }}</span></div>
                    @empty
                        <p class="py-4 text-sm text-gray-500">{{ __('No activity yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
