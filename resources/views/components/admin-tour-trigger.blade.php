@auth
    <button
        type="button"
        onclick="window.startAdminTour?.()"
        class="fi-topbar-item-button flex items-center justify-center gap-x-2 rounded-lg p-2 text-gray-500 outline-none transition duration-75 hover:bg-gray-50 hover:text-gray-700 focus-visible:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-200 dark:focus-visible:bg-white/5"
        aria-label="{{ __('Take a tour') }}"
        title="{{ __('Take a tour') }}"
    >
        <x-filament::icon icon="heroicon-o-light-bulb" class="h-5 w-5" />
    </button>
@endauth
