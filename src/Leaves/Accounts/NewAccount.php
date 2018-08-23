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

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Accounts;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Account;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\TenantSettings;

class NewAccount extends Leaf
{
    /** @var NewAccountModel $model */
    protected $model;

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return NewAccountView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new NewAccountModel();

        $model->createAccountEvent->attachHandler(
            function () {
                $this->createAccount();
                $settings = TenantSettings::singleton();
                $response = new RedirectResponse($settings->dashboardUrl);
                throw new ForceResponseException($response);
            }
        );

        return $model;
    }

    /**
     * @return Account $account
     * @throws \Rhubarb\RestApi\Exceptions\RestImplementationException
     *
     * Override to add project specific account setup steps. E.g. Menus / Permissions
     */
    protected function createAccount()
    {
        $account = new Account();
        $account->AccountName = $this->model->accountName;
        $account->save();

        $session = AccountSession::singleton();
        $session->connectToAccount($account->_id);

        return $account;
    }
}