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
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\TenantSettings;

class AccountsList extends Leaf
{
    protected function onAccountSelected()
    {
        $settings = TenantSettings::singleton();
        throw new ForceResponseException(new RedirectResponse($settings->dashboardUrl));
    }

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return AccountsListView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new AccountsListModel();

        $request = Request::current();
        $invitation = $request->get("i");

        $model->accounts = Me::getAccounts();
        $model->invites = Me::getInvites($invitation);

        $model->selectAccountEvent->attachHandler(function ($accountId) {
            $accountSession = AccountSession::singleton();
            $accountSession->connectToAccount($accountId);
            $this->onAccountSelected();
        });

        $model->acceptInviteEvent->attachHandler(function ($inviteId) {
            Me::acceptInvite($inviteId);
        });

        return $model;
    }
}