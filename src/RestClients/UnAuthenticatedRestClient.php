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

namespace Rhubarb\Scaffolds\Saas\Tenant\RestClients;

use Rhubarb\Scaffolds\Saas\Tenant\Settings\UnAuthenticatedRestClientSettings;
use Rhubarb\RestApi\Clients\BasicAuthenticatedRestClient;

/**
 * The RestClient that handles activities such as user registration, forgot password etc.
 *
 */
class UnAuthenticatedRestClient extends BasicAuthenticatedRestClient
{
    /// TODO: Change this class to use PayloadSignatureRestClient
    public function __construct($apiUrl)
    {
        $clientSettings = UnAuthenticatedRestClientSettings::singleton();

        parent::__construct($apiUrl, $clientSettings->Username, $clientSettings->Password);
    }
}