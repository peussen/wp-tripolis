(function($){

  var $document = $(document),
      database = '[data-tripolis="db"]',
      currentDb = $(database).val();
      availableFields = [],
      count = 0; 
  console.log('initial' + currentDb);
  
  //initialize sortable
  function sortableContent() 
  {
   var $sortable = $('.sortable');
    //fix from foliotek for preserving table row width while dragging
    var fixHelper = function(e, tbody) {  
      tbody.children().each(function() {  
          $(this).width($(this).width());  
        });  
        return tbody;  
      }; 

    if ($sortable.children().length > 1) {
      $('.sortable').sortable({
        cursor:'move',
        handle: '.handle-js',
        cancel: '',
        helper: fixHelper
      }).disableSelection();   
    }
  }

  $document.ready(sortableContent);

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
      db: $('[data-tripolis="db"]').val(),
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

    var magic = JSON.stringify(formArgs);
    $('[data-tripolis="send-data"]').val(magic);

  }

  function addSelectTable(id, value)
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

  function addSelectOption(id, value)
  {
    $('<option />').
    prop('value',id).
    html(value).
    appendTo('[data-tripolis="fields"]');
  }


 // Empty fields when selecting new database
  function wptripolisResetFields() 
  {
    $('[data-tripolis="fields"]').not(':first').empty();
    $('[data-tripolis="fields-selected"]').empty();
    tb_remove();
  }

  // Get fields from selected database
  function wptripolisGetFields() 
  {
      currentDb = $('[data-tripolis="db"]').val();
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
            addSelectTable(value.id, value.label);
          } else {
            addSelectOption(value.id, value.label);
          }
        });
      }).done(function() {
        $loadImg.hide();
      }); 
  }
  // Thickbox notification to confirm changing database
  function wptripolisConfirmChange() 
  {
    console.log('confirmchange' + currentDb);
    count ++; 

    if (count === 1 ) {
      wptripolisGetFields();
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

  $document.on('focus' ,'[data-tripolis="db"]', function() {
    currentDb = $( this ).val();
     console.log('focus' + currentDb);
  });

  $document.on('change' ,'[data-tripolis="db"]', wptripolisConfirmChange);
  $document.on('click' ,'[data-confirm]', wptripolisGetFields);
  $document.on('click', '#TB_closeWindowButton', function() {
    $('[data-tripolis="db"]').val(currentDb);
    console.log('closed' + currentDb);

  });
  $document.on('change','[data-tripolis="fields"]',function() {

      $selectedFields = $(':selected', this);

      $selectedFields.each(function(key, item){ 
        item = $(item);
        addSelectTable(item.attr('value'),item.html());
        item.remove();
      });
      $( this ).val('default');
  });

  $document.on('click', '[data-deselect]',function() {
      addSelectOption($(this).closest('[data-value]').data('id'), $(this).closest('[data-value]').data('value'));
      $(this).closest('[data-value]').remove();
  });

  $document.on('click', '#publish', getFields);

})(jQuery);