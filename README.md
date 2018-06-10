# Laravel service host

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

```php
'providers' => [
    'clients' => [
        'driver' => 'eloquent',
        'model' => Siewwp\LaravelServiceHost\Client::class,
    ],
],
```

Finally, you may use this provider in your guards configuration:

```php
'guards' => [
    'client' => [
        'driver' => 'service-host',
        'provider' => 'clients',
    ],
],
```


### Sending a webhook notification

To send notification, create a notification and specify `WebhookChannel::class` like below:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Siewwp\LaravelServiceHost\Channels\WebhookChannel;

class InvoicePaid extends Notification
{
    public function via($notifiable)
    {
        return [WebhookChannel::class];
    }
    
    public function toWebhook($notifiable)
    {
        return [
            // ...
        ];
    }
    
    // ...
}
```

## TODO

TEST