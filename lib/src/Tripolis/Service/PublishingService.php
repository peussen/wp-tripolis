<?php
/*
 * @author: petereussen
 * @package: hj2016
 */

namespace WPTripolis\Tripolis\Service;


class PublishingService extends AbstractService
{

    public function publishTransactionalEmail($contactId,$emailId,$mailJobTags = array())
    {
        $body = array(
            'contactId'     => $contactId,
            'directEmailId' => $emailId,
            'mailJobTagIds' => array(
                'mailJobTagId' => $mailJobTags
            )
        );

        return $this->invoke(__FUNCTION__,$body);
    }
}