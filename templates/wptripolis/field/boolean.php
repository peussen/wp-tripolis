<div class="form-group row">
  <div class="col-sm-9 offset-sm-3">
    <div class="form-check">
      <label for="<?php echo $field->id; ?>" class="form-check-label">
        <input class="form-check-input" type="checkbox" data-wptripolis-field="<?php echo $field->id ?>" name="<?php echo $field->id ?>" id="<?php echo $field->id; ?>" value="1" <?php echo $field->required ? 'required' : ''; ?>/>
        <?php echo esc_html($field->label) ?>
      </label>
      <span class="message"></span>
    </div>
  </div>
</div>