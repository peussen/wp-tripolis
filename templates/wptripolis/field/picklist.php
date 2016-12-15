<div class="field-control">
  <label for="<?php echo $field->id; ?>" class="label"><?php echo esc_html($field->label) ?></label>
  <select data-wptripolis-field="<?php echo $field->id ?>" name="<?php echo $field->id ?>" id="<?php echo $field->id; ?>" class="field" <?php echo $field->required ? 'required' : ''; ?>>
    <?php foreach( $field->options as $key => $value ): ?>
      <option value="<?php echo $value ?>" <?php echo ($value === $field->default ? 'selected' : '') ?>><?php echo $key ?></option>
    <?php endforeach; ?>
  </select>
  <span class="message"></span>
</div>