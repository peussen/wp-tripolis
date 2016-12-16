
;(function($){

  $(document).on('submit','[data-wptripolis]',function(e) {
    var formId     = $(this).data('wptripolis'),
        form       = $(this),
        formStatus = $(this).find('[data-wptripolis-form-message]');
        payload    = {
          form: formId,
          action: 'wptripolis_form',
          fields: {}
        };

    $(this).find('[data-wptripolis-field]').each(function() {
      payload.fields[$(this).attr('id')] = $(this).val();
    });

    formStatus.html(wptripolis_forms.submitting);

    $.post(wptripolis_forms.ajaxurl,payload,function(data) {
      formStatus.html(data.message)
      if ( data.status ) {
        formStatus.addClass('success').removeClass('error');
        form.addClass('completed');
        form.find('.wptripolis_form__field-container').addClass('hidden-xs-up');
      } else {
        formStatus.addClass('error').removeClass('success');
        form.removeClass('completed');
        form.find('.wptripolis_form__field-container').removeClass('hidden-xs-up');
      }
    },'json');

    // Prevent normal submit
    e.preventDefault();
  });
})(jQuery);