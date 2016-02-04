<?php
/*
 * @author: petereussen
 * @package: hj2016
 */

namespace WPTripolis\Tripolis\Service;



class DirectEmailService extends AbstractService
{
    public function getByDirectEmailTypeId($dmTypeId)
    {
        $body = array(
            'directEmailTypeId' => $dmTypeId
        );

        return $this->invoke(__FUNCTION__,$body);
    }

    public function getById($dmId)
    {
        return $this->invoke(__FUNCTION__,array('id' => $dmId));
    }
}