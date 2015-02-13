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

use Gcd\Core\HttpHeaders;
use Gcd\Core\Modelling\Exceptions\RepositoryConnectionException;
use Gcd\Core\Modelling\ModellingSettings;
use Gcd\Core\Saas\Tenant\Sessions\AccountSession;
use Gcd\Core\Saas\Tenant\UnitTesting\TenantTestCase;
use Gcd\Core\Sessions\Session;

class SaasMySqlRepositoryTest extends TenantTestCase
{
	public function testRepositoryGetsConnectionDetails()
	{
		$this->Login();

		$session = new AccountSession();
		$session->ConnectToAccount( 1 );

		try
		{
			SaasMySqlRepository::GetDefaultConnection();
		}
		catch( RepositoryConnectionException $er )
		{
			// The connection will fail as our details are fake but that's okay, it will
			// have initiated the setup of the ModellingSettings class which we can use
			// for our assertions below.
		}

		// Examine the modelling settings to see if they've been set correctly.
		$settings = new ModellingSettings();

		$this->assertEquals( "1.2.3.4", $settings->Host );
		$this->assertEquals( "9876", $settings->Port );
		$this->assertEquals( "widgets-co", $settings->Username );
		$this->assertEquals( "widgets-co", $settings->Database );
		$this->assertEquals( sha1( $session->UniqueReference.strrev( $session->CredentialsIV ) ), $settings->Password );

	}
}
 