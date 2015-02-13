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

namespace Rhubarb\Crown\Saas\Tenant\Presenters\Accounts;

use Rhubarb\Leaf\Views\HtmlView;
use Rhubarb\Crown\Saas\Tenant\Sessions\AccountSession;

class AccountsListView extends HtmlView
{
	public $accounts = [];

	protected function ParseRequestForCommand()
	{
		if ( isset( $_GET[ "choose" ] ) )
		{
			$this->RaiseEvent( "SelectAccount", intval( $_GET[ "choose" ] ) );
		}

		parent::ParseRequestForCommand();
	}

	protected function PrintViewContent()
    {
	    if ( sizeof( $this->accounts ) )
	    {
		    $accountSession = new AccountSession();

			foreach( $this->accounts as $account )
			{
				print "<a href='?choose=".$account->AccountID."'>".$account->AccountName;

				if ( $accountSession->AccountID == $account->AccountID )
				{
					print " - selected";
				}

				print "</a><br/>";
			}
	    }
	    else
	    {
		    print "<p>You don't have any accounts yet.</p>";
	    }
    }
}