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

use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Leaf\Views\UnitTestView;
use Rhubarb\Scaffolds\Saas\Tenant\Presenters\Login\ResetPasswordPresenter;
use Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures\TenantTestCase;

class ResetPasswordPresenterTest extends TenantTestCase
{
    public function testPasswordResetInvitation()
    {
        $view = new UnitTestView();

        $presenter = new ResetPasswordPresenter();
        $presenter->attachMockView($view);

        $presenter->model->Username = "nigel";
        $view->SimulateEvent("ResetPassword");

        $email = UnitTestingEmailProvider::getLastEmail();

        $this->assertEquals("Your password reset invitation.", $email->getSubject());

        $this->nigel->reload();

        $this->assertContains($this->nigel->PasswordResetHash, $email->getText());

        $this->assertEquals("Nigel Stevenson", $email->getRecipients()["bignige@ut.com"]->name);
    }
}
 