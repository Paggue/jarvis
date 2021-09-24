<?php

namespace Lara\Jarvis\Providers;

use Aws\Laravel\AwsServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Lara\Jarvis\Console\InstallJarvisPackage;
use Lara\Jarvis\Console\PublishJarvisSeeders;
use Lara\Jarvis\Utils\PixPayloadGenerator;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Http\Middleware\Authenticate;

// TODO configurar aws sdk, fazer audits funcionar, possibilidade de add payment forms ao pacote
class JarvisServiceProvider extends ServiceProvider
{
    public function boot ()
    {
        $this->registerRoutes();

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'custom-auth');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/emails', 'jarvis-mail');

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('auth', Authenticate::class);

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {

            // Export Config
            $this->publishes([
                __DIR__ . '/../../config/config.php' => config_path('jarvis.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../../config/audit.php' => config_path('audit.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../../config/auth.php' => config_path('auth.php'),
            ], 'config');

            // Export the migration
            $this->exportMigrations();

            $this->exportSeeders();

            $this->commands([
                InstallJarvisPackage::class,
                PublishJarvisSeeders::class,
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

        if (class_exists(AwsServiceProvider::class)) {
            /*
            * Register the service provider for the dependency.
            */
            $this->app->register('Aws\Laravel\AwsServiceProvider');
            $this->app->register('Barryvdh\DomPDF\ServiceProvider');
            $this->app->register('geekcom\ValidatorDocs\ValidatorProvider');
            $this->app->register('OwenIt\Auditing\AuditingServiceProvider');

            /*
             * Create aliases for the dependency.
             */
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('AWS', 'Aws\Laravel\AwsFacade');
            $loader->alias('PDF', 'Barryvdh\DomPDF\Facade');
        }
    }

    protected function registerRoutes ()
    {
        Route::group($this->apiRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/jarvis-api.php');
        });

        Route::group($this->authRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/jarvis-auth.php');
        });
    }

    protected function apiRouteConfiguration ()
    {
        return [
            'prefix'     => config('jarvis.routes.api_prefix'),
            'middleware' => config('jarvis.routes.middleware'),
            'namespace'  => 'Lara\Jarvis\Http\Controllers\Api'
        ];
    }

    protected function authRouteConfiguration ()
    {
        return [
            'prefix'     => config('jarvis.routes.auth_prefix'),
            'middleware' => config('jarvis.routes.middleware'),
            'namespace'  => 'Lara\Jarvis\Http\Controllers\Auth'
        ];
    }

    protected function exportMigrations ()
    {
        if (!class_exists('CreateStatesTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_states_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . 1 . '_create_states_table.php'),
                __DIR__ . '/../../database/migrations/create_cities_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . 2 . '_create_cities_table.php'),
            ], 'jarvis-migrations');
        }

        if (!class_exists('CreateCommentsTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_comments_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_comments_table.php'),
            ], 'jarvis-migrations');
        }

        if (!class_exists('CreateBanksTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_banks_table.php.stub'         => database_path('migrations/' . date('Y_m_d_Hisz', time()) . 1 . '_create_banks_table.php'),
                __DIR__ . '/../../database/migrations/create_bank_accounts_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . 2 . '_create_bank_accounts_table.php'),
            ], 'jarvis-migrations');
        }

        if (!class_exists('CreateSettingsTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_settings_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_settings_table.php'),
            ], 'jarvis-migrations');
        }

        if (!class_exists('CreateHolidaysTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_holidays_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_holidays_table.php'),
            ], 'jarvis-migrations');
        }

        if (!class_exists('CreateAuditsTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_audits_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_audits_table.php'),
            ], 'jarvis-migrations');
        }

        if (!class_exists('CreateAddressesTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_addresses_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_addresses_table.php'),
            ], 'jarvis-migrations');
        }

        if (!class_exists('CreateUserDeviceTokensTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_user_device_tokens_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hisz', time()) . '_create_user_device_tokens_table.php'),
            ], 'jarvis-migrations');
        }
    }

    protected function exportSeeders ()
    {
        $this->publishes([
            __DIR__ . '/../../database/seeders/JarvisSeeder.php' => database_path('seeders/JarvisSeeder.php'),
        ], 'jarvis-seeders');

        $this->publishes([
            __DIR__ . '/../../database/seeders/BankSeeder.php' => database_path('seeders/BankSeeder.php'),
        ], 'jarvis-seeders');

        $this->publishes([
            __DIR__ . '/../../database/seeders/HolidaySeeder.php' => database_path('seeders/HolidaySeeder.php'),
        ], 'jarvis-seeders');
    }
}
