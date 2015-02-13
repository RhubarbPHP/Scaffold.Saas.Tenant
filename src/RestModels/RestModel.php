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

namespace Rhubarb\Crown\Saas\Tenant\RestModels;

use Rhubarb\Stem\ModelState;
use Rhubarb\Crown\RestApi\Clients\RestClient;
use Rhubarb\Crown\RestApi\Clients\RestHttpRequest;
use Rhubarb\Crown\RestApi\Exceptions\RestImplementationException;

/**
 * Base class for RestModel objects.
 */
abstract class RestModel extends ModelState
{
	private $_restResourceId = false;

	public function __construct( $restResourceId = null )
	{
		parent::__construct();

		if ( $restResourceId !== null )
		{
			// Load the resource via the API


		}
	}


	/**
	 * Returns the RestClient object to use for loading and saving the model.
	 *
	 * @return RestClient
	 */
	protected function GetRestClient()
	{

	}

	/**
	 * Returns the URI for the collection holding this type of model in the API.
	 *
	 * This will normally be just the portion of the full URL unique to this collection. For
	 * example if the full URL was http://my.service.com/api/users then the return value would
	 * be just /users
	 *
	 * @return string
	 */
	protected abstract function GetCollectionUri();

	/**
	 * Returns the URL for a single instance of the model resource.
	 */
	protected function GetResourceUri()
	{
		$collectionUrl = $this->GetCollectionUri();

		if ( $this->_restResourceId !== false )
		{
			return $collectionUrl."/".$this->_restResourceId;
		}
		else
		{
			return $collectionUrl;
		}
	}

	public function Save()
	{
		$payload = $this->ExportRawData();

		$verb = ( $this->_restResourceId !== false ) ? "put" : "post";

		// This is a put operation as we're updating the existing resource.
		$request = new RestHttpRequest( $this->GetResourceUri(), $verb, $payload );

		$client = $this->GetRestClient();
		$response = $client->MakeRequest( $request );

		if ( isset( $response->result ) && !$response->result->status )
		{
			throw new RestImplementationException( "The rest model could not be saved." );
		}

		if ( $verb == "post" )
		{
			$this->ImportFromRestResource( $response );
		}

		return $response;
	}

	/**
	 * Takes a stdClass object and imports the key value pairs into the model's state.
	 *
	 * @param $resourceObject
	 */
	public function ImportFromRestResource( $resourceObject )
	{
		$values = get_object_vars( $resourceObject );

		$this->ImportData( $values );
	}

	/**
	 * Exports the key value pairs required to represent this as a REST resource payload.
	 *
	 * @return array
	 */
	public function ExportAsRestPayload()
	{
		return $this->ExportRawData();
	}
} 