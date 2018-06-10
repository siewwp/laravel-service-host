<?php


namespace Siewwp\LaravelServiceHost\Contracts;

use Closure;

interface AuthenticateClient
{
    public function handle($request, Closure $next);

    public function authenticate();

    public function signResponse($response);
}