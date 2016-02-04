<?php
/*
 * @author: petereussen
 * @package: hj2016
 */

namespace WPTripolis\Tripolis\Service;


class WorkspaceService extends AbstractService
{
    public function getByContactDatabaseId($dbId)
    {
        $body = array(
            'contactDatabaseId' => $dbId
        );

        return $this->invoke(__FUNCTION__,$body);
    }
}