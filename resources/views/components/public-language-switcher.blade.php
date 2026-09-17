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
