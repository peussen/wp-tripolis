<?php
/*
 * @author: petereussen
 * @package: hj2016
 */

namespace WPTripolis\Tripolis;


use WPTripolis;

class DirectMail
{
    static $mapping = false;

    private $provider;
    private $contactDatabaseId;
    private $workspaceId;

    public function __construct(TripolisProvider $provider)
    {
        $this->provider = $provider;
    }

    public function db($id)
    {
        $this->contactDatabaseId = $id;

        $workspace         = $this->provider->workspace()->getByContactDatabaseId($id);
        $this->workspaceId = $workspace->first()->id;
    }

    public function send($dmId,$contactId)
    {
        $publisher = $this->provider->publishing();

        try {
            $dm   = $this->provider->directEmail()->getById($dmId);
            $dmId = $dm->id;
        } catch( NotFoundException $e) {
            $dmId = $this->getByName($dmId);
        }

        try {
            $response  = $publisher->publishTransactionalEmail($contactId,$dmId);
            return $response->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getByName($name)
    {
        if ( static::$mapping === false ) {
            $types = $this->provider->directEmailType()->getByWorkspaceId($this->workspaceId);
            $dmMap = array();

            foreach( $types as $type ) {
                $dms = $this->provider->directEmail()->getByDirectEmailTypeId($type->id);

                foreach( $dms as $dm ) {
                    $dmMap[strtolower($dm->name)] = $dm->id;
                }
            }

            static::$mapping = $dmMap;
        }

        $name = strtolower($name);

        if ( isset(static::$mapping[$name])) {
            return static::$mapping[$name];
        }
        return false;
    }
}