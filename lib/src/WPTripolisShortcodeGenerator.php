<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 28/08/14
 * Time: 09:49
 */

namespace WPTripolis;


use WPTripolis\Wordpress\Utility;

class WPTripolisShortcodeGenerator extends Utility
{
	public function __construct( $pluginFile, $menutitle, $pagetitle ) {
		parent::__construct( $pluginFile, $menutitle, $pagetitle );

		add_action('admin_enqueue_scripts',array($this,'registerScripts'));
		add_action('wp_ajax_nopriv_' . $this->plugin . '_ajax', array($this,'ajaxCallback'));
		add_action('wp_ajax_' . $this->plugin . '_ajax', array($this,'ajaxCallback'));
	}

	public function registerScripts()
	{
		// Shared
		wp_register_style( 'wp-tripolis-admin', plugins_url( $this->plugin . '/css/admin.css' ),false);
		wp_enqueue_style( 'wp-tripolis-admin' );

		wp_register_script( $this->plugin . '-admin-generator', plugins_url( $this->plugin . '/js/generator.js'),array('jquery'),false,true);
		wp_enqueue_script( $this->plugin . '-admin-generator' );

		$params = array(
			'site_url' => site_url(),
			'admin_ajax_url' => site_url() . '/wp-admin/admin-ajax.php',
			'plugin_url' => plugins_url( $this->plugin)
		);
		wp_localize_script('jquery', str_replace('-','',$this->plugin), $params);
	}

	public function render()
	{
		$optionLink = 'options-general.php?page=wptripolis_optionsscreen';
		$option     = get_option('wptripolis_optionsscreen');
		$tp         = $this->getClient();

		?>
			<h2>WP-Tripolis Shortcode Generator</h2>
		<?php


		if ( !isset($option['client_validated']) || !$option['client_validated']): ?>
			<div class="error"><?php echo sprintf(__('You have not entered your credentials yet. Go to the <a href="%s">settings page</a> first.','tripolis'),$optionLink) ?></div>
		<?php else:

			$databases = $tp->ContactDatabase()->all();

			?>
				<div id="shortcode">
					<code id="rendered_shortcode">[wptripolis]</code>
					<p><?php _e('Copy the shortcode to your clipboard and paste it in the widget/post/page to place your Tripolis Form') ?></p>
				</div>

				<h3>Options</h3>
				<form method="get" id="wptripolis-generator">
					<div id="typeption">
						<label><?php _e('Form','tripolis') ?></label>
					<select name="type" class="wptripols-gen-option">
						<option value="0">--Choose the form type--</option>
						<option value="subscribe"><?php _e('Subscribe','tripolis') ?></option>
						<option value="unsubscribe"><?php _e('Unsubscribe','tripolis') ?></option>
					</select>
					</div>
					<div id="unsubscribeoption" class="hidden">
						<label><?php _e('Unsubscribe method','tripolis') ?></label>
						<select name="action" class="wptripols-gen-option">
							<option value="0">--Choose unsubscribe method--</option>
							<option value="move"><?php _e('Move to group','tripolis') ?></option>
							<option value="delete"><?php _e('Delete from database','tripolis') ?></option>
						</select>
					</div>
					<div is="databaseoption">
						<label><?php _e('Contact Database','tripolis') ?></label>
					<?php if ( count($databases)): ?>
					<select name="database" class="wptripols-gen-option">
						<option value="0">- Choose a database -</option>
						<?php foreach( $databases as $db):  ?>
								<option value="<?php echo esc_attr($db->id) ?>"><?php echo esc_html($db->label) ?></option>
						<?php endforeach; ?>
					</select>
					<?php else: ?>
							<div class="error"><?php _e("No database found") ?></div>
					<?php endif; ?>
					</div>
					<div id="fieldoption"></div>
					<div id="targetgroup"></div>
				</form>
		<?php endif;
	}

	public function ajaxCallback()
	{
		if ( isset($_GET['data'])) {
			$data = $_GET['data'];
		} else {
			$data = array();
		}

		if ( isset($_GET['fn'])) {
			switch($_GET['fn']) {
				case 'fields': $result = $this->getFieldSelect($data); break;
				case 'groups': $result = $this->getGroups($data); break;
				default:
					$result = 'unsupported call: ' . $_GET['fn'];
			}

			echo json_encode($result);
		} else {
			echo "No such method";
		}
		exit();
	}


	protected function getFieldSelect($data)
	{
		if ( isset($data['database'])) {
			$tp = $this->getClient();

			try {
				$groups = $tp->contactDatabaseFieldGroup()->all($data['database']);

				$html = '<label>' . __('Form Fields','tripolis') . '</label><select name="fields" id="wptripolisfields" multiple size="10" class="wptripols-gen-option">';

				foreach( $groups as $group ) {
					$html .= '<optgroup label="' . esc_attr($group->label) . '">';

					$fields = $tp->contactDatabaseField()->getByContactDatabaseFieldGroupId($group->id);

					foreach( $fields as $field ) {
						if ( $field->required) {
							$required = ' data-required="1" selected';
						} else {
							$required = '';
						}
						$html .= '<option value="' . esc_attr($field->id) . '"' . $required . '>' . esc_html($field->label) . '</option>';
					}
					$html .= '</optgroup>';
				}
				$html .= '</select>';
				return $html;
			} catch( \Exception $e) {
				return $e->getMessage();
			}
		} else {
			return "Invalid method call";
		}
	}

	function getGroups($data)
	{
		if ( isset($data['database']) && isset($data['type'])) {
			$tp = $this->getClient();

			try {
				$groups = $tp->COntactGroup()->database($data['database'])->all();

				if ( $data['type'] == 'subscribe' ) {
					$label = __('Subscribers Group','tripolis');
				} else {
					$label = __('Unsubscribers Group','tripolis');
				}
				$html = '<label>' . $label . '</label>' .
								'<select name="' . $data['type'] . 'group" class="wptripols-gen-option ' . $data['type'] . '">'.
								'<option value="0">--Choose the form type--</option>';

				foreach( $groups as $group ) {
					$html .= '<option value="' . esc_attr($group->id) . '">' . esc_html($group->label) . '</option>';
				}
				$html .= '</select>';
				return $html;
			} catch (\Exception $e) {
				return $e->getMessage();
			}
		} else {
			echo "Missing parameters";
		}
	}

	protected function getClient()
	{
		$option     = get_option('wptripolis_optionsscreen');
		return new TripolisProvider(
				$option['client_account'],
				$option['client_username'],
				$option['client_password'],
				$option['client_environment']
		);
	}
}