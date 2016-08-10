<?php

// Define functions
/**
 * Field Loop function. Initializes loop, or iterates to next field
 *
 * @return bool
 */
function wptripolis_have_fields() { return \WPTripolis\TemplateWrapper::haveFields(); }

/**
 * Returns the complete field definition as an array
 *
 * @return array|FALSE
 */
function wptripolis_get_field() { return \WPTripolis\TemplateWrapper::getField(); }

/**
 * Returns the type of field as dictated by Tripolis
 *
 * @return string|FALSE
 */
function wptripolis_get_field_type() { return \WPTripolis\TemplateWrapper::getFieldProperty('type'); }

/**
 * Returns an unique identifier for the field
 *
 * @return string|FALSE
 */
function wptripolis_get_field_id() { return \WPTripolis\TemplateWrapper::getFieldProperty('id'); }

/**
 * Returns the label as dictated by Tripolis
 * @return string|FALSE
 */
function wptripolis_get_field_label() { return \WPTripolis\TemplateWrapper::getFieldProperty('label'); }

/**
 * Returns the field message in case of an error
 *
 * @return string|FALSE
 */
function wptripolis_get_field_message() { return \WPTripolis\TemplateWrapper::getFieldProperty('message'); }

/**
 * Returns the current value for the field
 *
 * @return string|FALSE
 */
function wptripolis_get_field_value() { return \WPTripolis\TemplateWrapper::getFieldProperty('value'); }

/**
 * Locates a template in either the theme, child-theme or plugin
 *
 * @param $template
 *
 * @return string|FALSE
 */
function wptripolis_find_template($template) { return \WPTripolis\TemplateWrapper::findTemplate($template); }

/**
 * Returns a unique identifier for the plugin
 *
 * @return string
 */
function wptripolis_plugin_name() { echo \WPTripolis\TemplateWrapper::getPluginName(); }

/**
 * displays the unique field ID
 */
function wptripolis_field_id() { echo \WPTripolis\TemplateWrapper::getFieldProperty('id'); }

/**
 * Displays the field label
 */
function wptripolis_field_label() { echo \WPTripolis\TemplateWrapper::getFieldProperty('label'); }

/**
 * Displays the field name
 */
function wptripolis_field_name() { echo \WPTripolis\TemplateWrapper::getFieldProperty('name'); }

/**
 * Displays any field message
 */
function wptripolis_field_message() { echo \WPTripolis\TemplateWrapper::getFieldProperty('message'); }

/**
 * Displays "required" if the field is required
 */
function wptripolis_field_required() { echo \WPTripolis\TemplateWrapper::getFieldProperty('required') ? 'required' : ''; }

/**
 * Displays a list of classes assigned to the field
 *
 * @param array $extra
 */
function wptripolis_field_classes($extra = array()) { $all = $extra + (array)\WPTripolis\TemplateWrapper::getFieldProperty('class'); echo implode(' ',$all); }

// Unsubscribe functions

/**
 * Returns the contact id
 *
 * @return string|bool
 */
function wptripolis_get_contact_id() { return \WPTripolis\TemplateWrapper::getContactId(); }

/**
 * Displays the contactId
 *
 */
function wptripolis_contact_id() { echo \WPTripolis\TemplateWrapper::getContactId(); }

/**
 * Returns a field from the contact database
 * @param $field
 *
 * @return mixed|false
 */
function wptripolis_get_contact_meta($field) { return \WPTripolis\TemplateWrapper::getContactMeta($field); }

/**
 * Returns a list of subscriptions for the user that wants to unsubscribe
 *
 * @return array
 */
function wptripolis_get_subscriptions() { return \WPTripolis\TemplateWrapper::getSubscriptions(); }

function wptripolis_get_contact_id_field() {
  echo '<input type="hidden" name="' . \WPTripolis\TemplateWrapper::createFieldName('contactid') . '" value="' . esc_attr(\WPTripolis\TemplateWrapper::getContactId()) . '" />';
}

/**
 * Returns all contact data or false if none found
 *
 * @return WPTripolis\Tripolis\Response\ContactService\GetByIdResponse|bool
 */
function wptripolis_get_contact() { return \WPTripolis\TemplateWrapper::getContactMeta(); }


/**
 * Displays any possible error/update message
 */
function wptripolis_show_form_status() {
  $submit = \WPTripolis\TemplateWrapper::getInstance('submit');

  if ( isset($submit['form']) ) {
    $class = $submit['form']['class'];
  } else {
    $class = 'success';
  }

  if ( isset($submit['form']['message'])):
    ?>
    <div class="alert alert-<?php  echo $class ?> formmessage">
      <a href="#" class="close" data-dismiss="alert">&times;</a>
      <?php echo $submit['form']['message'] ?>
    </div>
    <?php
  endif;
}

/**
 * Creates a fieldname to be used in the shortcode
 *
 * @param $name
 * @return string
 */
function wptripolis_form_field( $name ) { return \WPTripolis\TemplateWrapper::createFieldName($name); }

/**
 * Creates an unique field id based on the given name
 * @param $name
 * @return string
 */
function wptripolis_form_field_id($name) { return \WPTripolis\TemplateWrapper::createFieldId($name); }
