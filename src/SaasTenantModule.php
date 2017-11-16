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
use Rhubarb\Crown\Module;
use Rhubarb\Crown\UrlHandlers\CallableUrlHandler;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\GreedyUrlHandler;
use Rhubarb\Leaf\UrlHandlers\LeafCollectionUrlHandler;
use Rhubarb\Scaffolds\Authentication\Settings\AuthenticationSettings;
use Rhubarb\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Scaffolds\Saas\Tenant\Custard\TenantSelectionRepositoryConnector;
use Rhubarb\Scaffolds\Saas\Tenant\Leaves\Accounts\AccountsList;
use Rhubarb\Scaffolds\Saas\Tenant\Leaves\Accounts\NewAccount;
use Rhubarb\Scaffolds\Saas\Tenant\Leaves\Login\ConfirmResetPassword;
use Rhubarb\Scaffolds\Saas\Tenant\Leaves\Login\Login;
use Rhubarb\Scaffolds\Saas\Tenant\Leaves\Login\ResetPassword;
use Rhubarb\Scaffolds\Saas\Tenant\Leaves\Registration\Registration;
use Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users\UsersCollection;
use Rhubarb\Scaffolds\Saas\Tenant\LoginProviders\TenantLoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\Model\TenantSolutionSchema;
use Rhubarb\Scaffolds\Saas\Tenant\UrlHandlers\ValidateTenantConnectedUrlHandler;
use Rhubarb\Stem\Custard\RequiresRepositoryCommand;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Stem\Schema\SolutionSchema;
use Symfony\Component\Console\Command\Command;

class SaasTenantModule extends Module
{
    private $loginProviderClassName;

    public function __construct($loginProviderClassName = "")
    {
        $this->loginProviderClassName = ($loginProviderClassName != "") ? $loginProviderClassName : TenantLoginProvider::class;

        parent::__construct();
    }

    protected function initialise()
    {
        parent::initialise();

        EncryptionProvider::setProviderClassName('\Rhubarb\Crown\Encryption\Aes256ComputedKeyEncryptionProvider');
        Repository::setDefaultRepositoryClassName(__NAMESPACE__ . '\Repositories\SaasMySqlRepository\SaasMySqlRepository');

        SolutionSchema::registerSchema("TenantSolutionSchema", TenantSolutionSchema::class);
    }

    protected function getModules()
    {
        return [
            new AuthenticationWithRolesModule($this->loginProviderClassName, '/app/')
        ];
    }

    private function getProvider()
    {
        $providerClassName = $this->loginProviderClassName;
        return $providerClassName::singleton();
    }


    protected function registerUrlHandlers()
    {
        parent::registerUrlHandlers();

        $signUp = new ClassMappedUrlHandler(Registration::class);
        $signUp->setPriority(20);

        $login = new CallableUrlHandler(function(){
            return new Login($this->getProvider());
            }, [
            "reset/" => new CallableUrlHandler(function(){
                return new ResetPassword($this->getProvider());
            },[
                '' => new GreedyUrlHandler(function($parentHandler, $captured){
                    return new ConfirmResetPassword($this->getProvider(), $captured);
                })
            ]),
        ]);

        $login->setPriority(20);
        $login->setName("login");

        $accounts = new ClassMappedUrlHandler(AccountsList::class,
            [
                "new/" => new ClassMappedUrlHandler(NewAccount::class)
            ]);

        $this->addUrlHandlers(
            [
                "/sign-up/" => $signUp,
                "/login/" => $login,
                "/app/" => new ValidateTenantConnectedUrlHandler(),
                "/app/accounts/" => $accounts,
                "/app/users/" => new LeafCollectionUrlHandler(UsersCollection::class, null)
            ]);
    }

    /**
     * An opportunity for the module to return a list custard command line commands to register.
     *
     * Note that modules are asked for commands in the same order in which the modules themselves
     * were registered. This allows extending modules or scaffolds to superseed a command with an
     * improved version by simply reregistering a command with the same name.
     *
     * @return Command[]
     */
    public function getCustardCommands()
    {
        RequiresRepositoryCommand::setRepositoryConnector(
            new TenantSelectionRepositoryConnector()
        );

        return [];
    }
}