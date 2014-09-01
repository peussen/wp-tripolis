<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 24/08/14
 * Time: 12:06
 */

namespace WPTripolis\Tripolis\Response\Contacts;

use WPTripolis\Tripolis\Response\AbstractResponse;

class ServiceInfoResponse extends AbstractResponse
{
	protected function parseResponse()
	{
		$tmp = array();

		if ( isset($this->raw->serviceInfoItems)) {

		}

		$this->setData($tmp);
	}


} 