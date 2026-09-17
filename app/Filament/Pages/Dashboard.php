<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\WelcomeHeaderWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }

    public function getWidgets(): array
    {
        return [WelcomeHeaderWidget::class];
    }
}
