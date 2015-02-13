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

namespace Rhubarb\Crown\Saas\Tenant\Presenters\Login;

use Rhubarb\Crown\Integration\Email\Email;
use Rhubarb\Crown\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Crown\Scaffolds\Authentication\User;

/**
 * Overrides the normal password reset presenter as we must intercept the reset request to pass it to the landlord.
 */
class ResetPasswordPresenter extends \Rhubarb\Crown\Scaffolds\Authentication\Presenters\ResetPasswordPresenter
{
	protected function InitiateResetPassword()
	{
		$data = new \stdClass();
		$data->Username = $this->model->Username;

		$response = SaasGateway::PostUnauthenticated( "/users/password-reset-invitations", $data );

		// If a user could not be found the $response could be null. For security reasons we
		// just pretend things went okay and simply skip the following process.
		if ( $response != null )
		{
			$emailData = [ "PasswordResetHash" => $response->PasswordResetHash ];

			$resetPasswordEmailClass = $this->_resetPasswordInvitationEmailClassName;

			/**
			 * @var Email $resetPasswordEmail
			 */
			$resetPasswordEmail = new $resetPasswordEmailClass( $emailData );
			$resetPasswordEmail->AddRecipient( $response->Email, $response->FullName );
			$resetPasswordEmail->Send();
		}
	}
} 