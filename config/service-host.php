<?php

return [
    'models' => [
        'client' => \Siewwp\LaravelServiceHost\Client::class,
    ],

    'host_clients_table_name' => 'host_clients',
    
    'guard' => 'client'
];
