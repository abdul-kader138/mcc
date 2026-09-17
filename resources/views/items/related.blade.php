@if($relatedItems->isNotEmpty())
<section class="border-t border-white/10 bg-[#0c0f16] px-6 py-16 lg:px-10">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 flex items-end justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-[.25em] text-cyan-300">Keep exploring</p><h2 class="mt-2 text-2xl font-semibold">More from this collection</h2></div><a href="{{ route('items.index', ['category' => $item->category]) }}" class="text-sm text-slate-400 hover:text-white">View category →</a></div>
        <div class="grid gap-5 md:grid-cols-3">@foreach($relatedItems as $related)<a href="{{ route('items.show', $related->slug) }}" class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[.03] transition hover:-translate-y-1 hover:border-cyan-300/40">@if($related->image_path)<img loading="lazy" src="{{ '/storage/'.ltrim($related->image_path, '/') }}" alt="{{ $related->name }} cover image" class="aspect-[16/9] w-full object-cover transition duration-500 group-hover:scale-105">@else<div class="flex aspect-[16/9] items-center justify-center bg-gradient-to-br from-cyan-500/15 to-indigo-500/15 text-4xl text-cyan-200/60">◈</div>@endif<div class="p-4"><p class="font-medium">{{ $related->name }}</p>@if($related->category)<p class="mt-1 text-xs uppercase tracking-[.15em] text-cyan-300/70">{{ $related->category }}</p>@endif</div></a>@endforeach</div>
    </div>
</section>
@endif
