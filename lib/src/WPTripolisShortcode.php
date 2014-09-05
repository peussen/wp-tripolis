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

/**
 * Shortcode handler class
 * This class handles the implementation of the wptripolis shortcut, both subscribe and unsubscribe.
 * Some refactoring may be in place, but later ;)
 *
 * @package WPTripolis
 */
class WPTripolisShortcode extends Shortcode
{
	public function __construct($pluginFile, $tag = null)
	{
		parent::__construct($pluginFile, strtolower(__NAMESPACE__));
	}

	/**
	 * Add styles so the base forms look okay-ish
	 */
	public function registerCustomScripts()
	{
		wp_register_style( $this->plugin . '-shortcode-style', plugins_url( $this->plugin . '/css/site.css' ),false);
		wp_enqueue_style( $this->plugin . '-shortcode-style' );
	}

	/**
	 * Core render method, displays the form based on the shortcode input
	 *
	 * @param array $attr
	 *
	 * @return bool
	 * @throws \Exception
	 */
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

		switch ( strtolower($attr['type']) ) {
			case 'unsubscribe':
				$template = 'unsubscribe.php';

				// Unsubscribe always requires the contact ID, tell people it is so.
				if (!isset($_GET['contactid'])) {

					if ( isset($_GET['wptripolis-contact-removed'])) {
						$template = 'removed.php';
					} else {
						_e('Please supply the Tripolis Contact ID in the link','tripolis');
						return false;
					}
				}

				try {
					// Setup some stuff for templating
					$db    = (isset($attr['database']) ? $attr['database'] : false);
					$group = (isset($attr['unsubscribegroup']) ? $attr['unsubscribegroup'] : false);
					TemplateWrapper::setContactId($_GET['contactid']);
					TemplateWrapper::setContactMeta($client->contact()->getById($_GET['contactid'],array(),$db));
					TemplateWrapper::setSubscriptions($this->getContactGroupsAndSubscriptionStatus($client,$db,$_GET['contactid'],$group));
				} catch (NotFoundException $e) {
					$template = 'removed.php';
				} catch (\SoapFault $f) {
					_e('We could not find your subscriptions.','tripolis');
				} catch (\TripolisException $e) {
					// Die gracefully if API barfs
					_e('We could not find your subscriptions.','tripolis');
					return false;
				} catch (\Exception $e) {
					if ( WP_DEBUG ) {
						throw $e;
					}
					else {
						return false;
					}
				}

				break;
			case 'subscribe':
			default:
				if ( !isset($fields)) {
					return false;
				}
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

				$template = 'subscribe.php';

				TemplateWrapper::setFieldLoop($fieldDef);
		}

		$template = $this->findTemplate($template);
		require($template);
		return true;
	}

	/**
	 * Creates a list of all "Subscribeable groups and combines that with current subscription info
	 *
	 * When in WP_DEBUG mode, all TEST groups will also be selectable.
	 *
	 * @param $client
	 * @param $db
	 * @param $contactId
	 * @param $removeGroup
	 *
	 * @return array
	 */
	protected function getContactGroupsAndSubscriptionStatus($client,$db,$contactId,$removeGroup)
	{
		$allowed       = array('SUBSCRIPTION');

		if ( WP_DEBUG ) {
			$allowed[] = 'TEST';
		}

		$subscriptions = $client->contact()->getContactGroupSubscriptions($contactId,$allowed);
		$all           = $client->contactGroup()->getByContactDatabaseId($db);
		$combined      = array();

		foreach( $all as $group ) {

			if ( !$group->isArchived && in_array($group->type,$allowed) && $group->id != $removeGroup) {
				$group->subscribed    = false;
				$combined[$group->id] = $group;
			}
		}

		foreach( $subscriptions as $subscription ) {
			if ( isset($combined[$subscription->contactGroupId])) {
				$combined[$subscription->contactGroupId]->subscribed = true;
			}
		}

		return $combined;
	}

	/**
	 * Create a list of fields to show in the register form.
	 *
	 * @param TripolisProvider $provider
	 * @param                  $database
	 * @param                  $fields
	 *
	 * @return array
	 * @throws \Exception
	 * @throws \SoapFault
	 * @throws \Exception
	 */
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

	/**
	 * Creates a Tripolis Client based on settings
	 *
	 * @return bool|TripolisProvider
	 */
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

	/**
	 * Basic form submit info.
	 *
	 * @param array $attr
	 * @param array $postData
	 *
	 * @return array|bool
	 */
	protected function handleFormSubmit($attr,$postData)
	{
		$postData = isset($postData[$this->getUniqueId()]) ? $postData[$this->getUniqueId()] : false;
		$status   = array();

		// Only do some work, if we have a type and the postData points to us.
		if ( isset($attr['type']) && $postData) {
			switch( strtolower($attr['type'])) {
				case 'subscribe':
					$status = $this->handleSubscribeSubmit($attr,$postData);
					break;
				case 'unsubscribe':
					$status = $this->handleUnsubscribe($attr,$postData);
					break;
				default:
			}
		}

		// Store status for templating
		TemplateWrapper::registerInstance('submit',$status);
		return $status;
	}

	/**
	 * Handles unsubscribe based on given input and actions
	 *
	 * If a user "Added" a subscription, he will not be placed in the subscriptions group.
	 * If the action is delete, this will only happen if the current user has no other subscriptions
	 *
	 * @param $attr
	 * @param $postData
	 *
	 * @return array
	 */
	protected function handleUnsubscribe($attr,$postData)
	{
		if ( !isset($postData['contactid'])) {
			return array();
		}

		if ( !isset($postData['_wpnonce']) || wp_verify_nonce($postData['_wpnonce'],'wptripolis-unsubscribe' . $postData['contactid']) !== 1) {
			return array('form' => array(
				'class' => 'error',
				'message' => __('The form expired, please try again','tripolis')
			));
		}

		$allowed       = array('SUBSCRIPTION');

		if ( WP_DEBUG ) {
			$allowed[] = 'TEST';
		}

		if ( $postData && isset($attr['database']) && isset($attr['type'])) {
			if ( isset($postData['contactid'])) {
				$subscriptionsToKeep = array();

				if ( isset($postData['retain'])) {
					$subscriptionsToKeep = (array)$postData['retain'];
				}

				try {
					$client         = $this->getTripolisClient();
					$contactService = $client->contact()->database($attr['database']);
					$subscriptions  = $contactService->getContactGroupSubscriptions($postData['contactid'],$allowed);
					$remove         = array();
					$add            = array();
					$unsubscribed   = false;
					$unsubgroup     = (isset($attr['unsubscribegroup']) ? $attr['unsubscribegroup'] : '');

					$tmp     = $subscriptions->toArray();
					$current = array();

					// Create a simple array of current subscriptions
					foreach( $tmp as $group ) {
						$current[] = $group->contactGroupId;
					}

					// Sort out which entries to remove,
					foreach( $current as $group ) {
						if ( $group == $unsubgroup) {
							$unsubscribed = true;
						} elseif ( !in_array($group,$subscriptionsToKeep)) {
							$remove[] = $group;
						}
					}

					// Which to add,
					foreach( $subscriptionsToKeep as $subscription ) {
						if ( !in_array($subscription,$current)) {
							$add[] = $subscription;
						}
					}

					foreach( $remove as $groupId ) {
						$contactService->removeFromContactGroup($postData['contactid'], $groupId);
					}

					foreach( $add as $groupId ) {
						$contactService->addToContactGroup($postData['contactid'],$groupId);
					}


					if ( isset($attr['action'])) {
						switch( $attr['action']) {
							// If move, then we move whenever we removed someone and the person is not in the unsubscribe group
							case 'move':
								if ( $add ) {
									if ( $unsubscribed ) {
										$contactService->removeFromContactGroup($postData['contactid'],$unsubgroup);
									}
									return array('form' => array('class' => 'success', 'message' => __('Your subscriptions have been updated.','tripolis')));
								} else {
									if ( !$unsubscribed && $unsubgroup) {
										$contactService->addToContactGroup($postData['contactid'], $unsubgroup);
									}
								}
								break;
							// If delete, we delete if the user is not subscribed to any newsletter anymore
							case 'delete':
								$remaining = array_diff($current,$remove);
								$remaining = array_merge($remaining,$add);

								if ( empty($remaining) ) {
									$contactService->delete($postData['contactid']);
									return array('form' => array('class' => 'success', 'message' => __('Your account has been removed.','tripolis')));
								}
								return array('form' => array('class' => 'success', 'message' => __('Your subscriptions have been updated.','tripolis')));
						}
					}
				} catch (\SoapFault $f) {
					return array('form' => array('class' => 'error', 'message' => __('Unable to handle your request, please try again later','tripolis')));
				} catch (\Exception $e) {
					return array('form' => array('class' => 'error', 'message' => __('Unable to handle your request, please try again later','tripolis')));
				}
			}
		}

		return array('form' => array('class' => 'updated', 'message' => __('Your subscriptions have been changed','tripolis')));
	}

	protected function handleSubscribeSubmit($attr,$postData)
	{
		// Only handle posts if it is ours
		if ( $postData && isset($attr['database']) && isset($attr['type'])) {
			$client = $this->getTripolisClient();
			$fields = $this->getFieldDefinitions($client,$attr['database'],array_keys($postData));
			$status = false;

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