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

use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Context;
use Rhubarb\Crown\LoginProviders\Exceptions\LoginFailedException;
use Rhubarb\RestApi\Exceptions\RestAuthenticationException;
use Rhubarb\Scaffolds\Authentication\LoginProvider;
use Rhubarb\Scaffolds\Authentication\User;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\AuthenticatedRestClient;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\RestSession;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Crown\Http\HttpResponse;
use Rhubarb\Stem\Schema\SolutionSchema;

/**
 * A login provider that understands when a user has logged into the saas system.
 *
 * Note it's name is to distinguish it from login providers referenced in the landlord.
 *
 * @package Core\Saas\Tenant\LoginProviders
 */
class TenantLoginProvider extends LoginProvider
{
    /**
     * Attempts to login by accessing the me resource.
     *
     * @param $username
     * @param $password
     * @return bool True if the login succeeded, False if it didn't
     * @throws LoginFailedException
     */
    public function login($username, $password)
    {
        $this->logOut();

        try {
            $this->getMe($username,$password);

            return true;
        } catch (RestAuthenticationException $er) {
            Log::debug( "Saas login failed for {$username} - the credentials were rejected by the landlord", "LOGIN" );
            throw new LoginFailedException();
        }
    }

    private function getMe( $username = "", $password = "" )
    {
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
                    "Username" => $username,
                    "Token" => $me->Token
                ];

            $this->storeSession();

            $accountSession = new AccountSession();
            // Place the me resource temporarily in the AccountSession so that it can update a tenant
            // once connected. This is not a duplication of the code above.
            $accountSession->LoggedInUserData = serialize( $me );
            $accountSession->storeSession();

            return true;
        } catch (RestAuthenticationException $er) {
            Log::debug("Saas login failed for {$username} - the credentials were rejected by the landlord", "LOGIN");
            throw new LoginFailedException();
        }
    }

    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        // If we're not logged in, let's see if we can auto login using a saved token.
        if (!$this->isLoggedIn()) {
            $request = Context::currentRequest();

            if ($request->cookie('ltk') != "") {
                $token = $request->cookie('ltk');

                $session = new RestSession();
                $session->ApiToken = $token;
                $session->storeSession();

                $this->getMe();
            }
        }
    }

    /**
     * @param object $data
     *
     * @return User|\Rhubarb\Stem\Models\Model
     * @throws \Exception
     * @throws \Rhubarb\Stem\Exceptions\ModelConsistencyValidationException
     */
    protected function createUpdateUserFromLandlordData($data)
    {
        $userClass = SolutionSchema::getModelClass('User');
        try {
            $user = $this->loadUserFromLandlordData($userClass, $data);
        } catch (RecordNotFoundException $ex) {
            $user = $this->createUserFromLandlordData($userClass, $data);
        }

        $this->updateUserFromLandlordData($user, $data);

        // Force save as sometimes it's on login that other models are refreshed with the logged in user
        // details.
        $user->save(true);

        return $user;
    }

    /**
     * @param string $userClassName
     * @param object $data
     *
     * @return User
     * @throws RecordNotFoundException
     */
    protected function loadUserFromLandlordData($userClassName, $data)
    {
        return $userClassName::findByUUID(
            $data->UUID
        );
    }

    /**
     * @param string $userClassName
     * @param object $data
     *
     * @return User
     */
    protected function createUserFromLandlordData($userClassName, $data)
    {
        $user = new $userClassName();
        $user->UUID = $data->UUID;

        return $user;
    }

    /**
     * @param User   $user
     * @param object $data
     */
    protected function updateUserFromLandlordData($user, $data)
    {
        $user->Email = $data->Email;
        $user->Forename = $data->Forename;
        $user->Surname = $data->Surname;
        $user->Username = $data->Username;
    }

    /**
     * @param object $data
     */
    public function setLoggedInUserIdentifierFromLandlordData($data)
    {
        $this->LoggedInUserIdentifier = $this->createUpdateUserFromLandlordData($data)->getUniqueIdentifier();
        $this->storeSession();
    }

    protected function detectRememberMe()
    {
        // Blank function to stop the broken base implementation of 'remember me'
        // TODO: Make remember me work in TenantLoginProvider
    }

    public function rememberLogin()
    {
        $session = new RestSession();

        HttpResponse::setCookie('ltk', $session->ApiToken );
    }

    protected function onLogOut()
    {
        AuthenticatedRestClient::clearToken();
        HttpResponse::unsetCookie('ltk');

        parent::onLogOut();
    }
}