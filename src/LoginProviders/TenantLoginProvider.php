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

namespace Gcd\Core\Saas\Tenant\LoginProviders;

use Gcd\Core\Saas\Tenant\RestClients\AuthenticatedRestClient;
use Gcd\Core\Saas\Tenant\RestClients\SaasGateway;
use Gcd\Core\LoginProviders\LoginProvider;
use Gcd\Core\RestApi\Clients\RestHttpRequest;
use Gcd\Core\RestApi\Exceptions\RestAuthenticationException;
use Gcd\Core\Scaffolds\Saas\Model\SaasSolutionSchema;

/**
 * A login provider that understands when a user has logged into the saas system.
 *
 * Note it's name is to distinguish it from login providers referenced in the landlord.
 *
 * @package Core\Saas\Tenant\LoginProviders
 */
class TenantLoginProvider extends LoginProvider
{
	public function IsLoggedIn()
	{
		return parent::IsLoggedIn();
	}

	public function LogOut()
	{
		AuthenticatedRestClient::ClearToken();

		$this->LoggedInData = [];

		parent::LogOut();
	}

	/**
	 * Attempts to login by accessing the me resource.
	 *
	 * @param $username
	 * @param $password
	 * @return bool True if the login succeeded, False if it didn't
	 */
	public function Login( $username, $password )
	{
		$this->LogOut();

		try
		{
			$me = SaasGateway::GetAuthenticated( "/users/me", $username, $password );

			// Note we are note capturing the user id from the landlord system as we should not be using it
			// on the tenant anywhere. Email is our 'unique' handle for each user as far as the tenant is concerned.
			$this->LoggedIn = true;
			$this->LoggedInData =
				[
					"Forename" => $me->Forename,
					"Surname" => $me->Surname,
					"Email" => $me->Email
				];

			$this->StoreSession();

			return true;
		}
		catch( RestAuthenticationException $er )
		{
			return false;
		}
	}
}