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

namespace Gcd\Core\Saas\Tenant\Presenters\Login;

use Gcd\Core\Integration\Email\UnitTestingEmailProvider;
use Gcd\Core\Mvp\Views\UnitTestView;
use Gcd\Core\Saas\Tenant\UnitTesting\TenantTestCase;

class ResetPasswordPresenterTest extends TenantTestCase
{
	public function testPasswordResetInvitation()
	{
		$view = new UnitTestView();

		$presenter = new ResetPasswordPresenter();
		$presenter->AttachMockView( $view );

		$presenter->model->Username = "nigel";
		$view->SimulateEvent( "ResetPassword" );

		$email = UnitTestingEmailProvider::GetLastEmail();

		$this->assertEquals( "Your password reset invitation.", $email->GetSubject() );

		$this->_nigel->Reload();

		$this->assertContains( $this->_nigel->PasswordResetHash, $email->GetText() );

		$this->assertEquals( "Nigel Stevenson", $email->GetRecipients()["bignige@ut.com" ]->name );
	}
}
 