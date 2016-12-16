<?php

namespace WPTripolis\Form;

/*
 * @author: petereussen
 * @package: wp-tripolis
 */

use HarperJones\Tripolis\Contact;
use HarperJones\Tripolis\TripolisException;
use WPTripolis\Factory;
use WPTripolis\Wordpress\Template;

class Form
{
  const TYPE_SUBSCRIBE  = "subscribe";
  const TYPE_UNSUBSCRIBE= "unsubscribe";

  public $title;
  public $id;
  public $db;
  public $action;
  public $fields = [];
  public $contactGroup = false;

  protected $provider;
  protected $template;

  public function __construct($post)
  {
    if ( is_numeric($post) ) {
      $post = get_post($post);
    }

    if ( get_post_type($post) === 'wptripolis-form') {
      $this->setupFormFromPost($post);
    }

    $this->provider = Factory::createProvider();
    $this->template = new Template();
  }

  public function getProvider()
  {
    return $this->provider;
  }

  public function process($fields)
  {
    $submitData = [];

    foreach( $fields as $id => $value ) {
      if ( isset($this->fields[$id]) ) {
        $submitData[$id] = $value;
      }
    }

    $contact = new Contact($this->provider,$this->db);
    $keyField= $contact->keyField();

    if ( isset($submitData[$keyField])) {
      $current = $contact->find($submitData[$keyField]);

      if ( $current->valid() && $this->action === self::TYPE_SUBSCRIBE ) {
        throw new AlreadySubscribedException(__("You are already on the mailing list","wptripolis"));
      } else {
        try {
          $current = $contact->create($submitData);

          if ( $this->contactGroup ) {
            $current->join($this->contactGroup,true);
            $response['status'] = true;
          }
        } catch (TripolisException $e) {
          $response['message'] = $e->getMessage();
        }


      }
      return true;
    }

    throw new FormWithoutKeyFieldException(__("Can not store information because keyfield is missing from the form","wptripolis"));
  }

  protected function setupFormFromPost($post)
  {
    $this->title = get_the_title($post);
    $this->id    = $post->ID;

    $data = @json_decode($post->post_content,true);

    if ( $data ) {
      $this->db           = $data['db'];
      $this->action       = $data['type'];
      $this->contactGroup = $data['contactgroup'];

      if ( is_array($data['fields'])) {
        foreach( $data['fields'] as $def ) {
          $field                    = new Field($def,$this);
          $this->fields[$field->id] = $field;
        }
      }
    }
  }
}