<div class="field-control">
  <label for="<?php echo $field->id; ?>" class="label"><?php echo esc_html($field->label) ?></label>
  <input type="datetime-local" data-wptripolis-field="<?php echo $field->id ?>" name="<?php echo $field->id ?>" id="<?php echo $field->id; ?>" class="field" value="<?php echo esc_attr($field->default) ?>" <?php echo $field->required ? 'required' : ''; ?>/>
  <span class="message"></span>
</div>