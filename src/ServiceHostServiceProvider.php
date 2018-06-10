<?php


namespace Siewwp\LaravelServiceHost;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Siewwp\LaravelServiceHost\Console\ClientCommand;
use Siewwp\LaravelServiceHost\Contracts\Client as ClientContract;
use Psr\Http\Message\ServerRequestInterface;

class ServiceHostServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerHostClientGuard();
        $this->registerModelBinding();
        if ($this->app->runningInConsole()) {
            $this->registerPublishes();
            $this->commands([
                ClientCommand::class
            ]);
        }
    }

    protected function registerPublishes()
    {
        $this->publishes([
            __DIR__.'/../config/service-host.php' => config_path('service-host.php'),
        ], 'config');


        if (! class_exists('CreateServiceHostTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_service_host_table.php.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_service_host_table.php",
            ], 'migrations');
        }
    }

    protected function registerHostClientGuard()
    {
        Auth::extend('service-host', function ($app, $name, array $config) {
            return new HostGuard(
                Auth::createUserProvider($config['provider']),
                $app[ServerRequestInterface::class]
            );
        });
    }
    
    protected function registerModelBinding()
    {
        $clientClass = $this->app->config['service-host.models.client'];

        $this->app->bind(ClientContract::class, $clientClass);
    }
}