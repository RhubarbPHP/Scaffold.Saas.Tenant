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

namespace Rhubarb\Crown\Saas\Tenant;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Crown\Module;
use Rhubarb\Leaf\UrlHandlers\MvpCollectionUrlHandler;
use Rhubarb\Crown\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;

class SaasTenantModule extends Module
{
	public function __construct()
	{
		parent::__construct();

		$this->namespace = __NAMESPACE__;
		$this->AddClassPath( __DIR__ );
	}

	protected function Initialise()
	{
		parent::Initialise();

		EncryptionProvider::SetEncryptionProviderClassName( '\Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider' );
		Repository::SetDefaultRepositoryClassName( '\Rhubarb\Crown\Saas\Tenant\Repositories\SaasMySqlRepository' );
	}

	protected function RegisterDependantModules()
	{
		parent::RegisterDependantModules();

		Module::RegisterModule( new AuthenticationWithRolesModule( '\Rhubarb\Crown\Saas\Tenant\LoginProviders\TenantLoginProvider' ) );
	}

	protected function RegisterUrlHandlers()
	{
		parent::RegisterUrlHandlers();

		$signUp = new ClassMappedUrlHandler( "\Rhubarb\Crown\Saas\Tenant\Presenters\Registration\RegistrationPresenter" );
		$signUp->SetPriority( 20 );

		$login = new ClassMappedUrlHandler( "\Rhubarb\Crown\Saas\Tenant\Presenters\Login\LoginPresenter", [
			"reset/" => new MvpCollectionUrlHandler( '\Rhubarb\Crown\Saas\Tenant\Presenters\Login\ResetPasswordPresenter', '\Rhubarb\Crown\Saas\Tenant\Presenters\Login\ConfirmResetPasswordPresenter' )
		] );

		$login->SetPriority( 20 );
		$login->SetName( "login" );

		$this->AddUrlHandlers(
		[
			"/accounts/" => new ClassMappedUrlHandler( "\Rhubarb\Crown\Saas\Tenant\Presenters\Accounts\AccountsListPresenter",
			[
				"new/" => new ClassMappedUrlHandler( '\Rhubarb\Crown\Saas\Tenant\Presenters\Accounts\NewAccountPresenter' )
			]),
			"/sign-up/" => $signUp,
			"/login/" => $login
		] );
	}
}