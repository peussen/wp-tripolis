(function($){

  var $document = $(document),
      database = '[data-tripolis="db"]',
      currentDb = $(database).val();
      availableFields = [],
      loadImg = '[data-ajax-load]',
      count = 0; 
console.log('initial: ' + currentDb);
  
  //initialize sortable
  function sortableContent() 
  {
    var $sortable = $('.sortable-js');

    //fix from foliotek for preserving table row width while dragging
    var fixHelper = function(e, tbody) {  
      tbody.children().each(function() {  
          $(this).width($(this).width());  
        });  
        return tbody;  
      }; 

    if ($sortable.children().length > 1) {
      $sortable.sortable({
        cursor:'move',
        handle: '.handle-js',
        cancel: '',
        helper: fixHelper
      }).disableSelection();   
    }
  }

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

  function getSavedData()
  {

    var data = $('[data-tripolis="send-data"]').val();

    JSON.parse(data, (key, value) => {
console.log(key + " : " + value);
    });

  }

  function getFields()
  {
    // Object containing all fields we want to use
    var formArgs = {
      db: $(database).val(),
      type: $('[data-tripolis="type"]').val(),
      contactgroup:'',
      fields:[]
    };

    $('[data-tripolis="fields-selected"] tr').each(function() {
        id = $(this).data('id');
        var fieldsUsed = {
          field: getAvailableObjectbyId(id),
          altlabel: $(this).data('value')
        };

       
        formArgs.fields.push(fieldsUsed);
    });

    $('[data-tripolis="send-data"]').val(JSON.stringify(formArgs));

  }

  function createDbFieldTableRow(id, value)
  {
    // Search field with ID in available Fields
    var field = getAvailableObjectbyId(id);

    $('<tr />').
    attr('data-value',value).
    attr('data-id',id).
    append(
      $('<td />').html('<button class="handle-js" data-handle type="button">move</button>'),
      $('<td />').html(value),
      $('<td />').html('<input type="text" name="'+ id +'"  value="'+ value +'" readonly>'),
      $('<td />').html('<button type="button" data-edit="'+ id +'">edit label</button>'),
      $('<td />').html((field.required ? '' : '<button  type="button" data-deselect="'+ id +'">delete</button>'))
    ).
    appendTo('[data-tripolis="fields-selected"]');

    sortableContent();
  }

  function restoreDbFieldOption(id, value)
  {
    $('<option />').
    prop('value',id).
    html(value).
    appendTo('[data-tripolis="fields"]');
  }


 // Empty fields when selecting new database
  function resetDbFields() 
  {
    $('[data-tripolis="fields"]').not(':first').empty();
    $('[data-tripolis="fields-selected"]').empty();
    tb_remove();
  }

  // Get fields from selected database
  function getDbFields() 
  {
      currentDb = $(database).val();
      resetDbFields();
      $(loadImg).css('display', 'inline-block');
      if ($('[data-tripolis="fields"]'))

      var dbSelected = $(database).val(),
          callUrl = ajaxurl + '?action=wptripolis_get_database_fields&db=' + dbSelected;

      $.getJSON(callUrl, function(data) {

        if ( data.fields ) {
          availableFields = data.fields;
        }

        //put all the fields in the selectbox
        $.each( availableFields, function(key, value) {

          if ( value.required ) {
            createDbFieldTableRow(value.id, value.label);
          } else {
            restoreDbFieldOption(value.id, value.label);
          }
        });
      }).done(function() {
        $(loadImg).hide();
      }); 
  }
  // Thickbox notification to confirm changing database
  function confirmDbChange() 
  {
console.log('confirm change: ' + currentDb);
    count ++; 

    if (count === 1 ) {
      getDbFields();
    } else {
      var url = '#TB_inline?inlineId=confirm_msg';
      tb_show("Let Op!", url, '');   
    }
  }

  $document.on('click', '[data-edit]', function() {
    var $toggle = $( this ),
        $targetId = $toggle.data('edit'),
        $target = $('input[name="' + $targetId + '"]');

    if ($target.is('[readonly]')) {

      $toggle.html('save label');
      $target.prop('readonly',false);

    } else {

      $toggle.closest('tr').attr('data-value',$target.val());
      $toggle.html('edit label');
      $target.prop('readonly',true);

    }     
  });

  $document.on('focus' ,database, function() {
    currentDb = $( this ).val();
console.log('focus and currentBd set to: ' + currentDb);
  });

  $document.on('change' ,database, confirmDbChange);
  $document.on('click' ,'[data-confirm]', getDbFields);
  $document.on('click', '[data-cancel]', function() {
    $('[data-tripolis="db"]').val(currentDb);
    tb_remove();
console.log('closed and thus unchanged: ' + currentDb);

  });
  $document.on('change','[data-tripolis="fields"]',function() {

      $selectedFields = $(':selected', this);

      $selectedFields.each(function(key, item){ 
        item = $(item);
        createDbFieldTableRow(item.attr('value'),item.html());
        item.remove();
      });
      $( this ).val('default');
  });

  $document.on('click', '[data-deselect]',function() {
      restoreDbFieldOption($(this).closest('[data-value]').data('id'), $(this).closest('[data-value]').data('value'));
      $(this).closest('[data-value]').remove();
  });

  $document.on('click', '#publish', getFields);
  $document.ready(sortableContent);

})(jQuery);