<?php

namespace Siewwp\LaravelServiceHost\Console;

use Illuminate\Console\Command;
use Siewwp\LaravelServiceHost\Contracts\Client as ClientContract;

class ClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-host:client
            {--name= : The name of the client}
            {--webhook_url= : The client\' webhook url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a client';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the client?'
        );
        
        $webhookUrl = $this->option('webhook_url') ?: $this->ask(
            'What is the client\'s webhook url?'
        );

        $client = app(ClientContract::class);

        $client = (new $client)->forceFill([
            'name' => $name,
            'webhook_url' => $webhookUrl,
            'secret' => str_random(40),
            'revoked' => false,
        ]);

        $client->save();

        $this->info('New client created successfully.');
        $this->line('<comment>Client ID:</comment> '.$client->id);
        $this->line('<comment>Client secret:</comment> '.$client->secret);
    }

}
