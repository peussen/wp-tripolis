<form method="post" action="#" data-wptripolis="<?= $wpform->id ?>" class="form-horizontal wptripolis__form--<?= $wpform->action; ?>">
  <div data-wptripolis-form-message class="wptripolis_form__message"></div>
  <div class="wptripolis_form__field-container">
    <input type="hidden" name="form" value="<?= $wpform->id ?>" />
    <?php foreach( $wpform->fields as $field ): ?>
      <?php $template->with(['field' => $field])->display('templates/wptripolis/field/' . $field->type); ?>
    <?php endforeach; ?>
    <div class="'form-group row">
      <div class="offset-sm-3 col-sm-9">
        <button type="submit" class="btn btn-primary"><?php _e('Submit','wptripolis'); ?></button>
      </div>
    </div>
  </div>
</form>