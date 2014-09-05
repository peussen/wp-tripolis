<div class="wptripolis form">
<?php
wptripolis_show_form_status();
?>
<h3><?php echo apply_filters(
			'wptripolis_unsubscribe_introduction',
			sprintf(__('Subscriptions for %s','tripolis'),wptripolis_get_contact_meta('email'),
			wptripolis_get_contact()));
?></h3>
<form method="post" action="<?php echo stripslashes( $_SERVER['REQUEST_URI']) ?>">
	<?php
	do_action('wptripolis_before_unsubscribe_form');
	wptripolis_get_contact_id_field();
	wp_nonce_field('wptripolis-unsubscribe' . wptripolis_get_contact_id(),wptripolis_form_field('_wpnonce'));
	?>
	<ul class="wptripolis-subscribelist">

	<?php foreach( wptripolis_get_subscriptions() as $subscription):;
		?>
		<?php if ( !$subscription->isArchived ): ?>
		<li class="wptripolis-subscription">
			<input type="checkbox" id="<?php echo wptripolis_form_field_id($subscription->id) ?>" name="<?php echo wptripolis_form_field('retain') ?>[]" <?php echo $subscription->subscribed ? 'checked="checked"' : '' ?> value="<?php echo esc_attr($subscription->id) ?>">
			<label class="checkbox-label" or="<?php echo wptripolis_form_field_id($subscription->id) ?>"><?php echo esc_html(apply_filters(wptripolis_plugin_name() . '_group-label',$subscription->label,$subscription)) ?></label>
		</li>
		<?php endif; ?>
	<?php endforeach ?>
	</ul>
	<input type="submit" value="<?php echo apply_filters(wptripolis_plugin_name() . '_unsubscribe-submit-label',__('Unsubscribe','tripolis')); ?>" />
	<?php do_action('wptripolis_after_unsubscribe_form'); ?>
</form>
</div>