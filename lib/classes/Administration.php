<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

namespace WPTripolis;


use WPTripolis\Admin\FormList;

class Administration extends \AdminPageFrameworkLoader_AdminPage
{
  public function setUp()
  {
    $this->setRootMenuPage('Tripolis');
    $this->addSubMenuItems(
      array(
        'title' => 'Settings',
        'page_slug'  => 'wptripolis_settings',
      ),
      array(
        'title' => 'Diagnostics',
        'page_slug'  => 'wptripolis_diagnostics',
      )
    );
  }

  public function do_wptripolis_diagnostics()
  {
    echo "<h3>Diganostics</h3>";
  }

  public function do_wptripolis_settings()
  {
    echo '<h3>Show all the options as an array</h3>';
    echo $this->oDebug->get( \AdminPageFramework::getOption( __CLASS__ ) );
  }

  public function load_wptripolis_settings($oAdminPage)
  {
    $options = array();
    for ( $i = 36; $i < 55; $i++) {
      $options['https://td' . $i . '.tripolis.com'] = 'td' . $i . '.tripolis.com';
    }

    $options = apply_filters('wptripolis_alter_environments',$options);

    $oAdminPage->addSettingFields(
      array(    // Single text field
        'field_id'      => 'client_environment',
        'type'          => 'select',
        'title'         => __('Tripolis Environment','tripolis'),
        'description'   => __('The URL where your instance of Tripolis is located','tripolis'),
        'label'         => $options,
      ),
      array(    // Single text field
        'field_id'      => 'client_account',
        'type'          => 'text',
        'title'         => __('Account','tripolis'),
        'description'   => __('The Account name','tripolis'),
      ),
      array(    // Single text field
        'field_id'      => 'client_username',
        'type'          => 'text',
        'title'         => __('Username','tripolis'),
        'description'   => __('The API user name','tripolis'),
      ),
      array(    // Single text field
        'field_id'      => 'client_password',
        'type'          => 'password',
        'title'         => __('Password','tripolis'),
        'description'   => __('The API user password','tripolis'),
      ),
      array(
        'field_id'    => 'submit',
        'type'        => 'submit',
        'value'       => __('Save','tripolis'),
      )
    );
  }

  public function validate_Administration_wptripolis_settings($inputs,$old,$factory,$submitinfo)
  {

  }
}