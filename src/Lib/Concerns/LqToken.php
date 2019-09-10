<?php

namespace Singsys\LQ\Lib\Concerns;

use Singsys\LQ\Lib\Token\AuthToken;

trait LqToken
{
    public function createUserToken()
    {
        $client_id = request()->client()->id;
        $device_id = request()->device()->id;
        $token = new AuthToken($client_id, $this->id);
        return $token->generateToken();
    }
}
