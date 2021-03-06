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

use Rhubarb\Crown\Context;
use Rhubarb\Scaffolds\Authentication\LoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\LoginProviders\TenantLoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Crown\Scaffolds\Saas\Model\SaasSolutionSchema;
use Rhubarb\Crown\Sessions\EncryptedSession;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Stem\Schema\SolutionSchema;

/**
 * Stores key details for the selected tenant.
 *
 * @property int $AccountID
 * @property string $AccountName
 * @property string $ServerHost
 * @property string $ServerPort
 * @property string $CredentialsIV
 * @property object $LoggedInUserData
 */
class AccountSession extends EncryptedSession
{
    /**
     * Override to return the encryption key salt to use.
     *
     * @return mixed
     */
    protected function getEncryptionKeySalt()
    {
        $context = new Context();

        if (isset($_COOKIE["tsks"])) {
            $keySalt = $_COOKIE["tsks"];
        } else {
            /**
             * Generate a keySalt that contains randomness and the exact time of creation.
             */
            $keySalt = sha1(uniqid() . mt_rand());
        }

        if (!$context->UnitTesting) {
            // The if test is required for unit testing due to the "output already having started" error.
            setcookie("tsks", $keySalt, null, "/");
        }

        $_COOKIE["tsks"] = $keySalt;

        return $keySalt;
    }

    public function connectToAccount($accountId)
    {
        $accountDetails = SaasGateway::getAuthenticated("/users/me/accounts/" . $accountId);

        $this->AccountID = $accountId;
        $this->AccountName = $accountDetails->AccountName;
        $this->ServerHost = $accountDetails->Server->Host;
        $this->ServerPort = $accountDetails->Server->Port;
        $this->CredentialsIV = $accountDetails->CredentialsIV;
        $loggedInUserData = unserialize($this->LoggedInUserData);
        $this->LoggedInUserData = null;
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
            $loginProvider = LoginProvider::getDefaultLoginProvider();
            $loginProvider->setLoggedInUserIdentifierFromLandlordData($loggedInUserData);
        }
    }
}