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

namespace Rhubarb\Scaffolds\Saas\Tenant\RestClients;

use Rhubarb\RestApi\Clients\RestHttpRequest;
use Rhubarb\RestApi\Clients\TokenAuthenticatedRestClient;
use Rhubarb\RestApi\Exceptions\RestResponseException;
use Rhubarb\Scaffolds\Saas\Tenant\Exceptions\SaasAuthenticationException;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\RestSession;

class AuthenticatedRestClient extends TokenAuthenticatedRestClient
{
    public function __construct($apiUrl, $username, $password)
    {
        $existingToken = self::getStoredToken();

        if ($username == "" && $password == "" && $existingToken == "") {
            throw new SaasAuthenticationException("The authenticated client requires credentials to make it's request.");
        }

        parent::__construct($apiUrl, $username, $password, "/tokens", $existingToken);
    }

    /**
     * Stores the token in the session.
     *
     * @param $token
     */
    protected function onTokenReceived($token)
    {
        $session = new RestSession();
        $session->ApiToken = $token;
        $session->storeSession();

        parent::onTokenReceived($token);
    }

    /**
     * Clears the stored token effectively logging you out.
     */
    public static function clearToken()
    {
        $session = new RestSession();

        unset($session->ApiToken);

        $session->storeSession();
    }

    /**
     * Gets an existing token stored in the session.
     *
     * @return mixed|string
     */
    private static function getStoredToken()
    {
        $session = new RestSession();

        if (isset($session->ApiToken)) {
            return $session->ApiToken;
        }

        return "";
    }

    public function makeRequest(RestHttpRequest $request)
    {
        try {
            return parent::makeRequest($request);
        } catch (RestResponseException $ex) {
            throw new TenantRestClientResponseException($ex->getMessage(), $ex->response);
        }
    }


}
