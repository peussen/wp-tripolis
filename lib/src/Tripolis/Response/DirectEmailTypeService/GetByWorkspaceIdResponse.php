<?php
/*
 * @author: petereussen
 * @package: hj2016
 */

namespace WPTripolis\Tripolis\Response\DirectEmailTypeService;


use WPTripolis\Tripolis\Response\AbstractIteratorResponse;

class GetByWorkspaceIdResponse extends AbstractIteratorResponse
{
    public function parseResponse($reply)
    {
        $this->populate($reply,'directEmailTypes','directEmailType');
    }
}