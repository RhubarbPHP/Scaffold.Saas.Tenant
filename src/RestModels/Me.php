<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\RestModels;

use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

class Me extends User
{
    public function __construct($restResourceId = null)
    {
        parent::__construct('me');
    }

    /**
     * Gets the users outstanding invitations.
     *
     * @param null $newInvitationCode If a new invitation code is being redeemed this is passed here to the landlord.
     * @return mixed
     */
    public static function getInvites($newInvitationCode = null)
    {
        $url = "/users/me/invites";

        if ($newInvitationCode){
            $url .= "?invitation=".$newInvitationCode;
        }

        $accounts = SaasGateway::getAuthenticated($url);

        return $accounts->items;
    }

    public static function acceptInvite($inviteId)
    {
        $payload =
            [
                "Accepted" => true
            ];

        SaasGateway::putAuthenticated("/users/me/invites/".$inviteId, $payload );
    }

    public static function getAccounts()
    {
        $accounts = SaasGateway::getAuthenticated("/users/me/accounts");

        return $accounts->items;
    }
}