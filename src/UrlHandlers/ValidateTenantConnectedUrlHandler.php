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

namespace Rhubarb\Scaffolds\Saas\Tenant\UrlHandlers;

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\UrlHandlers\UrlHandler;
use Rhubarb\Scaffolds\Saas\Tenant\LoginProviders\TenantLoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\TenantSettings;

class ValidateTenantConnectedUrlHandler extends UrlHandler
{
    /**
     * Return the response if appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @return bool
     * @throws ForceResponseException Thrown if the user must be redirected to another page.
     */
    protected function generateResponseForRequest($request = null)
    {
        // If the user has a single account, we should auto connect them and redirect to the app.
        // This should provide a good user experience in nearly all cases.

        Container::singleton(TenantLoginProvider::class);

        $session = AccountSession::singleton();

        if ( !$session->accountId )
        {
            $accounts = Me::getAccounts();

            $tenantSettings = TenantSettings::singleton();

            if ( count( $accounts ) === 1 )
            {
                $accountSession = AccountSession::singleton();
                $accountSession->connectToAccount( $accounts[0]->_id );

                throw new ForceResponseException( new RedirectResponse($tenantSettings->dashboardUrl) );
            }

            throw new ForceResponseException( new RedirectResponse($tenantSettings->accountsUrl) );
        }

        return false;
    }
}