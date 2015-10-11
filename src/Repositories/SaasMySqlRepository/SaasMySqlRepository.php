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

namespace Rhubarb\Scaffolds\Saas\Tenant\Repositories\SaasMySqlRepository;

use Rhubarb\Scaffolds\Saas\Tenant\Exceptions\SaasNoTenantSelectedException;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Stem\Repositories\MySql\MySql;
use Rhubarb\Stem\StemSettings;

class SaasMySqlRepository extends MySql
{
    public static function getDefaultConnection()
    {
        if (self::$defaultConnection === null) {

            self::assertConnectedToTenant();

            $session = new AccountSession();

            /**
             * Change the modelling settings to those provided by our SaasConnection
             */
            $db = new StemSettings();
            $db->Host = $session->ServerHost;
            $db->Port = $session->ServerPort;
            $db->Username = $session->AccountID;
            $db->Database = $session->AccountID;
            $db->Password = sha1($session->AccountID . strrev($session->CredentialsIV));
        }

        parent::getDefaultConnection();

        return self::$defaultConnection;
    }

    /**
     * Simply verifies a connection to a tenant is currently made and if not throws an exception.
     *
     * @throws SaasNoTenantSelectedException
     */
    public static function assertConnectedToTenant()
    {
        $session = new AccountSession();

        if (!$session->AccountID) {
            throw new SaasNoTenantSelectedException("The application isn't connected to a tenant");
        }
    }
}