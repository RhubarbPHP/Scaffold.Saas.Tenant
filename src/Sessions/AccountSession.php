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

namespace Rhubarb\Scaffolds\Saas\Tenant\Sessions;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\LoginProviders\TenantLoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Crown\Sessions\EncryptedSession;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Stem\Schema\SolutionSchema;

/**
 * Stores key details for the selected tenant.
 *

 */
class AccountSession extends EncryptedSession
{
    public $accountId;
    public $accountName;
    public $serverHost;
    public $serverPort;
    public $credentialsIV;
    public $loggedInUserData;

    /**
     * Override to return the encryption key salt to use.
     *
     * @return mixed
     */
    protected function getEncryptionKeySalt()
    {
        if (isset($_COOKIE["tsks"])) {
            $keySalt = $_COOKIE["tsks"];
        } else {
            /**
             * Generate a keySalt that contains randomness and the exact time of creation.
             */
            $keySalt = sha1(uniqid() . mt_rand());
        }

        if (!Application::current()->unitTesting) {
            // The if test is required for unit testing due to the "output already having started" error.
            setcookie("tsks", $keySalt, null, "/");
        }

        $_COOKIE["tsks"] = $keySalt;

        return $keySalt;
    }

    public function connectToAccount($accountId)
    {
        $accountDetails = SaasGateway::getAuthenticated("/users/me/accounts/" . $accountId);

        $this->accountId = $accountId;
        $this->accountName = $accountDetails->AccountName;
        $this->serverHost = $accountDetails->Server->Host;
        $this->serverPort = $accountDetails->Server->Port;
        $this->credentialsIV = $accountDetails->CredentialsIV;
        $loggedInUserData = unserialize($this->loggedInUserData);
        $this->loggedInUserData = null;
        $this->storeSession();

        $repos = Repository::getDefaultRepositoryClassName();

        // If the Repos is connected to an actual database we need to reset it to allow a new
        // connection to be created to the new account.
        if (method_exists($repos, "ResetDefaultConnection")) {
            $repos::resetDefaultConnection();

            $solutionSchemas = SolutionSchema::getAllSchemas();

            foreach ($solutionSchemas as $schema) {
                $schema->checkModelSchemas();
            }

            Model::clearAllRepositories();
        }

        // Now that we are connected to the repo, we can safely have the login provider update the User

        /** @var TenantLoginProvider $loginProvider */
        if ( $loggedInUserData !== false ) {
            $loginProvider = LoginProvider::getProvider();
            $loginProvider->setLoggedInUserIdentifierFromLandlordData($loggedInUserData);
        }
    }
}