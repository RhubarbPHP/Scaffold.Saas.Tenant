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

namespace Gcd\Core\Saas\Tenant\Repositories;

use Gcd\Core\Modelling\ModellingSettings;
use Gcd\Core\Modelling\Repositories\MySql\MySql;
use Gcd\Core\Saas\Tenant\Exceptions\SaasConnectionException;
use Gcd\Core\Saas\Tenant\Sessions\AccountSession;

class SaasMySqlRepository extends MySql
{
	public static function GetDefaultConnection()
	{
		if ( self::$defaultConnection === null )
		{
			$session = new AccountSession();

			if ( !$session->AccountID )
			{
				throw new SaasConnectionException( "The application isn't connected to a tenant" );
			}

			/**
			 * Change the modelling settings to those provided by our SaasConnection
			 */
			$db = new ModellingSettings();
			$db->Host = $session->ServerHost;
			$db->Port = $session->ServerPort;
			$db->Username = $session->UniqueReference;
			$db->Database = $session->UniqueReference;
			$db->Password = sha1( $session->UniqueReference.strrev( $session->CredentialsIV ) );
		}

		parent::GetDefaultConnection();

		return self::$defaultConnection;
	}
} 