<?php

namespace Singsys\LQ\Lib;

use Cache;
use Laravel\Passport\ClientRepository as PassportClient;

class ClientRepository
{
    /**
     * Get Client Detail by primary id
     */
    public function getClient($client_id) {

        return Cache::rememberForever('lg_client.'.$client_id, function () use ($client_id) {
            return  app()->make(PassportClient::class)->find($client_id);
        });
    }
}
