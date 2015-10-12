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

namespace Rhubarb\Scaffolds\Saas\Tenant;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Leaf\UrlHandlers\MvpCollectionUrlHandler;
use Rhubarb\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Scaffolds\Saas\Tenant\Model\TenantSolutionSchema;
use Rhubarb\Scaffolds\Saas\Tenant\Presenters\Users\UsersCollectionPresenter;
use Rhubarb\Scaffolds\Saas\Tenant\UrlHandlers\ValidateTenantConnectedUrlHandler;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Stem\Schema\SolutionSchema;

class SaasTenantModule extends Module
{
    protected function initialise()
    {
        parent::initialise();

        EncryptionProvider::setEncryptionProviderClassName('\Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider');
        Repository::setDefaultRepositoryClassName(__NAMESPACE__ . '\Repositories\SaasMySqlRepository\SaasMySqlRepository');
        ExceptionHandler::setExceptionHandlerClassName(__NAMESPACE__ . '\Exceptions\ExceptionHandlers\TenantExceptionHandler');

        SolutionSchema::registerSchema("TenantSolutionSchema", TenantSolutionSchema::class);
    }

    protected function registerDependantModules()
    {
        parent::registerDependantModules();

        Module::registerModule(new AuthenticationWithRolesModule('\Rhubarb\Scaffolds\Saas\Tenant\LoginProviders\TenantLoginProvider', '/app/'));
    }

    protected function registerUrlHandlers()
    {
        parent::registerUrlHandlers();

        $signUp = new ClassMappedUrlHandler("\Rhubarb\Scaffolds\Saas\Tenant\Presenters\Registration\RegistrationPresenter");
        $signUp->setPriority(20);

        $login = new ClassMappedUrlHandler("\Rhubarb\Scaffolds\Saas\Tenant\Presenters\Login\LoginPresenter", [
            "reset/" => new MvpCollectionUrlHandler('\Rhubarb\Scaffolds\Saas\Tenant\Presenters\Login\ResetPasswordPresenter',
                '\Rhubarb\Scaffolds\Saas\Tenant\Presenters\Login\ConfirmResetPasswordPresenter')
        ]);

        $login->setPriority(20);
        $login->setName("login");

        $accounts = new ClassMappedUrlHandler("\Rhubarb\Scaffolds\Saas\Tenant\Presenters\Accounts\AccountsListPresenter",
            [
                "new/" => new ClassMappedUrlHandler('\Rhubarb\Scaffolds\Saas\Tenant\Presenters\Accounts\NewAccountPresenter')
            ]);
        $accounts->setPriority( 1000 );

        $this->AddUrlHandlers(
            [
                "/accounts/" => $accounts,
                "/sign-up/" => $signUp,
                "/login/" => $login,
                "/app/" => new ValidateTenantConnectedUrlHandler(),
                "/app/users/" => new MvpCollectionUrlHandler(UsersCollectionPresenter::class, null)
            ]);
    }
}