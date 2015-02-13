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

namespace Gcd\Core\Saas\Tenant\Presenters\Registration;

use Gcd\Core\Exceptions\ForceResponseException;
use Gcd\Core\Response\RedirectResponse;
use Gcd\Core\Saas\Tenant\LoginProviders\TenantLoginProvider;
use Gcd\Core\Saas\Tenant\RestModels\User;
use Gcd\Core\LoginProviders\LoginProvider;
use Gcd\Core\Mvp\Presenters\Forms\Form;
use Gcd\Core\Saas\Tenant\Settings\TenantSettings;

class RegistrationPresenter extends Form
{
	private function CreateUser()
	{
		// Assumes the model has been populated with all the various settings.
		$user = new User();

		$user->Forename = $this->model->Forename;
		$user->Surname = $this->model->Surname;
		$user->Username = $this->model->Username;
		$user->Email = $this->model->Email;
		$user->NewPassword = $this->model->NewPassword;

		$loggedIn = false;

		try
		{
			$user->Save();

			$loginProvider = LoginProvider::GetDefaultLoginProvider();
			$loggedIn = $loginProvider->Login( $this->model->Username, $this->model->NewPassword );

			$settings = new TenantSettings();
		}
		catch( \Exception $er )
		{
			/// TODO: What happens now?
		}

		if ( $loggedIn )
		{
			throw new ForceResponseException( new RedirectResponse( $settings->PostRegistrationUrl ) );
		}
	}

	protected function ConfigureView()
	{
		parent::ConfigureView();

		$this->view->AttachEventHandler( "CreateUser", function()
		{
			$this->CreateUser();
		});
	}

	protected function CreateView()
	{
		return new RegistrationView();
	}
} 