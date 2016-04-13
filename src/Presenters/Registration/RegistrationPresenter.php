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

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Registration;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\User;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\TenantSettings;
use Rhubarb\Leaf\Presenters\Forms\Form;

class RegistrationPresenter extends Form
{
    /**
     * Override to initialise the presenter with it's model, and any other relevant settings.
     *
     * The view should not be instantiated or configured here however - do this in ApplyModelToView
     */
    protected function initialiseModel()
    {
        $context = Request::current();
        $i = $context->get("i");

        if ($i){
            $this->model->InviteID = $i;
        }

        parent::initialiseModel();
    }

    /**
     * Returns the list of properties that should appear in the model.
     *
     * This does seem like duplicated effort as ModelState has a similar convention however the burden of creating
     * a separate model object for every presenter just to set this data is overkill
     *
     * @return array
     */
    protected function getPublicModelPropertyList()
    {
        $list = parent::getPublicModelPropertyList();
        $list[] = "InviteID";

        return $list;
    }

    private function createUser()
    {
        // Assumes the model has been populated with all the various settings.
        $user = new User();

        $user->Forename = $this->model->Forename;
        $user->Surname = $this->model->Surname;
        $user->Username = $this->model->Username;
        $user->Email = $this->model->Email;
        $user->NewPassword = $this->model->NewPassword;

        if (isset($this->model->InviteID)){
            $user->InviteID = $this->model->InviteID;
        }

        $loggedIn = false;

        try {
            $user->save();

            $loginProvider = LoginProvider::getProvider();
            $loggedIn = $loginProvider->login($this->model->Username, $this->model->NewPassword);

            $settings = TenantSettings::singleton();
        } catch (\Exception $er) {
            /// TODO: What happens now?
        }

        if ($loggedIn) {
            throw new ForceResponseException(new RedirectResponse($settings->PostRegistrationUrl));
        }
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler("CreateUser", function () {
            $this->createUser();
        });
    }

    protected function createView()
    {
        return new RegistrationView();
    }
} 