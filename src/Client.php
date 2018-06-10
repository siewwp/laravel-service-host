<?php


namespace Siewwp\LaravelServiceHost;

use Siewwp\LaravelServiceHost\Contracts\Client as ClientContract;
use Acquia\Hmac\Key;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class Client
 * 
 * @property Key $hmac_key
 * @package Siewwp\LaravelServiceHost
 */
class Client extends Model implements ClientContract
{
    use Notifiable;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('service-host.host_clients_table_name'));
    }


    public function routeNotificationForHmacKey()
    {
        return $this->hmac_key;
    }

    public function getHmacKeyAttribute()
    {
        return new Key($this->id, $this->secret);
    }
}