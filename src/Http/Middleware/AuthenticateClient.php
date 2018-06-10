<?php


namespace Siewwp\LaravelServiceHost\Http\Middleware;

use Acquia\Hmac\Key;
use Acquia\Hmac\ResponseSigner;
use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use \Illuminate\Contracts\Auth\Factory as Auth;
use Psr\Http\Message\ServerRequestInterface;
use Siewwp\LaravelServiceHost\Client;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Siewwp\LaravelServiceHost\Contracts\AuthenticateClient as AuthenticateClientContract;

class AuthenticateClient extends Authenticate implements AuthenticateClientContract
{
    protected $serverRequest;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @param ServerRequestInterface $serverRequest
     */
    public function __construct(Auth $auth, ServerRequestInterface $serverRequest)
    {
        parent::__construct($auth);
        $this->serverRequest = $serverRequest;
    }
    
    /**
     * Handle an incoming request and sign response
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guard = config('service-host.guard');
        
        $this->authenticate([$guard]);

        $response = $next($request);

        $response = (new DiactorosFactory)->createResponse($response);
        
        /** @var Client $client */
        $client = $request->user($guard);
        
        $signer = new ResponseSigner($client->hmac_key, $this->serverRequest);

        return $signer->signResponse($response);
    }
}