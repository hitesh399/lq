<?php

namespace Singsys\LQ\Lib\Token;

use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Laravel\Passport\ClientRepository as ClientModelRepository;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

class AuthToken extends BearerTokenResponse
{
    protected $client_id;
    protected $user_id;
    protected $scope;
    protected $tokenGrand;


    public function __construct($client_id, $user_id, $scope = [])
    {
        $this->client_id = $client_id;
        $this->user_id = $user_id;
        $this->scope = $scope;

        $this->tokenGrand = new TokenGrant();
    }

    /**
     * Get The User Jwt Access Token
     */
    public function generateToken($accessToken = null)
    {
        $client = new \Laravel\Passport\Bridge\Client($this->client_id, 'Laravel Password Grant Client', url('/'));
        $time = new \DateInterval('P1Y');
         # private Key
        $private_key = new CryptKey(storage_path('oauth-private.key'), null, false);

        $this->setPrivateKey($private_key);
        
        if (method_exists($this->tokenGrand, 'setPrivateKey')) {
            $this->tokenGrand->setPrivateKey($private_key);
        }
        

        $this->setEncryptionKey(app('encrypter')->getKey());
        $accessToken = $this->tokenGrand->issueToken($time, $client, $this->user_id, []);


        $refreshToken = $this->tokenGrand->issueRefreshAccessToken($accessToken);

        $this->setAccessToken($accessToken);
        $this->setRefreshToken($refreshToken);
       

        $expireDateTime = $this->accessToken->getExpiryDateTime()->getTimestamp();

        $jwtAccessToken =  method_exists($this->accessToken, '__toString') ? $this->accessToken->__toString() : $this->accessToken->convertToJWT($this->privateKey);

        $responseParams = [
        'token_type'   => 'Bearer',
        'expires_in'   => $expireDateTime - (new \DateTime())->getTimestamp(),
        'access_token' => (string) $jwtAccessToken,
        ];

        if ($this->refreshToken instanceof RefreshTokenEntityInterface) {
            $refreshToken = $this->encrypt(
                json_encode(
                    [
                        'client_id'        => $this->accessToken->getClient()->getIdentifier(),
                        'refresh_token_id' => $this->refreshToken->getIdentifier(),
                        'access_token_id'  => $this->accessToken->getIdentifier(),
                        'scopes'           => $this->accessToken->getScopes(),
                        'user_id'          => $this->accessToken->getUserIdentifier(),
                        'expire_time'      => $this->refreshToken->getExpiryDateTime()->getTimestamp(),
                    ]
                )
            );

            $responseParams['refresh_token'] = $refreshToken;
        }

        $responseParams = array_merge($this->getExtraParams($this->accessToken), $responseParams);

        return $responseParams;
    }
    public function generateTokenByRefreshToken()
    {
    }
}
