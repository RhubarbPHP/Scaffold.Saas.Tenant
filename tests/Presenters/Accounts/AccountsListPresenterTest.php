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

namespace Rhubarb\Scaffolds\Saas\Tenant\Tests\Presenters\Accounts;

use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Leaf\Views\UnitTestView;
use Rhubarb\Scaffolds\Saas\Tenant\Presenters\Accounts\AccountsListPresenter;
use Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures\TenantTestCase;

class AccountsListPresenterTest extends TenantTestCase
{
	public function testAccountsRetrieved()
	{
		$presenter = new AccountsListPresenter();
		$mockView = new UnitTestView();

		$presenter->AttachMockView( $mockView );

		$loginProvider = LoginProvider::GetDefaultLoginProvider();
		$loginProvider->login( "unit-tester", "abc123" );

		$presenter->Test();

		$this->assertCount( 2, $mockView->accounts );

		$loginProvider = LoginProvider::GetDefaultLoginProvider();
		$loginProvider->login( "nigel", "abc123" );

		$presenter->Test();

		$this->assertCount( 1, $mockView->accounts );
	}
}
 