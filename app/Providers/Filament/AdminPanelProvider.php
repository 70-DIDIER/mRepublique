<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\CommandesChartWidget;
use App\Filament\Widgets\LatestCommandesWidget;
use App\Filament\Widgets\LivraisonsChartWidget;
use App\Filament\Widgets\StatutsCommandesWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName("M'République")
            ->brandLogo(asset('img/Logo version 2.png'))
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('img/favicon.png'))
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::hex('#2B7A9E'),
                'gray'    => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                StatsOverviewWidget::class,
                CommandesChartWidget::class,
                StatutsCommandesWidget::class,
                LivraisonsChartWidget::class,
                LatestCommandesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString('
                    <style>
                        :root {
                            --sidebar-width: 15rem !important;
                        }
                        .fi-sidebar {
                            width: 15rem !important;
                        }
                        .fi-sidebar-nav-groups {
                            padding-inline: 0.5rem;
                        }
                    </style>
                ')
            );
    }
}
