<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Model Studio · Interactive 3D model library</title>
    <meta name="description" content="Explore and customize interactive 3D models.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html{scroll-behavior:smooth}
        body{
            font-family:'Inter',ui-sans-serif,system-ui,-apple-system,sans-serif;
            background:#07080c;
            background-image:
                radial-gradient(ellipse 70% 50% at 50% -10%, rgba(34,211,238,.10), transparent 60%),
                radial-gradient(ellipse 55% 40% at 100% 0%, rgba(99,102,241,.08), transparent 60%);
        }
        .bg-grid{
            background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);
            background-size:48px 48px;
            mask-image:radial-gradient(ellipse 65% 60% at 50% 0%,#000,transparent 78%);
            -webkit-mask-image:radial-gradient(ellipse 65% 60% at 50% 0%,#000,transparent 78%);
        }
        .catalog-card{
            background:linear-gradient(155deg,rgba(255,255,255,.065),rgba(255,255,255,.015));
            box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 24px 60px -24px rgba(0,0,0,.55);
        }
        .catalog-card:hover{box-shadow:0 1px 0 rgba(255,255,255,.07) inset,0 30px 80px -20px rgba(34,211,238,.18)}
        .image-shine:after{
            content:"";position:absolute;inset:0;
            background:linear-gradient(115deg,transparent 20%,rgba(255,255,255,.14) 48%,transparent 70%);
            transform:translateX(-120%);transition:transform .7s ease;
        }
        .catalog-card:hover .image-shine:after{transform:translateX(120%)}
        .tag-chip.is-active{background:#22d3ee!important;color:#04141a!important;border-color:#22d3ee!important}
        #catalog-grid.view-list{grid-template-columns:1fr!important}
        #catalog-grid.view-list .catalog-card{display:grid;grid-template-columns:16rem 1fr}
        #catalog-grid.view-list .image-shine{aspect-ratio:auto}
        @media(max-width:640px){#catalog-grid.view-list .catalog-card{grid-template-columns:1fr}}
        #recently-viewed:empty{display:none}
        .view-toggle-btn.is-active{background:rgba(255,255,255,.14);color:#fff}
        .field-select{background-image:none}
        ::selection{background:#22d3ee;color:#04141a}
    </style>
</head>
<body class="min-h-screen bg-[#07080c] text-white antialiased">

    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="bg-grid absolute inset-0"></div>
        <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-20 h-[32rem] w-[32rem] rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>

    <header class="relative z-20 border-b border-white/10 bg-[#07080c]/70 backdrop-blur-xl">
        <div class="mx-auto flex min-h-[4.5rem] max-w-7xl flex-wrap items-center justify-between gap-x-6 gap-y-3 px-6 py-3 lg:px-12">
            <a href="{{ route('items.index') }}" class="flex shrink-0 items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300 focus-visible:ring-offset-4 focus-visible:ring-offset-[#07080c]">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-cyan-300/20 bg-gradient-to-br from-cyan-300/20 to-indigo-400/10 text-cyan-200" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z" stroke-linejoin="round"/><path d="M4 7l8 4 8-4M12 11v10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span class="font-semibold tracking-tight">Model Studio</span>
            </a>
            <div class="flex w-full flex-wrap items-center justify-end gap-2 sm:w-auto sm:gap-3">
                <a href="/admin" class="group inline-flex h-11 shrink-0 items-center gap-2 rounded-xl border border-cyan-300/20 bg-cyan-300/[.06] px-3 text-sm font-medium text-cyan-100 transition hover:border-cyan-300/40 hover:bg-cyan-300/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300 focus-visible:ring-offset-2 focus-visible:ring-offset-[#07080c] sm:px-4">
                    <svg class="h-4 w-4 text-cyan-300/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="3"/><path d="M3.5 9h17M9 9v11.5"/></svg>
                    <span>Admin workspace</span>
                    <svg class="h-3.5 w-3.5 text-cyan-300/60 transition group-hover:translate-x-0.5 group-hover:text-cyan-200" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 10h12m-5-5 5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <span class="hidden h-6 w-px shrink-0 bg-white/10 sm:block" aria-hidden="true"></span>
                @include('components.public-language-switcher', ['inline' => true])
            </div>
        </div>
    </header>

    <main class="relative mx-auto max-w-7xl px-6 py-14 lg:px-12 lg:py-20">

        <section class="max-w-3xl">
            <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/[.06] px-3 py-1 text-xs font-semibold uppercase tracking-[.3em] text-cyan-300">
                <span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>
                Public collection
            </p>
            <h1 class="text-5xl font-semibold leading-[1.05] tracking-[-.03em] sm:text-6xl">
                Explore every<br class="hidden sm:block"> angle.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-400">
                Browse the collection, open any model in an interactive 3D space, and make your own color and texture variations — right in the browser.
            </p>
            <div class="mt-7 flex flex-wrap items-center gap-2.5 text-xs text-slate-400">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[.03] px-3 py-1.5">
                    <svg class="h-3.5 w-3.5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z" stroke-linejoin="round"/></svg>
                    {{ number_format($items->total()) }} {{ $items->total() === 1 ? 'model' : 'models' }} listed
                </span>
                @if(count($categories))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[.03] px-3 py-1.5">
                    <svg class="h-3.5 w-3.5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                    {{ count($categories) }} {{ count($categories) === 1 ? 'category' : 'categories' }}
                </span>
                @endif
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[.03] px-3 py-1.5">
                    <svg class="h-3.5 w-3.5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M5 12.5 10 17l9-10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    No account required to browse
                </span>
            </div>
        </section>

        <section id="recently-viewed" class="mt-10"></section>

        <section id="catalog" class="mt-12 scroll-mt-24 rounded-3xl border border-white/10 bg-white/[.025] p-3 shadow-2xl shadow-black/20 backdrop-blur-sm md:p-4">
            <form method="GET" action="{{ route('items.index') }}" class="flex flex-col gap-3 md:flex-row">
                <input type="hidden" name="tag" value="{{ $tag }}">
                <label class="flex flex-1 items-center gap-3 rounded-2xl border border-white/10 bg-black/25 px-4 transition focus-within:border-cyan-300/50">
                    <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
                    <input name="q" value="{{ $search }}" type="search" placeholder="Search models, categories, descriptions…" class="w-full bg-transparent py-3.5 text-sm text-white outline-none placeholder:text-slate-600">
                </label>
                <div class="grid grid-cols-2 gap-3 md:flex md:shrink-0">
                    <select name="category" class="field-select rounded-2xl border border-white/10 bg-[#12151d] px-4 py-3.5 text-sm text-slate-300 outline-none transition hover:border-white/20 focus:border-cyan-300/50 md:w-44">
                        <option value="">All categories</option>
                        @foreach($categories as $availableCategory)
                        <option value="{{ $availableCategory }}" @selected($category === $availableCategory)>{{ $availableCategory }}</option>
                        @endforeach
                    </select>
                    <select name="sort" class="field-select rounded-2xl border border-white/10 bg-[#12151d] px-4 py-3.5 text-sm text-slate-300 outline-none transition hover:border-white/20 focus:border-cyan-300/50 md:w-44">
                        <option value="featured" @selected($sort === 'featured')>Featured first</option>
                        <option value="latest" @selected($sort === 'latest')>Newest</option>
                        <option value="popular" @selected($sort === 'popular')>Most viewed</option>
                        <option value="liked" @selected($sort === 'liked')>Most liked</option>
                        <option value="name" @selected($sort === 'name')>Name A–Z</option>
                    </select>
                </div>
                <div class="flex shrink-0 gap-2">
                    <button class="flex-1 rounded-2xl bg-cyan-300 px-6 py-3.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200 md:flex-none">Search</button>
                    @if($search || $category || $tag || $sort !== 'featured')
                    <a href="{{ route('items.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-3.5 text-sm text-slate-400 transition hover:border-white/20 hover:text-white" aria-label="Clear filters">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m6 6 12 12M18 6 6 18" stroke-linecap="round"/></svg>
                    </a>
                    @endif
                </div>
            </form>

            @if(count($tags))
            <div class="mt-4 flex flex-wrap gap-2 px-1">
                @foreach($tags as $availableTag)
                <a href="{{ route('items.index', array_filter(['q' => $search, 'category' => $category, 'sort' => $sort, 'tag' => $tag === $availableTag ? null : $availableTag])) }}" class="tag-chip rounded-full border border-white/10 px-3 py-1.5 text-xs text-slate-400 transition hover:border-cyan-300/40 hover:text-white @if($tag === $availableTag) is-active @endif">#{{ $availableTag }}</a>
                @endforeach
            </div>
            @endif
        </section>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-500">
            <span>{{ $items->total() }} {{ $items->total() === 1 ? 'model' : 'models' }} available</span>
            <div class="flex items-center gap-4">
                @if($items->total())<span>Page {{ $items->currentPage() }} of {{ $items->lastPage() }}</span>@endif
                <div class="flex items-center gap-1 rounded-xl border border-white/10 bg-white/[.02] p-1">
                    <button type="button" id="view-grid" class="view-toggle-btn flex items-center justify-center rounded-lg p-2 text-slate-400 transition hover:text-white" aria-label="Grid view">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                    </button>
                    <button type="button" id="view-list" class="view-toggle-btn flex items-center justify-center rounded-lg p-2 text-slate-400 transition hover:text-white" aria-label="List view">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <section id="catalog-grid" class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($items as $item)
            <a href="{{ route('items.show', $item->slug) }}" data-slug="{{ $item->slug }}" data-name="{{ $item->name }}" data-image="{{ $item->image_path ? '/storage/'.ltrim($item->image_path, '/') : '' }}" data-category="{{ $item->category }}" class="catalog-card group overflow-hidden rounded-[1.5rem] border border-white/10 transition duration-300 hover:-translate-y-1 hover:border-cyan-300/40">
                <div class="image-shine relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-cyan-500/20 via-slate-900 to-indigo-500/20">
                    @if($item->image_path)
                    <img loading="lazy" src="{{ '/storage/'.ltrim($item->image_path, '/') }}" alt="{{ $item->name }} cover image" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    @else
                    <div class="flex h-full items-center justify-center text-cyan-200/50">
                        <svg class="h-16 w-16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25"><path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z" stroke-linejoin="round"/><path d="M4 7l8 4 8-4M12 11v10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/50 to-transparent opacity-0 transition group-hover:opacity-100"></div>
                    <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center gap-1 rounded-full border border-white/15 bg-black/40 px-2.5 py-1 text-[10px] font-medium uppercase tracking-[.14em] text-cyan-100 backdrop-blur">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z" stroke-linejoin="round"/></svg>
                            Interactive 3D
                        </span>
                        @if($item->is_featured)
                        <span class="inline-flex items-center gap-1 rounded-full border border-amber-300/30 bg-amber-300/15 px-2.5 py-1 text-[10px] font-medium uppercase tracking-[.14em] text-amber-100 backdrop-blur">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.5 14.6 9l6.9.6-5.2 4.5 1.6 6.7L12 17.3 5.9 20.8l1.6-6.7L2.3 9.6 9.2 9 12 2.5Z"/></svg>
                            Featured
                        </span>
                        @endif
                    </div>
                    <div class="absolute bottom-3.5 right-3.5 flex h-10 w-10 translate-y-1 items-center justify-center rounded-full bg-white text-slate-900 opacity-0 shadow-xl transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-medium tracking-tight">{{ $item->name }}</h2>
                            @if($item->category)<p class="mt-1 text-xs uppercase tracking-[.18em] text-cyan-300/80">{{ $item->category }}</p>@endif
                        </div>
                        <span class="mt-1 shrink-0 text-xs text-slate-500">{{ $item->created_at?->format('M Y') }}</span>
                    </div>
                    <p class="mt-3 min-h-[3rem] line-clamp-2 text-sm leading-6 text-slate-400">{{ $item->description ?: 'A detailed 3D model ready to explore and customize.' }}</p>
                    @if($item->tags)
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @foreach(array_slice($item->tags, 0, 3) as $itemTag)
                        <span class="rounded-full bg-white/5 px-2.5 py-1 text-[11px] text-slate-500">#{{ $itemTag }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-4">
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-cyan-300">
                            Open 3D model
                            <svg class="h-3.5 w-3.5 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="flex items-center gap-3 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1" title="Views">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.75"/></svg>
                                {{ number_format($item->view_count) }}
                            </span>
                            <span class="inline-flex items-center gap-1" title="Likes">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 20.2s-7.5-4.6-9.8-9.2C.7 7.9 2.3 4.8 5.4 4.2c1.9-.4 3.8.4 5 2 .9-1.5 2.9-2.4 5-2 3.1.6 4.7 3.7 3.2 6.8-2.3 4.6-9.8 9.2-9.8 9.2Z" stroke-linejoin="round"/></svg>
                                {{ number_format($item->likes_count) }}
                            </span>
                        </span>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full flex flex-col items-center rounded-[1.75rem] border border-dashed border-white/15 bg-white/[.015] p-20 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/5 text-slate-500">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
                </div>
                <h2 class="mt-5 text-xl font-medium">No models found</h2>
                <p class="mt-2 max-w-sm text-sm text-slate-500">Try another search term, or clear the filters to see the full collection.</p>
                @if($search || $category || $tag || $sort !== 'featured')
                <a href="{{ route('items.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl border border-white/10 px-5 py-2.5 text-sm text-slate-300 transition hover:border-cyan-300/40 hover:text-white">Clear filters</a>
                @endif
            </div>
            @endforelse
        </section>

        @if($items->hasPages())
        <nav aria-label="Model pages" class="mt-12 flex items-center justify-center gap-2">
            @if($items->onFirstPage())
            <span class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-700">← Previous</span>
            @else
            <a href="{{ $items->previousPageUrl() }}" class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-400 transition hover:border-white/20 hover:text-white">← Previous</a>
            @endif
            <span class="rounded-xl bg-white/10 px-4 py-2 text-sm text-slate-200">{{ $items->currentPage() }} / {{ $items->lastPage() }}</span>
            @if($items->hasMorePages())
            <a href="{{ $items->nextPageUrl() }}" class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-400 transition hover:border-white/20 hover:text-white">Next →</a>
            @else
            <span class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-700">Next →</span>
            @endif
        </nav>
        @endif
    </main>

    <footer class="relative mt-8 border-t border-white/10 bg-[#07080c]/70">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 py-8 text-sm text-slate-500 sm:flex-row lg:px-12">
            <div class="flex items-center gap-2.5">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-cyan-200/80" aria-hidden="true">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z" stroke-linejoin="round"/></svg>
                </span>
                <span>Model Studio · Interactive 3D catalog</span>
            </div>
            <div class="flex items-center gap-5">
                <a href="{{ route('items.index') }}" class="transition hover:text-white">Browse models</a>
                <a href="/admin" class="transition hover:text-white">Admin workspace</a>
            </div>
        </div>
    </footer>

    <script>
    (function(){
        var gridEl = document.querySelector('#catalog-grid');
        var gridBtn = document.querySelector('#view-grid'), listBtn = document.querySelector('#view-list');
        function setView(mode){
            gridEl.classList.toggle('view-list', mode === 'list');
            gridBtn.classList.toggle('is-active', mode !== 'list');
            listBtn.classList.toggle('is-active', mode === 'list');
            try { localStorage.setItem('catalog-view', mode); } catch(e){}
        }
        gridBtn.onclick = function(){ setView('grid'); };
        listBtn.onclick = function(){ setView('list'); };
        try { setView(localStorage.getItem('catalog-view') || 'grid'); } catch(e){ setView('grid'); }

        try {
            var recent = JSON.parse(localStorage.getItem('recently-viewed') || '[]');
            if (recent.length) {
                var wrap = document.querySelector('#recently-viewed');
                wrap.innerHTML = '<p class="mb-3 text-xs font-semibold uppercase tracking-[.25em] text-slate-500">Recently viewed</p><div class="flex gap-3 overflow-x-auto pb-2"></div>';
                var strip = wrap.querySelector('div');
                recent.slice(0, 8).forEach(function(entry){
                    var a = document.createElement('a');
                    a.href = '/models/' + encodeURIComponent(entry.slug);
                    a.className = 'flex w-40 shrink-0 flex-col overflow-hidden rounded-xl border border-white/10 bg-white/[.03] transition hover:border-cyan-300/40';
                    a.innerHTML = (entry.image ? '<img src="'+entry.image+'" class="h-20 w-full object-cover" loading="lazy">' : '<div class="flex h-20 items-center justify-center bg-gradient-to-br from-cyan-500/15 to-indigo-500/15 text-2xl text-cyan-200/60">◈</div>') + '<span class="truncate p-2 text-xs text-slate-300">'+entry.name+'</span>';
                    strip.append(a);
                });
            }
        } catch(e){}
    })();
    </script>
</body>
</html>
