<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 04/09/14
 * Time: 14:39
 */

namespace WPTripolis\Tripolis\Response\ContactService;


use WPTripolis\Tripolis\Response;

class GetByIdResponse extends Response
{
	public function parseResponse($reply)
	{
		$fields = array();

		if ( isset($reply->response->contact->contactFields->contactField)) {
			foreach($reply->response->contact->contactFields->contactField as $field ) {
				$fields[$field->name] = isset($field->value) ? $field->value : '';
			}
		}

		if ( isset($reply->response->contact->contactId)) {
			$fields['id'] = $reply->response->contact->contactId;
		}

		$this->setData($fields);

	}
} 