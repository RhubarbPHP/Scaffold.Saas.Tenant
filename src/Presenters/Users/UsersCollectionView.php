<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Users;

use Rhubarb\Leaf\Views\HtmlView;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

class UsersCollectionView extends HtmlView
{
    protected function printViewContent()
    {
        parent::printViewContent();

        // Get the list of users and show them.
        $users = SaasGateway::getUsers();

        foreach( $users->items as $user ){
            print $user->Username;
        }
    }

}