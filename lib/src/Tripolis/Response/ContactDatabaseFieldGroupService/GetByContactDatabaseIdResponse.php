<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 25/08/14
 * Time: 16:02
 */

namespace WPTripolis\Tripolis\Response\ContactDatabaseFieldGroupService;


use WPTripolis\Tripolis\Response\AbstractIteratorResponse;

class GetByContactDatabaseIdResponse extends AbstractIteratorResponse
{
	public function parseResponse($reply)
	{
		$this->populate($reply,'contactDatabaseFieldGroups','contactDatabaseFieldGroup');
	}
} 