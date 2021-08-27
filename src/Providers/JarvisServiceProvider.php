<?php

namespace Lara\Jarvis\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Lara\Jarvis\Console\InstallJarvisPackage;
use Lara\Jarvis\Utils\PixPayloadGenerator;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Http\Middleware\Authenticate;

class JarvisServiceProvider extends ServiceProvider
{
    public function boot ()
    {
        $this->registerRoutes();

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'custom-auth');

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('auth', Authenticate::class);

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {

            // Export Config
            $this->publishes([
                __DIR__ . '/../../config/config.php' => config_path('jarvis.php'),
            ], 'config');

            // Export the migration
            $this->exportMigrations();


            $this->commands([
                InstallJarvisPackage::class,
            ]);
        }
    }

    public function register ()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'jarvis');

        // Facedes
        $this->app->bind('pixPayloadGenerator', function ($app) {
            return new PixPayloadGenerator();
        });
    }

    protected function registerRoutes ()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/jarvis-api.php');
        });
    }

    protected function routeConfiguration ()
    {
        return [
            'prefix'     => config('jarvis.prefix'),
            'middleware' => config('jarvis.middleware'),
            'namespace'  => 'Lara\Jarvis\Http\Controllers\Api'
        ];
    }

    protected function exportMigrations ()
    {
        if (!class_exists('CreateStatesTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_states_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . 1 . '_create_states_table.php'),
                __DIR__ . '/../../database/migrations/create_cities_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . 2 . '_create_cities_table.php'),
            ], 'migrations');
        }

        if (!class_exists('CreateCommentsTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_comments_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_comments_table.php'),
            ], 'migrations');
        }

        if (!class_exists('CreateBanksTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_banks_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_banks_table.php'),
            ], 'migrations');
        }
    }
}
