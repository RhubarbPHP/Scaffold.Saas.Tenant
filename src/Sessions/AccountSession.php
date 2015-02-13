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

namespace Gcd\Core\Saas\Tenant\Sessions;

use Gcd\Core\Context;
use Gcd\Core\Modelling\Repositories\MySql\MySql;
use Gcd\Core\Modelling\Repositories\Repository;
use Gcd\Core\Modelling\Schema\SolutionSchema;
use Gcd\Core\Saas\Tenant\RestClients\SaasGateway;
use Gcd\Core\Scaffolds\Saas\Model\SaasSolutionSchema;
use Gcd\Core\Sessions\EncryptedSession;

/**
 * Stores key details for the selected tenant.
 *
 * @property int $AccountID
 * @property string $AccountName
 * @property string $UniqueReference
 * @property string $ServerHost
 * @property string $ServerPort
 * @property string $CredentialsIV
 */
class AccountSession extends EncryptedSession
{
	/**
	 * Override to return the encryption key salt to use.
	 *
	 * @return mixed
	 */
	protected function GetEncryptionKeySalt()
	{
		$context = new Context();

		if ( isset( $_COOKIE[ "tsks" ] ) )
		{
			$keySalt = $_COOKIE[ "tsks" ];
		}
		else
		{
			/**
			 * Generate a keySalt that contains randomness and the exact time of creation.
			 */
			$keySalt = sha1( uniqid().mt_rand() );
		}

		if ( !$context->UnitTesting )
		{
			// The if test is required for unit testing due to the "output already having started" error.
			setcookie( "tsks", $keySalt, null, "/" );
		}

		$_COOKIE[ "tsks" ] = $keySalt;

		return $keySalt;
	}

	public function ConnectToAccount( $accountId )
	{
		$accountDetails = SaasGateway::GetAuthenticated( "/accounts/".$accountId );

		$this->AccountID = $accountId;
		$this->AccountName = $accountDetails->AccountName;
		$this->UniqueReference = $accountDetails->UniqueReference;
		$this->ServerHost = $accountDetails->Server->Host;
		$this->ServerPort = $accountDetails->Server->Port;
		$this->CredentialsIV = $accountDetails->CredentialsIV;
		$this->StoreSession();

		$repos = Repository::GetDefaultRepositoryClassName();

		// If the Repos is connected to an actual database we need to reset it to allow a new
		// connection to be created to the new account.
		if ( method_exists( $repos, "ResetDefaultConnection" ) )
		{
			$repos::ResetDefaultConnection();

			$solutionSchemas = SolutionSchema::GetAllSchemas();

			foreach( $solutionSchemas as $schema )
			{
				$schema->CheckModelSchemas();
			}
		}
	}
}