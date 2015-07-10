<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Scaffolds\Saas\Tenant\LoginProviders;

use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Scaffolds\Authentication\User;
use Rhubarb\Scaffolds\Saas\Tenant\Exceptions\SaasNoTenantSelectedException;
use Rhubarb\Scaffolds\Saas\Tenant\Repositories\SaasMySqlRepository\SaasMySqlRepository;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\AuthenticatedRestClient;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\RestApi\Exceptions\RestAuthenticationException;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\Equals;

/**
 * A login provider that understands when a user has logged into the saas system.
 *
 * Note it's name is to distinguish it from login providers referenced in the landlord.
 *
 * @package Core\Saas\Tenant\LoginProviders
 */
class TenantLoginProvider extends LoginProvider
{
    public function isLoggedIn()
    {
        return parent::isLoggedIn();
    }

    public function logOut()
    {
        AuthenticatedRestClient::clearToken();

        $this->LoggedInData = [];

        parent::logOut();
    }

    /**
     * Attempts to login by accessing the me resource.
     *
     * @param $username
     * @param $password
     * @return bool True if the login succeeded, False if it didn't
     */
    public function login($username, $password)
    {
        $this->logOut();

        try {
            $me = SaasGateway::getAuthenticated("/users/me", $username, $password);

            // Note we are note capturing the user id from the landlord system as we should not be using it
            // on the tenant anywhere. Email is our 'unique' handle for each user as far as the tenant is concerned.
            $this->LoggedIn = true;
            $this->LoggedInData =
                [
                    "Forename" => $me->Forename,
                    "Surname" => $me->Surname,
                    "Email" => $me->Email,
                    "Username" => $username
                ];

            $this->StoreSession();

            return true;
        } catch (RestAuthenticationException $er) {
            return false;
        }
    }

    public function GetUserOnCurrentTenant()
    {
        $session = new AccountSession();

        if (!$session->AccountID) {
            throw new SaasNoTenantSelectedException("The application isn't connected to a tenant");
        }

        $userEmail = $this->LoggedInData[ 'Email' ];
        try {
            $user = User::findFirst(
                new Equals( "Email", $userEmail )
            );
        } catch( RecordNotFoundException $ex ) {
            $user = new User();
            $user->Email =  $userEmail;
            $user->Enabled = true;
            $user->Username = $this->LoggedInData[ 'Username' ];
        }

        //update the user info
        $user->Forename = $this->LoggedInData[ 'Forename' ];
        $user->Surname = $this->LoggedInData[ 'Surname' ];
        $user->save();

        return $user;
    }
}