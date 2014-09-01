<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 27/08/14
 * Time: 09:56
 */

namespace WPTripolis\Tripolis\Response\Contacts;


use WPTripolis\Tripolis\Response\AbstractIteratorResponse;

class GetContactGroupSubscriptionsResponse extends AbstractIteratorResponse
{
	public function parseResponse($reply)
	{
		$this->populate($reply,'contactGroupSubscriptions','contactGroupSubscription');
	}
} 