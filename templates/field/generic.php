
<p class="field-control">
	<label for="<?php wptripolis_field_id() ?>" class="label <?php wptripolis_field_classes() ?>"><?php echo esc_html(__(wptripolis_field_label(),'tripolis')) ?></label>
	<input type="text" name="<?php wptripolis_field_name() ?>" id="<?php wptripolis_field_id() ?>" class="field <?php wptripolis_field_classes() ?> <?php echo strtolower(wptripolis_get_field_type()) ?>" value="<?php echo esc_attr(wptripolis_get_field_value()) ?>" <?php wptripolis_field_required() ?>/>
	<?php if ( wptripolis_get_field_message()): ?>
	<span class="message"><?php esc_html(wptripolis_get_field_message()) ?></span>
	<?php endif; ?>
</p>
