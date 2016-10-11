(function($){
  //app sandbox
  $db = $('#database');

  $(document.body).on('click', '[data-tripolis-="add-field"]', function() {
    preventDefault();
  });

  $(document).on('change' ,'[data-tripolis="db"]', wptripolisGetFields);

  function wptripolisResetFields() {
    $('[data-tripolis="fields"]').empty();
    $('[data-tripolis="fields-selected"]').empty();
  }
  
  function wptripolisGetFields() {

      wptripolisResetFields();
      var dbSelected = $(this).val(),
          callUrl = ajaxurl + '?action=wptripolis_get_database_fields&db=' + dbSelected;

      $.getJSON(callUrl, function(data) {

        //put all the fields in the selectbox
        $.each( data.fields, function(key, value) {

          $("<option>" + value.label + "</option>").appendTo('[data-tripolis="fields"]');

        });

        //add selected fields to 
        $('[data-tripolis="fields"]').on('change', function() {
          console.log('changed');
          $(':selected', this).remove().appendTo('[data-tripolis="fields-selected"]');
        });
      }); 
  }




})(jQuery);