<div class="wptripolis form">
	<?php
	wptripolis_show_form_status();
	?>
	<form method="post" id="<?php echo $id ?>_form" class="<?php echo $this->plugin ?> <?php echo $class ?>">
	<input type="hidden" value="<?php echo $type ?>" name="type" />

	<?php
	do_action('wptripolis_before_subscribe_form');

	while( wptripolis_have_fields() ):
		$template = wptripolis_find_template('field/' . wptripolis_get_field_type() . '.php');

		if ( $template ) {
			require($template);
		} else {
			$template = wptripolis_find_template('field/generic.php');
			require($template);
		}
	endwhile;

	?>
	<input type="submit" value="<?php echo apply_filters(wptripolis_plugin_name() . '_submit-label',__('Subscribe','tripolis')) ?>" class="<?php wptripolis_plugin_name() ?> submit"/>
	<?php do_action('wptripolis_after_subscribe_form'); ?>

</form>
</div>