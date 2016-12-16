<div class="form-group row">
  <label for="<?php echo $field->id; ?>" class="col-sm-3 col-form-label"><?php echo esc_html($field->label) ?></label>
  <div class="col-sm-9">
    <input type="text" data-wptripolis-field="<?php echo $field->id ?>" name="<?php echo $field->id ?>" id="<?php echo $field->id; ?>" class="field form-control" value="<?php echo esc_attr($field->default) ?>" <?php echo $field->required ? 'required' : ''; ?>/>
    <span class="message help-block"></span>
  </div>
</div>