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

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Login;

use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;

class ConfirmResetPassword extends \Rhubarb\Scaffolds\Authentication\Leaves\ConfirmResetPassword
{
	/**
	 * Returns the name of the standard view used for this leaf.
	 *
	 * @return string
	 */
	protected function getViewClass()
	{
		return ConfirmResetPasswordView::class;
    }

	protected function confirmPasswordReset()
	{

		if ($this->model->newPassword == $this->model->confirmNewPassword && $this->model->newPassword != "") {
			try {
				$payload = new \stdClass();
				$payload->PasswordResetHash = $this->resetHash;
				$payload->NewPassword = $this->model->newPassword;

				$response = SaasGateway::putUnauthenticated( "/users/password-reset-invitations", $payload );
				
				if (isset($response->result) && !$response->result->status){
					$this->model->message = "HashInvalid";
					return false;
				} else {
					$this->model->message = "PasswordReset";
					return true;
				}
			} catch (RecordNotFoundException $ex) {
				$this->model->message = "UserNotRecognised";
				return false;
			}
		} else if ($this->model->newPassword == "") {
			$this->model->message = "PasswordEmpty";
			return false;
		} else {
			$this->model->message = "PasswordsDontMatch";
			return false;
		}
	}
}