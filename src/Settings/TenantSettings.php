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

namespace Rhubarb\Scaffolds\Saas\Tenant\Settings;

use Rhubarb\Crown\Settings;

/**
 * Contains settings which allow for easy configuration of a tenant system
 *
 * @property string $PostRegistrationUrl    The url where a user is redirected after registration
 * @property string $AccountsUrl    The url where a users accounts are listed.
 * @property string $DashboardUrl   The url of the main entry point to the app itself.
 */
class TenantSettings extends Settings
{
    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        $this->RegistrationUrl = "/sign-up/";
        $this->PostRegistrationUrl = "/accounts/";
        $this->AccountsUrl = "/accounts/";
        $this->DashboardUrl = "/app/";
    }
}