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

use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Leaf\Views\HtmlView;

class AccountsListView extends HtmlView
{
    public $accounts = [];
    public $invites = [];

    protected function parseRequestForCommand()
    {
        if (isset($_GET["choose"])) {
            $this->raiseEvent("SelectAccount", $_GET["choose"]);
        }

        if (isset($_GET["accept"])) {
            $this->raiseEvent("AcceptInvite", $_GET["accept"]);
        }

        parent::parseRequestForCommand();
    }

    protected function printViewContent()
    {
        if (sizeof($this->accounts)) {
            $accountSession = AccountSession::singleton();

            print "<h2>Connected Accounts</h2>";

            foreach ($this->accounts as $account) {
                print "<a href='?choose=" . $account->_id . "'>" . $account->AccountName;

                if ($accountSession->accountId == $account->_id) {
                    print " - selected";
                }

                print "</a><br/>";
            }
        } else {
            print "<p>You don't have any accounts yet.</p>";
        }

        if (sizeof($this->invites)) {

            print "<h2>Invitations</h2>
            <p>You've been invited to join the following accounts:</p>";

            foreach ($this->invites as $invite) {
                print $invite->Account->AccountName." - <a href='?accept=".$invite->_id."''>Accept</a><br/>";
            }
        }
    }
}