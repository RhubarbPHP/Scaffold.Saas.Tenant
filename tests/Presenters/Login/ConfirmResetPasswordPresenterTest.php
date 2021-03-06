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

namespace Rhubarb\Scaffolds\Saas\Tenant\Tests\Presenters\Login;

use Rhubarb\Leaf\Views\UnitTestView;
use Rhubarb\Scaffolds\Saas\Tenant\Presenters\Login\ConfirmResetPasswordPresenter;
use Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures\TenantTestCase;

class ConfirmResetPasswordPresenterTest extends TenantTestCase
{
	public function testResetHappens()
	{
		$oldPassword = $this->nigel->Password;

		$hash = $this->nigel->generatePasswordResetHash();

		$mvp = new ConfirmResetPasswordPresenter();
		$view = new UnitTestView();
		$mvp->AttachMockView( $view );

		$mvp->ItemIdentifier = $hash;
		$mvp->NewPassword = "def324";

		$view->simulateEvent( "ConfirmPasswordReset" );

		$this->nigel->reload();

		$this->assertNotEquals( $oldPassword, $this->nigel->Password, "The password should have changed." );
	}
}
 