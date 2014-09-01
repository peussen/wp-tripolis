<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 25/08/14
 * Time: 14:38
 */

namespace WPTripolis\Tripolis\Response\ContactDatabaseService;

use WPTripolis\Tripolis\Response;

class GetAllResponse extends Response\AbstractIteratorResponse
{

	protected function parseResponse($replyStructure)
	{
		$this->populate($replyStructure, 'contactDatabases','contactDatabase');
	}

}