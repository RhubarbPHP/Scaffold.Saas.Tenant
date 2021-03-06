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

use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\RestClientSettings;
use Rhubarb\RestApi\Clients\RestHttpRequest;

/**
 * A simple static class to simplify to process of talking to the landlord.
 *
 * The static methods contained here should allow for 90% of the communication required with the landlord.
 */
class SaasGateway
{
    public static function inviteUser($email)
    {
        $accountSession = new AccountSession();

        $uri = "/accounts/".$accountSession->AccountID."/invites";

        return self::postAuthenticated($uri, [
            "Email" => $email
        ]);
    }

    /**
     * Get's an array of users connected to the account the current user is connected to.
     */
    public static function getUsers()
    {
        $accountSession = new AccountSession();

        $uri = "/accounts/".$accountSession->AccountID."/users";

        return self::getAuthenticated($uri);
    }

    /**
     * Get's an array of users invited to the account the current user is connected to.
     */
    public static function getOutstandingInvites()
    {
        $accountSession = new AccountSession();

        $uri = "/accounts/".$accountSession->AccountID."/invites";

        return self::getAuthenticated($uri);
    }

    /**
     * Makes an unauthenticated GET request
     *
     * @param $uri
     * @return mixed
     */
    public static function getUnauthenticated($uri)
    {
        $client = self::getUnAuthenticatedRestClient();
        $request = new RestHttpRequest($uri, "get");

        return $client->makeRequest($request);
    }

    /**
     * POSTs to an unauthenticated resource
     *
     * @param $uri
     * @param $payload
     * @return mixed
     */
    public static function postUnauthenticated($uri, $payload)
    {
        $client = self::getUnAuthenticatedRestClient();
        $request = new RestHttpRequest($uri, "post", $payload);

        return $client->makeRequest($request);
    }

    /**
     * POSTs to an authenticated resource
     *
     * @param $uri
     * @param $payload
     * @return mixed
     */
    public static function postAuthenticated($uri, $payload)
    {
        $client = self::getAuthenticatedRestClient();
        $request = new RestHttpRequest($uri, "post", $payload);

        return $client->makeRequest($request);
    }

    /**
     * PUTs to an authenticated resource
     *
     * @param $uri
     * @param $payload
     * @return mixed
     * @throws \Rhubarb\RestApi\Exceptions\RestImplementationExceptio
     */
    public static function putAuthenticated($uri, $payload)
    {
        $client = self::getAuthenticatedRestClient();
        $request = new RestHttpRequest($uri, "put", $payload);

        return $client->makeRequest($request);
    }

    /**
     * DELETEs to an unauthenticated resource
     *
     * @param $uri
     * @return mixed
     */
    public static function deleteUnauthenticated($uri)
    {
        $client = self::getUnAuthenticatedRestClient();
        $request = new RestHttpRequest($uri, "delete");

        return $client->makeRequest($request);
    }

    /**
     * PUTs to an unauthenticated resource
     *
     * @param $uri
     * @param $payload
     * @return mixed
     */
    public static function putUnauthenticated($uri, $payload)
    {
        $client = self::getUnAuthenticatedRestClient();
        $request = new RestHttpRequest($uri, "put", $payload);

        return $client->makeRequest($request);
    }

    /**
     * Makes an authenticated GET request
     *
     * @param $uri
     * @param string $username Only used for logging in
     * @param string $password Only used for logging in
     * @return mixed
     */
    public static function getAuthenticated($uri, $username = "", $password = "")
    {
        $client = self::getAuthenticatedRestClient($username, $password);
        $request = new RestHttpRequest($uri, "get");

        return $client->makeRequest($request);
    }

    /**
     * Gets the unauthenticated rest client.
     *
     * @return UnAuthenticatedRestClient
     */
    private static function getUnAuthenticatedRestClient()
    {
        return new UnAuthenticatedRestClient(self::getApiUrl());
    }

    /**
     * Gets the authenticated rest client
     *
     * @param string $username
     * @param string $password
     * @return AuthenticatedRestClient
     */
    private static function getAuthenticatedRestClient($username = "", $password = "")
    {
        return new AuthenticatedRestClient(self::getApiUrl(), $username, $password);
    }

    /**
     * Gets the stub url for the api
     *
     * @return string
     */
    public static function getApiUrl()
    {
        $settings = new RestClientSettings();
        return $settings->ApiUrl;
    }

    public static function getUser( $username )
    {
        $client = self::getAuthenticatedRestClient();
        $request = new RestHttpRequest( "users/{$username}" );
        $response = $client->makeRequest( $request );
        die( $response );
    }
} 