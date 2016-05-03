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

use Rhubarb\Crown\Email\Email;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

/**
 * Overrides the normal password reset presenter as we must intercept the reset request to pass it to the landlord.
 */
class ResetPassword extends \Rhubarb\Scaffolds\Authentication\Leaves\ResetPassword
{
    protected function initiateResetPassword()
    {
        $data = new \stdClass();
        $data->Username = $this->model->Username;

        $response = SaasGateway::postUnauthenticated("/users/password-reset-invitations", $data);

        // If a user could not be found the $response could be null. For security reasons we
        // just pretend things went okay and simply skip the following process.
        if ($response != null) {
            $emailData = ["PasswordResetHash" => $response->PasswordResetHash];

            $resetPasswordEmailClass = $this->resetPasswordInvitationEmailClassName;

            /**
             * @var Email $resetPasswordEmail
             */
            $resetPasswordEmail = new $resetPasswordEmailClass($emailData);
            $resetPasswordEmail->addRecipient($response->Email, $response->FullName);
            $resetPasswordEmail->send();
        }
    }
} 