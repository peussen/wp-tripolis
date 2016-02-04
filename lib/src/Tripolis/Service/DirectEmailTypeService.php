<?php
/*
 * @author: petereussen
 * @package: hj2016
 */

namespace WPTripolis\Tripolis\Service;


class DirectEmailTypeService extends AbstractService
{
    public function getByWorkspaceId($workspace)
    {
        $body = array(
            'workspaceId' => $workspace
        );

        return $this->invoke(__FUNCTION__,$body);
    }
}