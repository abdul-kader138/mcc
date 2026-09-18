@if($inline ?? false)
    <form method="POST" action="{{ route('locale.update') }}" class="relative shrink-0">
        @csrf
        <label for="public-locale" class="sr-only">{{ __('Language') }}</label>
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/></svg>
        <select id="public-locale" name="locale" onchange="this.form.submit()" class="h-11 cursor-pointer appearance-none rounded-xl border border-white/10 bg-[#11151c] pl-9 pr-8 text-sm text-slate-300 transition [color-scheme:dark] hover:border-white/25 hover:bg-[#191e27] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300 focus-visible:ring-offset-2 focus-visible:ring-offset-[#080a0f]">
            <option value="en" @selected(app()->getLocale() === 'en')>English</option>
            <option value="fr" @selected(app()->getLocale() === 'fr')>Français</option>
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 h-3 w-3 -translate-y-1/2 text-slate-500" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m4 6 4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </form>
@else
<div class="fixed right-4 top-4 z-50">
    <form method="POST" action="{{ route('locale.update') }}" class="flex items-center gap-2">
        @csrf
        <label for="public-locale" class="sr-only">{{ __('Language') }}</label>
        <span class="text-sm text-slate-500">◎</span>
        <select id="public-locale" name="locale" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200 outline-none transition hover:border-cyan-300/40 focus:border-cyan-300/60">
            <option value="en" @selected(app()->getLocale() === 'en')>English</option>
            <option value="fr" @selected(app()->getLocale() === 'fr')>Français</option>
        </select>
    </form>
</div>
@endif
