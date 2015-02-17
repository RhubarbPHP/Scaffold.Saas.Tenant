<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\RestModels;

use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

class Me extends User
{
    protected function getResourceUri()
    {
        return "/users/me";
    }

    public static function getAccounts()
    {
        $accounts = SaasGateway::getAuthenticated("/users/me/accounts");

        return $accounts->items;
    }
}