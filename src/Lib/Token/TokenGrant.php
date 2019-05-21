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

		$refresh_token_repository = app(RefreshTokenRepository::class);
		$user_repository =  app(UserRepository::class);
		parent::__construct($user_repository, $refresh_token_repository);
		$accessTokenRepository = app(LqAccessTokenRepository::class);
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
