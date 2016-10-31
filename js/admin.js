(function($){
  var $document = $(document);

  function sortableContent() 
  {
    //fix from foliotek for preserving table row width while dragging
    var fixHelper = function(e, tbody) {  
      tbody.children().each(function() {  
          $(this).width($(this).width());  
        });  
        return tbody;  
      }; 

    $('.sortable').sortable({
      cursor:'move',
      handle: '.handle',
      cancel: '',
      helper: fixHelper
    }).disableSelection();
  }

$(document).ready(sortableContent);

      // This will contain all the fields for the database
      var availableFields = [],
          count = 0; 

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
      //e.preventDefault();
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
    $('.sortable').sortable({
      cursor: 'move'
    }).disableSelection();
  }

  function addSelectOption(id, value)
  {
    $('<option />').
    prop('value',id).
    html(value).
    appendTo('[data-tripolis="fields"]');
  }


 //empty fields when selecting new
  function wptripolisResetFields() 
  {
    $('[data-tripolis="fields"]').not(':first').empty();
    $('[data-tripolis="fields-selected"]').empty();
    tb_remove();
  }

  
  function wptripolisGetFields() 
  {

      wptripolisResetFields();
      var $loadImg = $('.load');
      $loadImg.show();
      if ($('[data-tripolis="fields"]'))

      var dbSelected = $('[data-tripolis="db"]').val(),
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
      }).done(function() {
        $loadImg.hide();
      }); 
  }

  function wptripolisConfirmChange() 
  {
    count ++; 
    if (count === 1 ) {
      wptripolisGetFields();
    } else {
      var url = '#TB_inline?inlineId=confirm_msg';
      tb_show("hell yeah", url, '');     
    }
  }

  $document.on('click', '[data-tripolis-="add-field"]', function() {
    preventDefault();
  });

  $document.on('click', '[data-edit]', function() {
    var $toggle = $( this ),
        $targetId = $toggle.data('edit'),
        $target = $('[data-id="' + $targetId + '"]');
    if ($target.is('[readonly]')) {
      $toggle.html('save label');
      $target.prop('readonly',false);
    }  else {
      $toggle.html('edit label');
      $target.prop('readonly',true);
    }     
  });

  $document.on('change' ,'[data-tripolis="db"]', wptripolisConfirmChange);

  $document.on('click' ,'[data-confirm]', wptripolisGetFields);

  $document.on('change','[data-tripolis="fields"]',function() {

      $selectedFields = $(':selected', this);

      $selectedFields.each(function(key, item){ 
        item = $(item);
        addFieldToForm(item.attr('value'),item.html());
        item.remove();
      });
  });

  $document.on('click', '[data-selected]',function() {
      addSelectOption($(this).parent().data('id'), $(this).parent().data('value'));
      $(this).parent().remove();
  });
  $document.on('click', '[data-dismiss]',function() {
      addSelectOption($(this).parent().data('id'), $(this).parent().data('value'));
      $(this).parent().remove();
  });

  $document.on('click', '#publish', getFields);

})(jQuery);