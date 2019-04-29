<?php

namespace Singsys\LQ\Lib\Token;

use League\OAuth2\Server\Grant\PasswordGrant;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Bridge\AccessTokenRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
/**
 *
 */
class TokenGrant extends PasswordGrant
{

	public function __construct() {

		$dispatcher = app(\Illuminate\Contracts\Events\Dispatcher::class);
		$connection = app(\Illuminate\Database\Connection::class);
		$hash = app(\Illuminate\Hashing\HashManager::class);
        //dd($dispatcher);
        $token_repository = new \Laravel\Passport\TokenRepository();

        $accessTokenRepository = new AccessTokenRepository($token_repository, $dispatcher);

		// $refresh_token_repository = new RefreshTokenRepository($accessTokenRepository, $connection, $dispatcher);
		$refresh_token_repository = new RefreshTokenRepository( $connection, $dispatcher);

		$user_repository = new UserRepository($hash);

		parent::__construct($user_repository, $refresh_token_repository);

		$accessTokenRepository = new LqAccessTokenRepository($token_repository, $dispatcher);

		$this->setAccessTokenRepository($accessTokenRepository);
	}

	public function issueToken( \DateInterval $accessTokenTTL, ClientEntityInterface $client,  $userIdentifier, array $scopes = [] )
	{
		return $this->issueAccessToken( $accessTokenTTL, $client,$userIdentifier,$scopes);

	}

	public function issueRefreshAccessToken (AccessTokenEntityInterface $accessToken) {

		return $this->issueRefreshToken($accessToken);
	}
}
