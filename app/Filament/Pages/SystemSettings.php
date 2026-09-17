<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Providers\Filament\AdminPanelProvider;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SystemSettings extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 40;
    public ?array $data = [];

    public static function getNavigationLabel(): string { return __('System Settings'); }
    public static function getNavigationGroup(): ?string { return __('Administration'); }
    public static function canAccess(): bool
    {
        $user = auth()->user();
        $superAdmin = (string) config('filament-shield.super_admin.name', 'super_admin');
        return $user && (($user->hasRole($superAdmin)) || $user->can('page_SystemSettings'));
    }
    public function getTitle(): string { return __('System Settings'); }
    public function getView(): string { return 'filament.pages.system-settings'; }

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => Setting::get('app_name', '3D Model Studio'),
            'app_tagline' => Setting::get('app_tagline', 'Create and customize interactive 3D models.'),
            'default_locale' => Setting::get('default_locale', 'en'),
            'admin_theme' => Setting::get('admin_theme', 'indigo'),
            'admin_panel_theme_mode' => Setting::get('admin_panel_theme_mode', 'dark'),
            'app_logo' => Setting::get('app_logo'),
            'app_icon' => Setting::get('app_icon'),
            'favicon' => Setting::get('favicon'),
            'two_factor_enabled' => Setting::get('two_factor_enabled', true),
            'google_client_id' => Setting::get('google_client_id', config('services.google.client_id', '')),
            'google_client_secret' => Setting::get('google_client_secret', config('services.google.client_secret', '')),
            'mail_from_name' => Setting::get('mail_from_name', config('mail.from.name', '')),
            'mail_from_address' => Setting::get('mail_from_address', config('mail.from.address', '')),
            'mail_active_vendor' => Setting::get('mail_active_vendor', 'smtp'),
            'mail_vendors' => $this->getMailVendors(),
            'staff_notification_email' => Setting::get('staff_notification_email', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Tabs::make('settings_tabs')->tabs([
                Tab::make(__('General'))->icon('heroicon-o-home')->schema([
                    Section::make(__('Application'))->schema([
                        TextInput::make('app_name')->label(__('Application Name'))->required()->maxLength(100),
                        TextInput::make('app_tagline')->label(__('Tagline'))->maxLength(200),
                    ])->columns(2),
                    Section::make(__('Language'))->schema([
                        Select::make('default_locale')->label(__('Default language'))->options(['en' => __('English'), 'fr' => __('Français')])->native(false)->required(),
                    ]),
                ]),
                Tab::make(__('Appearance'))->icon('heroicon-o-swatch')->schema([
                    Section::make(__('Color Theme'))->schema([
                        Radio::make('admin_theme')->label(__('Admin Panel Theme'))->options(collect(AdminPanelProvider::$themes)->mapWithKeys(fn ($t, $key) => [$key => $t['label']])->toArray())->columns(4)->required(),
                    ]),
                    Section::make(__('Panel Mode'))->schema([
                        Radio::make('admin_panel_theme_mode')->label(__('Admin Panel Mode'))->options(['light' => 'Light', 'dark' => 'Dark', 'system' => 'System', 'high_contrast' => 'High Contrast', 'sepia' => 'Sepia', 'midnight' => 'Midnight'])->inline()->required(),
                    ]),
                    Section::make(__('Branding'))->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('app_logo')->label(__('Application Logo'))->image()->disk('public')->directory('branding')->visibility('public'),
                            FileUpload::make('app_icon')->label(__('App Icon / Favicon'))->image()->disk('public')->directory('branding')->visibility('public'),
                            FileUpload::make('favicon')->label(__('Favicon'))->image()->disk('public')->directory('branding')->visibility('public'),
                        ]),
                    ]),
                ]),
                Tab::make(__('Security'))->icon('heroicon-o-shield-check')->schema([
                    Section::make(__('Two-Factor Authentication'))->schema([
                        Toggle::make('two_factor_enabled')->label(__('Allow two-factor authentication'))->default(true),
                    ]),
                    Section::make(__('Google Sign-In'))->schema([
                        Grid::make(2)->schema([
                            TextInput::make('google_client_id')->label(__('Client ID'))->maxLength(255),
                            TextInput::make('google_client_secret')->label(__('Client Secret'))->password()->revealable()->autocomplete('new-password')->maxLength(255),
                        ]),
                        Placeholder::make('google_redirect_uri')->label(__('Authorized redirect URI'))->content(fn () => route('auth.google.callback')),
                    ]),
                ]),
                Tab::make(__('Email'))->icon('heroicon-o-envelope')->schema([
                    Section::make(__('Sender'))->schema([
                        Grid::make(2)->schema([
                            TextInput::make('mail_from_name')->label(__('From Name'))->required()->maxLength(100),
                            TextInput::make('mail_from_address')->label(__('From Address'))->email()->required()->maxLength(255),
                        ]),
                        TextInput::make('staff_notification_email')->label(__('Staff Notification Email'))->email()->maxLength(255),
                    ]),
                    Section::make(__('Email Vendors'))->schema([
                        Select::make('mail_active_vendor')->label(__('Active vendor'))->options(['smtp' => 'SMTP', 'brevo' => 'Brevo', 'sendgrid' => 'SendGrid', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES SMTP', 'postmark' => 'Postmark SMTP', 'resend' => 'Resend SMTP', 'log' => 'Log only'])->required()->native(false),
                        Repeater::make('mail_vendors')->label(__('Vendor profiles'))->schema([
                            Grid::make(2)->schema([
                                TextInput::make('key')->label('Vendor key')->required()->alphaDash()->maxLength(50),
                                TextInput::make('label')->label('Display name')->required()->maxLength(100),
                                TextInput::make('host')->label('SMTP host')->maxLength(255),
                                TextInput::make('port')->label('SMTP port')->numeric()->default(587),
                                TextInput::make('username')->label('SMTP username')->maxLength(255),
                                TextInput::make('password')->label('SMTP password / API key')->password()->revealable()->autocomplete('new-password')->maxLength(255),
                                Select::make('encryption')->label('Encryption')->options(['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None'])->default('tls')->native(false),
                                Select::make('transport')->label('Transport')->options(['smtp' => 'SMTP', 'log' => 'Log only'])->default('smtp')->native(false),
                            ]),
                        ])->defaultItems(1)->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['key'] ?? null)->collapsible()->reorderable(false)->required(),
                    ]),
                ]),
            ])->persistTabInQueryString('tab'),
        ]);
    }

    public function save(): void
    {
        $groups = ['app_name' => 'general', 'app_tagline' => 'general', 'default_locale' => 'general', 'admin_theme' => 'appearance', 'admin_panel_theme_mode' => 'appearance', 'app_logo' => 'appearance', 'app_icon' => 'appearance', 'favicon' => 'appearance', 'two_factor_enabled' => 'security', 'google_client_id' => 'security', 'google_client_secret' => 'security', 'mail_from_name' => 'email', 'mail_from_address' => 'email', 'mail_active_vendor' => 'email', 'mail_vendors' => 'email', 'staff_notification_email' => 'email'];
        foreach ($this->form->getState() as $key => $value) Setting::set($key, $value ?? '', $groups[$key] ?? 'general');
        Notification::make()->success()->title(__('Settings saved'))->send();
    }

    protected function getFormActions(): array { return [Action::make('save')->label(__('Save'))->submit('save')]; }

    private function getMailVendors(): array
    {
        $vendors = Setting::get('mail_vendors');
        return is_array($vendors) && $vendors !== [] ? $vendors : [[
            'key' => 'smtp', 'label' => 'SMTP', 'transport' => 'smtp', 'host' => Setting::get('mail_host', ''), 'port' => Setting::get('mail_port', 587), 'username' => Setting::get('mail_username', ''), 'password' => Setting::get('mail_password', ''), 'encryption' => Setting::get('mail_encryption', 'tls'),
        ]];
    }
}
