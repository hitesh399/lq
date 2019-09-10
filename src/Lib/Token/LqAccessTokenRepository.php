<?php

namespace Singsys\LQ\Lib\Token;

use Laravel\Passport\Bridge\AccessTokenRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use DateTime;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;

class LqAccessTokenRepository extends AccessTokenRepository
{

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $device_id = request()->device()->id;

        Token::where('device_id', $device_id)
            ->where('user_id', $accessTokenEntity->getUserIdentifier())
            ->where('client_id', $accessTokenEntity->getClient()->getIdentifier())
            ->where('revoked', 0)->update(['revoked' => 1]);


        $this->tokenRepository->create([
            'id' => $accessTokenEntity->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'scopes' => $this->scopesToArray($accessTokenEntity->getScopes()),
            'revoked' => false,
            'device_id' => $device_id,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
            'expires_at' => $accessTokenEntity->getExpiryDateTime(),
        ]);

        $this->events->dispatch(new AccessTokenCreated(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getUserIdentifier(),
            $accessTokenEntity->getClient()->getIdentifier()
        ));
    }
}
