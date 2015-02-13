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

namespace Rhubarb\Crown\Saas\Tenant\RestClients;

use Rhubarb\Crown\RestApi\Exceptions\RestAuthenticationException;
use Rhubarb\Crown\Saas\Tenant\Exceptions\SaasAuthenticationException;
use Rhubarb\Crown\Saas\Tenant\Sessions\RestSession;
use Rhubarb\Crown\RestApi\Clients\TokenAuthenticatedRestClient;

class AuthenticatedRestClient extends TokenAuthenticatedRestClient
{
	public function __construct($apiUrl, $username, $password )
	{
		$existingToken = self::GetStoredToken();

		if ( $username == "" && $password == "" && $existingToken == "" )
		{
			throw new SaasAuthenticationException( "The authenticated client requires credentials to make it's request." );
		}

		parent::__construct($apiUrl, $username, $password, "/tokens", $existingToken );
	}

	/**
	 * Stores the token in the session.
	 *
	 * @param $token
	 */
	protected function OnTokenReceived($token)
	{
		$session = new RestSession();
		$session->ApiToken = $token;
		$session->StoreSession();

		parent::OnTokenReceived($token);
	}

	/**
	 * Clears the stored token effectively logging you out.
	 */
	public static function ClearToken()
	{
		$session = new RestSession();

		unset( $session->ApiToken );

		$session->StoreSession();
	}

	/**
	 * Gets an existing token stored in the session.
	 *
	 * @return mixed|string
	 */
	private static function GetStoredToken()
	{
		$session = new RestSession();

		if ( isset( $session->ApiToken ) )
		{
			return $session->ApiToken;
		}

		return "";
	}
}