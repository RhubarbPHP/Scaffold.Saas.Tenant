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

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Accounts;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\TenantSettings;
use Rhubarb\Leaf\Presenters\Forms\Form;

class AccountsListPresenter extends Form
{
    protected function createView()
    {
        return new AccountsListView();
    }

    protected function applyModelToView()
    {
        $this->view->accounts = Me::getAccounts();

        parent::applyModelToView();
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler("SelectAccount", function ($accountId) {
            $accountSession = new AccountSession();
            $accountSession->connectToAccount($accountId);

            $settings = new TenantSettings();

            throw new ForceResponseException(new RedirectResponse($settings->DashboardUrl));
        });
    }
}