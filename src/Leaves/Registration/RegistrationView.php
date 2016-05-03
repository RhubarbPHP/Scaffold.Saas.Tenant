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

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Registration;

use Rhubarb\Leaf\Leaves\Controls\Buttons\Button;
use Rhubarb\Leaf\Leaves\Controls\Text\Password\Password;
use Rhubarb\Leaf\Leaves\Controls\Text\TextBox\TextBox;
use Rhubarb\Leaf\Views\View;

class RegistrationView extends View
{
    protected function configureLeaves()
    {
        parent::configureLeaves();

        $this->registerSubLeaf(
            new TextBox("Forename"),
            new TextBox("Surname"),
            new TextBox("Email", 80),
            new TextBox("Username"),
            new Password("NewPassword"),
            new Password("NewPasswordConfirm"),
            $submit = new Button("Signup", "Sign Up", function () {
                $this->raiseEvent("CreateUser");
            })
        );
    }

    protected function printViewContent()
    {
        parent::printViewContent();

        $this->layoutItemsWithContainer(
            "",
            [
                "Forename",
                "Surname",
                "Email",
                "Username",
                "Password" => "NewPassword",
                "Confirm Password" => "NewPasswordConfirm"
            ]
        );

        print $this->Leaves["Signup"];
    }
}