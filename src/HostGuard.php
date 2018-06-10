<?php


namespace Siewwp\LaravelServiceHost;

use Acquia\Hmac\AuthorizationHeader;
use Acquia\Hmac\Exception\InvalidSignatureException;
use Acquia\Hmac\Exception\MalformedRequestException;
use Acquia\Hmac\KeyLoader;
use Acquia\Hmac\RequestAuthenticator;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Siewwp\LaravelServiceHost\Contracts\Client as ClientContract;

class HostGuard implements Guard
{
    use GuardHelpers;
    protected $serverRequest;
    protected $client;

    /**
     * Instantiate the class.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider $provider
     * @param ServerRequestInterface $serverRequest
     */
    public function __construct(UserProvider $provider, ServerRequestInterface $serverRequest)
    {
        $this->provider = $provider;
        $this->serverRequest = $serverRequest;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if ($this->validateAuthorization()) {
            return $this->user = $this->client;
        }
    }

    protected function validateAuthorization()
    {
        try{
            $authorizationHeader = AuthorizationHeader::createFromRequest($this->serverRequest);

            $client = app(ClientContract::class);
            
            $this->client = $client = $client::findOrFail($authorizationHeader->getId());
            
            $keyLoader = new KeyLoader([$client->id => $client->token]);

            $authenticator = new RequestAuthenticator($keyLoader);

            $authenticator->authenticate($this->serverRequest);

            return true;
            
        } catch (MalformedRequestException $e) {
            return false;
        } catch (InvalidSignatureException $e) {
            return false;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Always return false, as it is not necessary
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return false;
    }

    protected function getRequestParameter($parameter, ServerRequestInterface $request, $default = null)
    {
        $requestParameters = (array) $request->getParsedBody();

        return isset($requestParameters[$parameter]) ? $requestParameters[$parameter] : $default;
    }
}