<div class="field-control">
  <label for="<?php echo $field->id; ?>" class="label"><?php echo esc_html($field->label) ?></label>
  <input type="checkbox" data-wptripolis-field="<?php echo $field->id ?>" name="<?php echo $field->id ?>" id="<?php echo $field->id; ?>" class="field" value="1" <?php echo $field->required ? 'required' : ''; ?>/>
  <span class="message"></span>
</div>