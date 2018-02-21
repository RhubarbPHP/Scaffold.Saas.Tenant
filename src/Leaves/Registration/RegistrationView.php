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

use Rhubarb\Leaf\Controls\Common\Buttons\Button;
use Rhubarb\Leaf\Controls\Common\Text\PasswordTextBox;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Leaf\Views\View;

class RegistrationView extends View
{
    /**
     * @var RegistrationModel
     */
    protected $model;

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            new TextBox("forename"),
            new TextBox("surname"),
            new TextBox("email"),
            new PasswordTextBox("newPassword"),
            new PasswordTextBox("newPasswordConfirm"),
            $submit = new Button("Signup", "Sign Up", function () {
                $this->model->createUserEvent->raise();
            })
        );
    }

    protected function printViewContent()
    {
        parent::printViewContent();

        if (!$this->model->revoked) {
            $this->layoutItemsWithContainer(
                "",
                [
                    "forename",
                    "surname",
                    "email",
                    "Password" => "newPassword",
                    "Confirm Password" => "newPasswordConfirm"
                ]
            );
        } else {
            print "Sorry this invite has expired.";
        }

        print $this->leaves["Signup"];
    }
}