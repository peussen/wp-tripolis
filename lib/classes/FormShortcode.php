<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

namespace WPTripolis;


use HarperJones\Tripolis\Contact;
use WPTripolis\Form\Form;
use WPTripolis\Form\FormProcessingException;
use WPTripolis\Wordpress\Template;

class FormShortcode
{
  static private $instance = null;

  protected $enqueued = false;
  protected $defaults = [
    'form' => false,
    'css'  => true,
    'js'   => true,
  ];

  static public function create()
  {
    if ( self::$instance === null ) {
      self::$instance = new static();
    }
    return self::$instance;
  }

  protected function __construct()
  {
    add_shortcode('wptripolis-form',[$this,'formRender']);

    add_action('wp_enqueue_scripts',[$this,'enqueueFormScript']);
    add_action('wp_ajax_wptripolis_form',[$this,'handleFormSubmit']);
    add_action('wp_ajax_nopriv_wptripolis_form',[$this,'handleFormSubmit']);
  }

  public function formRender($atts)
  {
    $config = shortcode_atts($this->defaults,$atts,'wptripolis-form');
    $form   = new Form($config['form']);

    if ( !$config['js'] ) {
      wp_dequeue_script('wptripolis-form');
    }

    if ( !$config['css'] ) {
      wp_dequeue_style('wptripolis-form');
    }

    set_query_var('wpform',$form);

    $template = new Template();

    $template->display('templates/wptripolis/form');
  }

  public function enqueueFormScript()
  {
    if ( !$this->enqueued ) {
      wp_enqueue_script('wptripolis-form',plugins_url('js/form.js',WPTRIPOLIS_BASEDIR . '/plugin.php'),['jquery'],time(),true);
      wp_localize_script('wptripolis-form','wptripolis_forms',[
        'ajaxurl'     => admin_url('admin-ajax.php'),
        'submitting'  => __('Submitting information, please wait..','wptripolis'),
        'failed'      => __('Some errors occurred, please check the form','wptripolis'),
      ]);
      $this->enqueued = true;
    }
  }

  public function handleFormSubmit()
  {
    header('Content-Type: text/json');

    $formId   = isset($_POST['form']) ? $_POST['form'] : false;
    $form     = new Form($formId);
    $response = [
      'status'  => false,
      'message' => '',
    ];

    if ( $form ) {

      try {
        if ( $form->process($_POST['fields']) ) {
          $response['status']  = true;
          $response['message'] = sprintf(
            __('Your have been %s','wptripolis'),
            ($form->action == Form::TYPE_SUBSCRIBE ? __('subscribed','wptripolis') : __('unsubscribed','wptripolis'))
          );
        } else {
          $response['message'] = __('Could not save your contact information','wptripolis');
        }
      } catch (FormProcessingException $e) {
        $response['message'] = $e->getMessage();
      }

    } else {
      $response['message'] = __('Form not found','wptripolis');
    }

    echo json_encode($response);
    exit();
  }
}