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

namespace Rhubarb\Scaffolds\Saas\Tenant\Exceptions;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;

class SaasNoTenantSelectedException extends SaasConnectionException
{
    public function __construct($privateMessage = "", \Exception $previous = null)
    {
        // If the user has a single account, we should auto connect them and redirect to the app.
        // This should provide a good user experience in nearly all cases.

        $accounts = Me::getAccounts();

        if ( count( $accounts ) === 1 )
        {
            $accountSession = new AccountSession();
            $accountSession->connectToAccount( $accounts[0]->_id );

            throw new ForceResponseException( new RedirectResponse("/app/") );
        }

        throw new ForceResponseException( new RedirectResponse("/accounts/") );
    }
}