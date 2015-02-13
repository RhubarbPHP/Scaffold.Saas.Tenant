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

namespace Rhubarb\Crown\Saas\Tenant\Sessions;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Crown\Saas\Tenant\Exceptions\SaasConnectionException;
use Rhubarb\Crown\Saas\Tenant\UnitTesting\TenantTestCase;

class AccountSessionTest extends TenantTestCase
{
	public function testUsingSessionSetsCookie()
	{
		$_COOKIE = [];

		$session = new AccountSession();
		$session->AccountID = 1;
		$session->StoreSession();

		$this->assertArrayHasKey( "tsks", $_COOKIE, "Using the tenant session should drop a cookie" );

		$firstKey = $_COOKIE[ "tsks" ];

		$_COOKIE = [];

		$session = new AccountSession();
		$session->AccountID = 1;
		$session->StoreSession();

		$secondKey = $_COOKIE[ "tsks" ];

		$this->assertNotEquals( $firstKey, $secondKey, "A new key salt should be generated as the cookies have been cleared" );

		$session = new AccountSession();
		$session->AccountID = 1;
		$session->StoreSession();

		$thirdKey = $_COOKIE[ "tsks" ];

		$this->assertEquals( $secondKey, $thirdKey, "If a cookie exists it should have used the cookie as the key salt" );

		$encryption = EncryptionProvider::GetEncryptionProvider();
		$data = $session->ExportRawData();

		$this->assertEquals( 1, $encryption->Decrypt( $data[ "AccountID" ], $thirdKey ) );
	}

	public function testConnectingToAccountGetsCredentials()
	{
		$session = new AccountSession();

		$login = LoginProvider::GetDefaultLoginProvider();
		$login->LogOut();

		try
		{
			$session->ConnectToAccount( 1 );
			$this->fail( "You can't connect to an account - you're not logged in!" );
		}
		catch( SaasConnectionException $er )
		{
		}

		$this->Login();

		$session->ConnectToAccount( 1 );

		$this->assertEquals( "Widgets Co", $session->AccountName );
		$this->assertEquals( "1.2.3.4", $session->ServerHost );
		$this->assertEquals( "9876", $session->ServerPort );
	}
}
 