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

namespace Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\Encryption\HashProvider;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Scaffolds\Saas\Tenant\SaasTenantModule;
use Rhubarb\Scaffolds\Saas\Landlord\SaasLandlordModule;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasTestCaseTrait;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\RestClientSettings;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Stem\Schema\SolutionSchema;

class TenantTestCase extends RhubarbTestCase
{
    use SaasTestCaseTrait;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        SolutionSchema::clearSchemas();

        Module::clearModules();
        Module::registerModule(new SaasLandlordModule());
        Module::registerModule(new SaasTenantModule());
        Module::initialiseModules();

        Repository::setDefaultRepositoryClassName("\Rhubarb\Stem\Repositories\Offline\Offline");

        LayoutModule::disableLayout();

        $context = new Context();
        $context->UnitTesting = true;

        $request = Request::current();
        $request->reset();

        HashProvider::setProviderClassName("\Rhubarb\Crown\Encryption\Sha512HashProvider");

        EncryptionProvider::setProviderClassName('\Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider');


        // Make sure HTTP requests go the unit testing route.
        HttpClient::setProviderClassName('\Rhubarb\Crown\Tests\Fixtures\UnitTestingHttpClient');

        $restClientSettings = RestClientSettings::singleton();
        $restClientSettings->apiUrl = "/api";
    }

    protected function logout()
    {
        $login = LoginProvider::getProvider();
        $login->logOut();
    }


    /**
     * Login as unit-tester
     */
    protected function loginWithMultipleAccounts()
    {
        $login = LoginProvider::getProvider();
        $login->login("unit-tester", "abc123");
    }

    /**
     * Login as nigel
     */
    protected function loginWithSingleAccounts()
    {
        $login = LoginProvider::getProvider();
        $login->login("nigel", "abc123");
    }
} 