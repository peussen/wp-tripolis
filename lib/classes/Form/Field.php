<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

namespace WPTripolis\Form;


use WPTripolis\Wordpress\Template;

class Field
{
  protected $form;

  public $label;
  public $required = false;
  public $id;
  public $type = 'string';
  public $default = '';

  protected $source;

  protected $definition_fields = [
    'id'      => false,
    'label'   => false,
    'altlabel'=> false,
    'required'=> false,
    'default' => '',
    'type'    => 'STRING'
  ];

  public function __construct($definition,$form = null)
  {
    $this->form = $form;

    $this->label    = $definition['altlabel'] ?: $definition['field']['label'];
    $this->required = $definition['field']['required'];
    $this->id       = $definition['field']['id'];
    $this->type     = strtolower($definition['field']['type']);
    $this->default  = $definition['field']['default'];
    $this->source   = $definition['field'];
  }

  public function __toString()
  {
    set_query_var('wpfield',$this);

    $template = new Template();
    return $template->get('templates/field',$this->type);
  }
}