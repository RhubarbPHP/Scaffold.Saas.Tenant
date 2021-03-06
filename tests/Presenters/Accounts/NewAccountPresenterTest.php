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

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Leaf\Views\UnitTestView;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Scaffolds\Saas\Tenant\Presenters\Accounts\NewAccountPresenter;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures\TenantTestCase;

class NewAccountPresenterTest extends TenantTestCase
{
	public function testAccountCreates()
	{
		$this->loginWithMultipleAccounts();

		$presenter = new NewAccountPresenter();
		$view = new UnitTestView();
		$presenter->AttachMockView( $view );

		$presenter->AccountName = "Widget Factory";

		try
		{
			$view->SimulateEvent( "CreateAccount" );
			$this->fail( "Creating an account should cause a redirection" );
		}
		catch( ForceResponseException $er )
		{}

		$lastAccount = Account::FindLast();

		$this->assertEquals( "Widget Factory", $lastAccount->AccountName );

		// Check that the new account has been selected.
		$tenantSession = new AccountSession();

		$this->assertEquals( $lastAccount->UniqueIdentifier, $tenantSession->AccountID, "Upon creation, a new account should be selected automatically" );
	}

}
 