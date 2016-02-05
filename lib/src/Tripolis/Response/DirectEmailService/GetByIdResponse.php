<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

namespace WPTripolis\Tripolis\Response\DirectEmailService;


use WPTripolis\Tripolis\Response\GenericResponse;

class GetByIdResponse extends GenericResponse
{
    protected function parseResponse($reply)
    {
        if ( isset($reply->response->directEmail)) {
            $this->setData($reply->response->directEmail);
        }
    }

}