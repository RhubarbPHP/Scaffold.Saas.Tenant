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

namespace Gcd\Core\Saas\Tenant\UnitTesting;

use Gcd\Core\Encryption\EncryptionProvider;
use Gcd\Core\LoginProviders\LoginProvider;
use Gcd\Core\Saas\Tenant\RestClients\SaasGateway;
use Gcd\Core\Saas\Tenant\SaasTenantModule;
use Gcd\Core\Saas\Tenant\Settings\RestClientSettings;
use Gcd\Core\Context;
use Gcd\Core\CoreModule;
use Gcd\Core\Encryption\HashProvider;
use Gcd\Core\Integration\Http\HttpClient;
use Gcd\Core\Modelling\Repositories\Repository;
use Gcd\Core\Modelling\Schema\SolutionSchema;
use Gcd\Core\Module;
use Gcd\Core\Scaffolds\Saas\SaasModule;
use Gcd\Core\Scaffolds\Saas\UnitTesting\SaasTestCaseTrait;
use Gcd\Core\UnitTesting\CoreTestCase;

class TenantTestCase extends CoreTestCase
{
	use SaasTestCaseTrait;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		SolutionSchema::ClearSchemas();

		Module::ClearModules();
		Module::RegisterModule( new CoreModule() );
		Module::RegisterModule( new SaasModule() );
		Module::RegisterModule( new SaasTenantModule() );
		Module::InitialiseModules();

		Repository::SetDefaultRepositoryClassName( "\Gcd\Core\Modelling\Repositories\Offline\Offline" );

		\Gcd\Core\Layout\LayoutModule::DisableLayout();

		$context = new \Gcd\Core\Context();
		$context->UnitTesting = true;

		$request = Context::CurrentRequest();
		$request->Reset();

		HashProvider::SetHashProviderClassName( "\Gcd\Core\Encryption\Sha512HashProvider" );

		EncryptionProvider::SetEncryptionProviderClassName( '\Gcd\Core\Encryption\Aes256ComputedKeyEncryptionProvider' );


		// Make sure HTTP requests go the unit testing route.
		HttpClient::SetDefaultHttpClientClassName( '\Gcd\Core\Integration\Http\UnitTestingHttpClient' );

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