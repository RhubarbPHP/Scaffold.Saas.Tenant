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

namespace Gcd\Core\Saas\Tenant\Presenters\Registration;

use Gcd\Core\Exceptions\ForceResponseException;
use Gcd\Core\Response\RedirectResponse;
use Gcd\Core\Saas\Tenant\Settings\TenantSettings;
use Gcd\Core\Saas\Tenant\UnitTesting\TenantTestCase;
use Gcd\Core\Mvp\Views\UnitTestView;
use Gcd\Core\LoginProviders\LoginProvider;
use Gcd\Core\Scaffolds\AuthenticationWithRoles\User;

class RegistrationPresenterTest extends TenantTestCase
{
	public function testRegistrationCreatesUser()
	{
		$view = new UnitTestView();

		$presenter = new RegistrationPresenter();
		$presenter->AttachMockView( $view );

		$presenter->Forename = "Jenny";
		$presenter->Surname = "Smith";
		$presenter->Username = "jsmith";
		$presenter->Email = "jsmith@hotmail.com";
		$presenter->NewPassword = "abc123";

		try
		{
			$view->SimulateEvent( "CreateUser" );
		}
		catch( ForceResponseException $er ){}

		$user = User::FindLast();

		$this->assertEquals( "Jenny", $user->Forename );
		$this->assertEquals( "Smith", $user->Surname );
		$this->assertTrue( $user->Enabled, "New users should be enabled" );
	}

	public function testRegistrationLogsYouIn()
	{
		$view = new UnitTestView();

		$presenter = new RegistrationPresenter();
		$presenter->AttachMockView( $view );

		$presenter->Forename = "Jenny";
		$presenter->Surname = "Smith";
		$presenter->Username = "jsmith";
		$presenter->Email = "jsmith@hotmail.com";
		$presenter->NewPassword = "abc123";

		try
		{
			$view->SimulateEvent( "CreateUser" );
		}
		catch( ForceResponseException $er ){}

		$loginProvider = LoginProvider::GetDefaultLoginProvider();

		$this->assertTrue( $loginProvider->IsLoggedIn() );
	}

	public function testRegistrationRedirectsToAccounts()
	{
		$view = new UnitTestView();

		$presenter = new RegistrationPresenter();
		$presenter->AttachMockView( $view );

		$presenter->Forename = "Jenny";
		$presenter->Surname = "Smith";
		$presenter->Username = "jsmith";
		$presenter->Email = "jsmith@hotmail.com";
		$presenter->NewPassword = "abc123";

		try
		{
			$view->SimulateEvent( "CreateUser" );

			$this->fail( "Registration should redirect to the post registration url." );
		}
		catch( ForceResponseException $er )
		{
			/**
			 * @var RedirectResponse $response
			 */
			$response = $er->GetResponse();
			$url = $response->GetUrl();

			$tenantSettings = new TenantSettings();

			$this->assertEquals( $tenantSettings->PostRegistrationUrl, $url );
		}
	}
}
 