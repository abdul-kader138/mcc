<?php

namespace App\Filament\Pages;

use App\Models\ItemAnalyticsEvent;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 35;
    protected static string $view = 'filament.pages.analytics-dashboard';

    public int $period = 30;
    public int $totalViews = 0;
    public int $uniqueVisitors = 0;
    public int $savedConfigurations = 0;
    public int $quoteRequests = 0;
    public array $topItems = [];
    public array $eventBreakdown = [];

    public static function getNavigationLabel(): string { return __('Analytics'); }
    public static function getNavigationGroup(): ?string { return __('Administration'); }
    public function getTitle(): string { return __('Analytics'); }

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $since = now()->subDays($this->period);
        $events = ItemAnalyticsEvent::query()->where('created_at', '>=', $since);
        $this->totalViews = (clone $events)->where('event', 'view')->count();
        $this->uniqueVisitors = (clone $events)->whereNotNull('visitor_hash')->distinct('visitor_hash')->count('visitor_hash');
        $this->savedConfigurations = (clone $events)->where('event', 'configuration_saved')->count();
        $this->quoteRequests = (clone $events)->where('event', 'quote_requested')->count();
        $this->eventBreakdown = (clone $events)->select('event', DB::raw('count(*) as total'))->groupBy('event')->orderByDesc('total')->pluck('total', 'event')->toArray();
        $this->topItems = (clone $events)->where('event', 'view')->whereNotNull('item_id')->select('item_id', DB::raw('count(*) as total'))->groupBy('item_id')->orderByDesc('total')->limit(8)->with('item:id,name')->get()->map(fn ($event): array => ['name' => $event->item?->name ?? __('Deleted model'), 'total' => $event->total])->all();
    }
}
