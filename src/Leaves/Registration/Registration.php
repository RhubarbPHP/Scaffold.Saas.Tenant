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

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Registration;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\Authentication\Settings\AuthenticationSettings;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\User;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\TenantSettings;
use Rhubarb\Leaf\Leaves\Forms\Form;

class Registration extends Leaf
{
    /**
     * @var RegistrationModel
     */
    protected $model;

    private function createUser()
    {
        // Assumes the model has been populated with all the various settings.
        $user = new User();

        $user->Forename = $this->model->forename;
        $user->Surname = $this->model->surname;
        $user->Username = $this->model->username;
        $user->Email = $this->model->email;
        $user->NewPassword = $this->model->newPassword;

        if (isset($this->model->inviteId)){
            $user->InviteID = $this->model->inviteId;
        }

        $loggedIn = false;

        try {
            $user->save();

            $loginProvider = LoginProvider::getProvider();
            $loggedIn = $loginProvider->login($this->model->email, $this->model->newPassword);

        } catch (\Exception $er) {
            /// TODO: What happens now?
        }

        if ($loggedIn) {
            $settings = TenantSettings::singleton();
            $url = $settings->postRegistrationUrl;

            if ($this->model->inviteId){
                $accounts = Me::getAccounts();
                $url .= "?choose=".$accounts[0]->_id;
            }

            throw new ForceResponseException(new RedirectResponse($url));
        }
    }

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return RegistrationView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new RegistrationModel();

        $context = Request::current();
        $i = $context->get("i");

        if ($i){
            $model->inviteId = $i;
        }

        $model->createUserEvent->attachHandler(function() {
           $this->createUser();
        });

        if (isset($model->inviteId)) {
            $invitedUsers = SaasGateway::getOutstandingInvites();

            foreach($invitedUsers->items as $invitedUser) {
                if ($model->inviteId == $invitedUser->_id) {
                    $model->email = $invitedUser->Email;
                }
            }
        }

        return $model;
    }
} 