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

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Leaf\Presenters\Forms\Form;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Crown\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Crown\Saas\Tenant\Settings\TenantSettings;

class AccountsListPresenter extends Form
{
    protected function CreateView()
    {
        return new AccountsListView();
    }

	protected function ApplyModelToView()
	{
		$accounts = SaasGateway::GetAuthenticated( "/users/me/accounts" );

		$this->view->accounts = $accounts->items;

		parent::ApplyModelToView();
	}

	protected function ConfigureView()
	{
		parent::ConfigureView();

		$this->view->AttachEventHandler( "SelectAccount", function( $accountId )
		{
			$accountSession = new AccountSession();
			$accountSession->ConnectToAccount( $accountId );

			$settings = new TenantSettings();

			throw new ForceResponseException( new RedirectResponse( $settings->DashboardUrl ) );
		});
	}
}