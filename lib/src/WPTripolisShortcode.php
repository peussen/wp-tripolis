<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 27/08/14
 * Time: 11:04
 */

namespace WPTripolis;


use WPTripolis\Tripolis\AlreadyExistsException;
use WPTripolis\Tripolis\NotFoundException;
use WPTripolis\Wordpress\Shortcode;

class WPTripolisShortcode extends Shortcode
{
	public function __construct($pluginFile, $tag = null)
	{
		parent::__construct($pluginFile, strtolower(__NAMESPACE__));
	}

	public function render( $attr )
	{
		$defaults = array(
			'type' 		         => 'subscribe',
			'fields'	         => '',
			'database'         => '',
			'subscribegroup'   => '',
			'unsubscribegroup' => '',
			'method'           => 'move',
			'id'							 => $this->getUniqueId(),
			'submit'					 => false,
		);

		$client = $this->getTripolisClient();

		extract(shortcode_atts($defaults,$attr),EXTR_SKIP);

		// Fields declared through extract();
		$fields = explode(",",$fields);
		$fields = array_map('trim',$fields);

		$fieldDef = $this->getFieldDefinitions($client,$database,$fields);
		foreach( $fieldDef as $field ) {
			if ( isset($submit[$field['code']])) {
				$fieldDef[$field['code']]['class'][] = $submit[$field['code']]['class'];
				$fieldDef[$field['code']]['message'] = $submit[$field['code']]['message'];
			}
		}

		// Type declared through extract()
		switch ( strtolower($type) ) {
			case 'unsubscribe':
				$template = 'unsubscribe.php';

				break;
			case 'subscribe':
			default:
				$template = 'subscribe.php';


		}

		TemplateWrapper::setFieldLoop($fieldDef);

		$template = $this->findTemplate($template);
		require($template);
	}

	protected function getFieldDefinitions( TripolisProvider $provider, $database, $fields )
	{
		// Try gracefully, so not to upset the non-programmers
		try {
			$fieldGroups = $provider->ContactDatabaseFieldGroup()->database($database)->all();
			$tableFields = $provider->ContactDatabaseField()->database($database)->all();
		} catch (\SoapFault $f) {
			if ( !WP_DEBUG )
				return array();
			else
				throw $f;
		} catch (\Exception $e) {
			if ( !WP_DEBUG )
				return array();
			else
				throw $e;
		}

		$postData    = isset($_POST[$this->plugin]) ? $_POST[$this->plugin] : false;

		$definition = array();

		if ( !$fields ) {
			$fields = array_keys($tableFields);
		}

		foreach( $fields as $field ) {
			if ( isset($tableFields[$field])) {

				$id = implode('_',array(
						$this->plugin,
						$fieldGroups[$tableFields[$field]->contactDatabaseFieldGroupId]->name,
						$tableFields[$field]->name
				));

				$required = apply_filters($this->plugin . '_required',$tableFields[$field]->required,$tableFields[$field]);
				$classes  = array($this->plugin,strtolower($tableFields[$field]->type));
				if ( $required) $classes[] = 'required';

				$definition[$field] = array(
					'code'    => $field,
					'label' 	=> apply_filters($this->plugin . '_label',$tableFields[$field]->label,$tableFields[$field]),
					'name'		=> $this->getUniqueId() . '[' . $field . ']',
					'id'		  => $id,
					'value' 	=> apply_filters($this->plugin . '_value',isset($postData[$field]) ? $postData[$field] : '',$tableFields[$field]),
					'type'  	=> $tableFields[$field]->type,
					'required'=> $required,
					'class'		=> apply_filters($this->plugin . '_classes',$classes,$tableFields[$field]),
					'group'	  => $fieldGroups[$tableFields[$field]->contactDatabaseFieldGroupId],
					'message' => '',
				);
			}

		}

		return $definition;

	}

	protected function getTripolisClient()
	{
		$config = get_option('wptripolis_optionsscreen');

		if ( isset($config['client_validated']) && $config['client_validated']) {
			return new TripolisProvider(
					$config['client_account'],
					$config['client_username'],
					$config['client_password'],
					$config['client_environment']
			);

		}
		return false;
	}

	protected function handleFormSubmit($attr,$postData)
	{
		$postData = isset($postData[$this->getUniqueId()]) ? $postData[$this->getUniqueId()] : false;
		$status   = array();

		// Only handle posts if it is ours
		if ( $postData && isset($attr['database']) && isset($attr['type'])) {
			$client = $this->getTripolisClient();
			$fields = $this->getFieldDefinitions($client,$attr['database'],array_keys($postData));

			foreach ($fields as $field ) {
				if ( $field['required'] && (!isset($postData[$field['code']]) || empty($postData[$field['code']]))) {
					$status[$field['code']] = array(
							'class' 	=> 'error',
							'message'	=> __('Field is required','tripolis')
					);
				} elseif ( $field['type'] === 'EMAIL' && isset($postData[$field['code']])) {
					$email = filter_var($postData[$field['code']],FILTER_VALIDATE_EMAIL);

					if ( $email === false ) {
						$status[$field['code']] = array(
							'class' => 'error',
							'message' => __('Please enter a valid e-mail','tripolis'),
						);
					}
				}

				// No messages means good!
				if ( !$status ) {
					switch( strtolower($attr['type'])) {
						case 'subscribe':
							if ($this->subscribe($client,$attr['database'],$attr['subscribegroup'],$postData)) {
								$status['form'] = array(
									'class' 	=> 'success',
									'message'	=> __('You have been succesfully subscribed','tripolis')
								);
							} else {
								$status['form'] = array(
										'class' 	=> 'error',
										'message'	=> __('We were unable to handle your request, try again later','tripolis')
								);
							}
							break;
						default:

					}
				}
			}

			return $status;
		}
	}


	public function subscribe($client,$database,$group,$contactData)
	{
		try {
			$contactService = $client->contact();
			$contactService->database($database);

			$contactId = $contactService->replace($contactData,'id');

			if ( $contactId ) {
				$response = $contactService->addToContactGroup($contactId,$group);
				return $response->id;
			}
		} catch ( NotFoundException $e ) {
			if ( WP_DEBUG) { echo $e->getMessage(); throw $e; }
			return false;
		} catch ( \SoapFault $f) {
			if ( WP_DEBUG) { echo $f->getMessage(); throw $f; }
			return false;
		} catch (\Exception $e) {
			if ( WP_DEBUG) throw $e;
			return false;
		}
		return false;
	}
} 