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

namespace Rhubarb\Crown\Saas\Tenant\Tests\Fixtures;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Crown\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Crown\Saas\Tenant\SaasTenantModule;
use Rhubarb\Crown\Saas\Tenant\Settings\RestClientSettings;
use Rhubarb\Crown\Context;
use Rhubarb\Crown\Encryption\HashProvider;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Crown\Module;
use Rhubarb\Scaffolds\Saas\SaasModule;

class TenantTestCase extends RhubarbTestCase
{
	use SaasTestCaseTrait;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		SolutionSchema::ClearSchemas();

		Module::ClearModules();
		Module::RegisterModule( new SaasModule() );
		Module::RegisterModule( new SaasTenantModule() );
		Module::InitialiseModules();

		Repository::SetDefaultRepositoryClassName( "\Rhubarb\Stem\Repositories\Offline\Offline" );

		\Rhubarb\Crown\Layout\LayoutModule::DisableLayout();

		$context = new \Rhubarb\Crown\Context();
		$context->UnitTesting = true;

		$request = Context::CurrentRequest();
		$request->Reset();

		HashProvider::SetHashProviderClassName( "\Rhubarb\Crown\Encryption\Sha512HashProvider" );

		EncryptionProvider::SetEncryptionProviderClassName( '\Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider' );


		// Make sure HTTP requests go the unit testing route.
		HttpClient::SetDefaultHttpClientClassName( '\Rhubarb\Crown\Integration\Http\UnitTestingHttpClient' );

		$restClientSettings = new RestClientSettings();
		$restClientSettings->ApiUrl = "/api";
	}

	/**
	 * Login as nigel
	 */
	protected function Login()
	{
		$login = LoginProvider::GetDefaultLoginProvider();
		$login->Login( "unit-tester", "abc123" );
	}
} 