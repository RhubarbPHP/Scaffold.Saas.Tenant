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

namespace Rhubarb\Crown\Saas\Tenant\Tests\LoginProviders;

use Rhubarb\Crown\Saas\Tenant\UnitTesting\TenantTestCase;

class TenantLoginProviderTest extends TenantTestCase
{
	public function testLoginWorks()
	{
		$loginProvider = new TenantLoginProvider();

		$this->assertFalse( $loginProvider->isLoggedIn(), "I shouldn't be logged in before Login is called" );

		$result = $loginProvider->login( "unit-tester", "abc123" );

		$this->assertTrue( $result, "Login should have worked" );
		$this->assertTrue( $loginProvider->isLoggedIn(), "I should be logged in now" );

		$this->assertEquals( "ut@ut.com", $loginProvider->LoggedInData[ "Email" ] );
		$this->assertEquals( "Unit Tester", $loginProvider->LoggedInData[ "Forename" ] );

		$loginProvider->logOut();

		$this->assertNotContains( "Email", $loginProvider->LoggedInData );

		$this->assertFalse( $loginProvider->isLoggedIn(), "I just logged out. Can't be logged in" );

		$result = $loginProvider->login( "norma", "abc123" );

		$this->assertFalse( $result, "Norma isn't active - we shouldn't have logged in." );

		$loginProvider->login( "unit-tester", "abc123" );
		$loginProvider->login( "norma", "abc123" );

		$this->assertFalse( $loginProvider->isLoggedIn(), "Norma isn't active - we should be logged in." );
	}
}
 