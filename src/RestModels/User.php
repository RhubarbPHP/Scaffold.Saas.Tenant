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

namespace Rhubarb\Scaffolds\Saas\Tenant\RestModels;

use Rhubarb\Scaffolds\Saas\Tenant\RestClients\UnAuthenticatedRestClient;
use Rhubarb\Scaffolds\Saas\Tenant\Settings\RestClientSettings;

/**
 * @property string $Username
 * @property string $Forename
 * @property string $Surname
 * @property string $Email
 */
class User extends RestModel
{
    /**
     * Returns the URI for the collection holding this type of model in the API.
     *
     * This will normally be just the portion of the full URL unique to this collection. For
     * example if the full URL was http://my.service.com/api/users then the return value would
     * be just /users
     *
     * @return string
     */
    protected function getCollectionUri()
    {
        return "/users";
    }

    protected function getRestClient()
    {
        $settings = new RestClientSettings();

        return new UnAuthenticatedRestClient($settings->ApiUrl);
    }
}