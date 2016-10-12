(function($){

  var $selectboxFields = $('[data-tripolis="fields"]'),
      $listFields = $('[data-tripolis="fields-selected"]'),

      // This will contain all the fields for the database
      availableFields = [];

    $(document).on('click', '#publish', getFields);

    function getAvailableObjectbyId(id) 
    {
      var field = availableFields.filter(function (obj) {
        return obj.id == id;
      });

      if ( !field.length ) {
        return;
      }

      field = field[0];

      return field;
    }

  function getFields(e)
  {
      e.preventDefault();
      // Object containing all fields we wantto use
      var fieldsUsed = {
        db: $('[data-tripolis="db"]').val(),
        type: $('[data-tripolis="type"]').val(),
        contactgroup:'',
        fields:[]
    };

    $('[data-tripolis="fields-selected"] li').each(function() {

        var field = getAvailableObjectbyId($(this).data('id'));
        fieldsUsed.fields.push(field);
    });
    // console.log(fieldsUsed);
    var magic = JSON.stringify(fieldsUsed);
    $('[data-tripolis="send-data"]').val(magic);

  }

  function addFieldToForm(id, value)
  {
    // Zoek veld met ID in available Fields
    var field = getAvailableObjectbyId(id);

    $('<li />').
    data('id',id).
    data('value',value).
    prop('class', field.required ? ' required' : '').
    html(value + (field.required ? '' : '<span data-selected>X</span>')).
    appendTo('[data-tripolis="fields-selected"]');
    $('.sortable').sortable().disableSelection();
    console.log($listFields);
  }

  function addSelectOption(id, value)
  {
    $('<option />').
    prop('value',id).
    html(value).
    appendTo('[data-tripolis="fields"]');
  }

  $('.sortable').sortable().disableSelection();

  $(document.body).on('click', '[data-tripolis-="add-field"]', function() {
    preventDefault();
  });

  $(document).on('change' ,'[data-tripolis="db"]', wptripolisGetFields);
  $(document).on('change','[data-tripolis="fields"]',function() {
      $selectedFields = $(':selected', this);

      $selectedFields.each(function(key, item){ 
        item = $(item);
        addFieldToForm(item.attr('value'),item.html());
        item.remove();
      });
  });
  $(document).on('click', '[data-selected]',function() {
      addSelectOption($(this).parent().data('id'), $(this).parent().data('value'));
      $(this).parent().remove();
  });

 //empty fields when selecting new
  function wptripolisResetFields() {
    $selectboxFields.empty();
    $('[data-tripolis="fields-selected"]').empty();
  }

  
  function wptripolisGetFields() {

      wptripolisResetFields();

      var dbSelected = $(this).val(),
          callUrl = ajaxurl + '?action=wptripolis_get_database_fields&db=' + dbSelected;

      $.getJSON(callUrl, function(data) {

        if ( data.fields ) {
          availableFields = data.fields;
        }

        //put all the fields in the selectbox
        $.each( availableFields, function(key, value) {

          if ( value.required ) {
            addFieldToForm(value.id, value.label);
          } else {
            addSelectOption(value.id, value.label);
          }

        });
      }); 
  }

})(jQuery);