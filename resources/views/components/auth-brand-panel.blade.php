@php
    $appName = \App\Models\Setting::get('app_name', '3D Model Studio');
    $tagline = \App\Models\Setting::get('app_tagline', 'Create and customize interactive 3D models.');
@endphp
<div class="flex flex-col items-center justify-center gap-5 px-8 py-10 text-center">
    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-500/15 text-3xl text-primary-300">◈</div>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">{{ $appName }}</h1>
        <p class="mt-2 max-w-xs text-sm text-gray-400">{{ $tagline }}</p>
    </div>
</div>
