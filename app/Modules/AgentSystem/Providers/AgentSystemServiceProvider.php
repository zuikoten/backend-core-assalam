<?php
// app/Modules/AgentSystem/Providers/AgentSystemServiceProvider.php
namespace App\Modules\AgentSystem\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AgentSystemServiceProvider extends ServiceProvider
{
    //Register any application services.
    public function register(): void
    {
        // Register module routes
        $this->loadRoutes();
    }

    //Bootstrap any application services.
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(
            base_path('database/migrations')
        );
    }

    // Load module routes.
    protected function loadRoutes(): void
    {
        $this->loadRoutesFrom(
            __DIR__ . '/../Routes/api.php'
        );
    }
}
