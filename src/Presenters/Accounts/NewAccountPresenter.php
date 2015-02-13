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

namespace Gcd\Core\Saas\Tenant\Presenters\Accounts;

use Gcd\Core\Exceptions\ForceResponseException;
use Gcd\Core\Mvp\Presenters\Forms\Form;
use Gcd\Core\Response\RedirectResponse;
use Gcd\Core\Saas\Tenant\Sessions\AccountSession;
use Gcd\Core\Saas\Tenant\Settings\TenantSettings;
use Gcd\Core\Scaffolds\Saas\Model\Accounts\Account;

class NewAccountPresenter extends Form
{
	protected function CreateView()
    {
        return new NewAccountView();
    }

	protected function ConfigureView()
	{
		parent::ConfigureView();

		$this->view->AttachEventHandler( "CreateAccount", function()
		{
			$account = new \Gcd\Core\Saas\Tenant\RestModels\Account();
			$account->AccountName = $this->model->AccountName;
			$account->Save();

			$session = new AccountSession();
			$session->ConnectToAccount( $account->AccountID );

			$settings = new TenantSettings();
			$response = new RedirectResponse( $settings->DashboardUrl );

			throw new ForceResponseException( $response );
		});
	}


}