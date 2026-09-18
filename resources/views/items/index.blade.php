<!doctype html>
@include('components.public-language-switcher')
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Model library</title>
    <meta name="description" content="Explore and customize interactive 3D models.">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body{background:#080a0f}.catalog-card{background:linear-gradient(145deg,rgba(255,255,255,.075),rgba(255,255,255,.025));box-shadow:0 20px 70px rgba(0,0,0,.18)}.catalog-card:hover{box-shadow:0 25px 80px rgba(34,211,238,.12)}.image-shine:after{content:"";position:absolute;inset:0;background:linear-gradient(115deg,transparent 20%,rgba(255,255,255,.14) 48%,transparent 70%);transform:translateX(-120%);transition:transform .7s}.catalog-card:hover .image-shine:after{transform:translateX(120%)}.tag-chip.is-active{background:#22d3ee!important;color:#020617!important;border-color:#22d3ee!important}#catalog-grid.view-list{grid-template-columns:1fr!important}#catalog-grid.view-list .catalog-card{display:grid;grid-template-columns:14rem 1fr}#catalog-grid.view-list .image-shine{aspect-ratio:auto}#recently-viewed:empty{display:none}</style>
</head>
<body class="min-h-screen text-white">
    <div class="pointer-events-none fixed inset-0 overflow-hidden"><div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div><div class="absolute -bottom-40 -right-20 h-[32rem] w-[32rem] rounded-full bg-indigo-500/10 blur-3xl"></div></div>
    <header class="relative border-b border-white/10 bg-black/20 px-6 backdrop-blur-xl lg:px-12"><div class="mx-auto flex h-20 max-w-7xl items-center justify-between"><a href="{{ route('items.index') }}" class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-300/15 text-xl text-cyan-200">◈</span><span class="font-medium tracking-tight">Model studio</span></a><a href="/admin" class="rounded-full border border-white/10 px-5 py-2.5 text-sm text-slate-300 transition hover:border-cyan-300/40 hover:bg-white/10 hover:text-white">Admin workspace <span class="ml-1">↗</span></a></div></header>
    <main class="relative mx-auto max-w-7xl px-6 py-16 lg:px-12 lg:py-24">
        <section class="max-w-3xl"><p class="mb-5 text-xs font-semibold uppercase tracking-[.35em] text-cyan-300">Public collection</p><h1 class="text-5xl font-semibold tracking-[-.04em] sm:text-6xl">Explore every angle.</h1><p class="mt-6 max-w-2xl text-lg leading-8 text-slate-400">Browse the collection, open any model in an interactive 3D space, and make your own color and texture variations.</p></section>

        <section id="recently-viewed" class="mt-10"></section>

        <form method="GET" action="{{ route('items.index') }}" class="mt-12 flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/[.04] p-3 md:flex-row">
            <input type="hidden" name="tag" value="{{ $tag }}">
            <label class="flex flex-1 items-center gap-3 rounded-xl bg-black/20 px-4"><span class="text-slate-500">⌕</span><input name="q" value="{{ $search }}" type="search" placeholder="Search models, categories, descriptions…" class="w-full bg-transparent py-3 text-sm text-white outline-none placeholder:text-slate-600"></label>
            <select name="category" class="rounded-xl border border-white/10 bg-[#131722] px-4 py-3 text-sm text-slate-300 outline-none"><option value="">All categories</option>@foreach($categories as $availableCategory)<option value="{{ $availableCategory }}" @selected($category === $availableCategory)>{{ $availableCategory }}</option>@endforeach</select>
            <select name="sort" class="rounded-xl border border-white/10 bg-[#131722] px-4 py-3 text-sm text-slate-300 outline-none">
                <option value="featured" @selected($sort === 'featured')>Featured first</option>
                <option value="latest" @selected($sort === 'latest')>Newest</option>
                <option value="popular" @selected($sort === 'popular')>Most viewed</option>
                <option value="liked" @selected($sort === 'liked')>Most liked</option>
                <option value="name" @selected($sort === 'name')>Name A–Z</option>
            </select>
            <button class="rounded-xl bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">Search</button>
            @if($search || $category || $tag || $sort !== 'featured')<a href="{{ route('items.index') }}" class="rounded-xl border border-white/10 px-5 py-3 text-center text-sm text-slate-400 hover:text-white">Clear</a>@endif
        </form>

        @if(count($tags))
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach($tags as $availableTag)
            <a href="{{ route('items.index', array_filter(['q' => $search, 'category' => $category, 'sort' => $sort, 'tag' => $tag === $availableTag ? null : $availableTag])) }}" class="tag-chip rounded-full border border-white/10 px-3 py-1.5 text-xs text-slate-400 transition hover:border-cyan-300/40 hover:text-white @if($tag === $availableTag) is-active @endif">#{{ $availableTag }}</a>
            @endforeach
        </div>
        @endif

        <div class="mt-8 flex items-center justify-between text-sm text-slate-500">
            <span>{{ $items->total() }} {{ $items->total() === 1 ? 'model' : 'models' }} available</span>
            <div class="flex items-center gap-4">
                @if($items->total())<span>Page {{ $items->currentPage() }} of {{ $items->lastPage() }}</span>@endif
                <div class="flex items-center gap-1 rounded-lg border border-white/10 p-1">
                    <button type="button" id="view-grid" class="rounded-md px-2.5 py-1 text-xs text-slate-300 hover:text-white" aria-label="Grid view">▦</button>
                    <button type="button" id="view-list" class="rounded-md px-2.5 py-1 text-xs text-slate-300 hover:text-white" aria-label="List view">☰</button>
                </div>
            </div>
        </div>

        <section id="catalog-grid" class="mt-5 grid gap-7 sm:grid-cols-2 lg:grid-cols-3">@forelse($items as $item)<a href="{{ route('items.show', $item->slug) }}" data-slug="{{ $item->slug }}" data-name="{{ $item->name }}" data-image="{{ $item->image_path ? '/storage/'.ltrim($item->image_path, '/') : '' }}" data-category="{{ $item->category }}" class="catalog-card group overflow-hidden rounded-[1.75rem] border border-white/10 transition duration-300 hover:-translate-y-1 hover:border-cyan-300/40"><div class="image-shine relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-cyan-500/20 via-slate-900 to-indigo-500/20">@if($item->image_path)<img loading="lazy" src="{{ '/storage/'.ltrim($item->image_path, '/') }}" alt="{{ $item->name }} cover image" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">@else<div class="flex h-full items-center justify-center text-7xl text-cyan-200/60">◈</div>@endif<div class="absolute left-4 top-4 flex gap-2"><span class="rounded-full border border-white/15 bg-black/35 px-3 py-1.5 text-[11px] font-medium uppercase tracking-[.16em] text-cyan-100 backdrop-blur">Interactive 3D</span>@if($item->is_featured)<span class="rounded-full border border-amber-300/30 bg-amber-300/15 px-3 py-1.5 text-[11px] font-medium uppercase tracking-[.16em] text-amber-100 backdrop-blur">Featured</span>@endif</div><div class="absolute bottom-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-white text-lg text-slate-900 opacity-0 shadow-xl transition group-hover:opacity-100">↗</div></div><div class="p-6"><div class="flex items-start justify-between gap-4"><div><h2 class="text-xl font-medium tracking-tight">{{ $item->name }}</h2>@if($item->category)<p class="mt-1 text-xs uppercase tracking-[.18em] text-cyan-300/80">{{ $item->category }}</p>@endif</div><span class="mt-1 shrink-0 text-xs text-slate-500">{{ $item->created_at?->format('M Y') }}</span></div><p class="mt-3 min-h-[3rem] line-clamp-2 text-sm leading-6 text-slate-400">{{ $item->description ?: 'A detailed 3D model ready to explore and customize.' }}</p>@if($item->tags)<div class="mt-4 flex flex-wrap gap-1.5">@foreach(array_slice($item->tags, 0, 3) as $itemTag)<span class="rounded-full bg-white/5 px-2.5 py-1 text-[11px] text-slate-500">#{{ $itemTag }}</span>@endforeach</div>@endif<div class="mt-6 flex items-center justify-between border-t border-white/10 pt-4"><span class="text-sm font-medium text-cyan-300">Open 3D model</span><span class="flex items-center gap-3 text-xs text-slate-500"><span title="Views">◉ {{ $item->view_count }}</span><span title="Likes">♥ {{ $item->likes_count }}</span><span class="transition group-hover:translate-x-1">→</span></span></div></div></a>@empty<div class="col-span-full rounded-[1.75rem] border border-dashed border-white/15 p-20 text-center"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/5 text-3xl text-slate-500">⌕</div><h2 class="mt-5 text-xl font-medium">No models found</h2><p class="mt-2 text-sm text-slate-500">Try another search or clear the filters.</p></div>@endforelse</section>
        @if($items->hasPages())<nav aria-label="Model pages" class="mt-12 flex items-center justify-center gap-2">@if($items->onFirstPage())<span class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-700">← Previous</span>@else<a href="{{ $items->previousPageUrl() }}" class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-400 hover:text-white">← Previous</a>@endif<span class="rounded-xl bg-white/10 px-4 py-2 text-sm text-slate-300">{{ $items->currentPage() }}</span>@if($items->hasMorePages())<a href="{{ $items->nextPageUrl() }}" class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-400 hover:text-white">Next →</a>@else<span class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-700">Next →</span>@endif</nav>@endif
    </main>
    <script>
    (function(){
        var gridEl = document.querySelector('#catalog-grid');
        var gridBtn = document.querySelector('#view-grid'), listBtn = document.querySelector('#view-list');
        function setView(mode){ gridEl.classList.toggle('view-list', mode === 'list'); gridBtn.classList.toggle('bg-white/10', mode !== 'list'); listBtn.classList.toggle('bg-white/10', mode === 'list'); try { localStorage.setItem('catalog-view', mode); } catch(e){} }
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
