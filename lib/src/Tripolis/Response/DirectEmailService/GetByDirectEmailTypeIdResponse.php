<?php
/*
 * @author: petereussen
 * @package: hj2016
 */

namespace WPTripolis\Tripolis\Response\DirectEmailService;


use WPTripolis\Tripolis\Response\AbstractIteratorResponse;

class GetByDirectEmailTypeIdResponse extends AbstractIteratorResponse
{

    public function parseResponse($reply)
    {
        $this->populate($reply,'directEmails','directEmail');
    }
}