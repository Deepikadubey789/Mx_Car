<?php

namespace App\Providers;

use Botble\Base\Facades\DashboardMenu;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerDashboardMenu();
    }

    /**
     * Register dashboard menu items
     */
    private function registerDashboardMenu(): void
    {
        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()->registerItem([
                'id' => 'chat-settings',
                'priority' => 50,
                'name' => 'Chat Settings',
                'icon' => 'fas fa-comments',
                'route' => 'admin.chat-settings.index',
            ]);
        });
    }
}
