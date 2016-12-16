<div class="form-group row">
  <label for="<?php echo $field->id; ?>" class="col-form-label col-sm-3"><?php echo esc_html($field->label) ?></label>
  <div class="col-sm-9">
    <select data-wptripolis-field="<?php echo $field->id ?>" name="<?php echo $field->id ?>" id="<?php echo $field->id; ?>" class="field form-control" <?php echo $field->required ? 'required' : ''; ?>>
      <?php foreach( $field->options as $key => $value ): ?>
        <option value="<?php echo $value ?>" <?php echo ($value === $field->default ? 'selected' : '') ?>><?php echo $key ?></option>
      <?php endforeach; ?>
    </select>
    <span class="message help-block"></span>
  </div>
</div>