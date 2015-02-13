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

namespace Gcd\Core\Saas\Tenant\RestClients;

use Gcd\Core\Saas\Tenant\Sessions\RestSession;
use Gcd\Core\Saas\Tenant\Settings\RestClientSettings;
use Gcd\Core\RestApi\Clients\RestHttpRequest;

/**
 * A simple static class to simplify to process of talking to the landlord.
 *
 * The static methods contained here should allow for 90% of the communication required with the landlord.
 */
class SaasGateway
{
	/**
	 * Makes an unauthenticated GET request
	 *
	 * @param $uri
	 * @return mixed
	 */
	public static function GetUnauthenticated( $uri )
	{
		$client = self::GetUnAuthenticatedRestClient();
		$request = new RestHttpRequest( $uri, "get" );

		return $client->MakeRequest( $request );
	}

	/**
	 * POSTs to an unauthenticated resource
	 *
	 * @param $uri
	 * @param $payload
	 * @return mixed
	 */
	public static function PostUnauthenticated( $uri, $payload )
	{
		$client = self::GetUnAuthenticatedRestClient();
		$request = new RestHttpRequest( $uri, "post", $payload );

		return $client->MakeRequest( $request );
	}

	/**
	 * PUTs to an unauthenticated resource
	 *
	 * @param $uri
	 * @param $payload
	 * @return mixed
	 */
	public static function PutUnauthenticated( $uri, $payload )
	{
		$client = self::GetUnAuthenticatedRestClient();
		$request = new RestHttpRequest( $uri, "put", $payload );

		return $client->MakeRequest( $request );
	}

	/**
	 * Makes an authenticated GET request
	 *
	 * @param $uri
	 * @param string $username Only used for logging in
	 * @param string $password Only used for logging in
	 * @return mixed
	 */
	public static function GetAuthenticated( $uri, $username = "", $password = "" )
	{
		$client = self::GetAuthenticatedRestClient( $username, $password );
		$request = new RestHttpRequest( $uri, "get" );

		return $client->MakeRequest( $request );
	}

	/**
	 * Gets the unauthenticated rest client.
	 *
	 * @return UnAuthenticatedRestClient
	 */
	private static function GetUnAuthenticatedRestClient()
	{
		return new UnAuthenticatedRestClient( self::GetApiUrl() );
	}

	/**
	 * Gets the authenticated rest client
	 *
	 * @param string $username
	 * @param string $password
	 * @return AuthenticatedRestClient
	 */
	private static function GetAuthenticatedRestClient( $username = "", $password = "" )
	{
		return new AuthenticatedRestClient( self::GetApiUrl(), $username, $password );
	}

	/**
	 * Gets the stub url for the api
	 *
	 * @return string
	 */
	public static function GetApiUrl()
	{
		$settings = new RestClientSettings();
		return $settings->ApiUrl;
	}
} 