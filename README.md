## Installation

```
composer require siewwp/laravel-service-host:dev-master
```

Publish service host config 
``` bash
php artisan vendor:publish --provider="Siewwp\LaravelServiceHost\ServiceHostServiceProvider" --tag="config"
```

Publish service host migration
``` bash
php artisan vendor:publish --provider="Siewwp\LaravelServiceHost\ServiceHostServiceProvider" --tag="migrations"
```

Run migration

```
php artisan migrate
```

run the command below on console to create host 

```
php artisan service-host:client {name} {webhook_url}
```


## Guide


### Authenticating client request

Add clients user provider in your `auth.php` configuration file.

```
'providers' => [
    'clients' => [
        'driver' => 'eloquent',
        'model' => Siewwp\LaravelServiceHost\Client::class,
    ],
],
```

Finally, you may use this provider in your guards configuration:

```
'guards' => [
    'client' => [
        'driver' => 'service-host',
        'provider' => 'clients',
    ],
],
```

### Webhook



migration

webhook

laravel-service-host
includes abstract HttpClient and abstract middleware
consumer notification
guard

shared-service-host

shared service tenant seeder