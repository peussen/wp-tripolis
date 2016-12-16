(function($){

  var $document          = $(document),
  database               = '[data-tripolis="db"]',
  type                   = '[data-tripolis="type"]',
  dbFields               = '[data-tripolis="fields"]',
  contactGroup           = '[data-tripolis="contactgroup"] select',
  action                 = '[data-tripolis="unsubscribe-action"] select'
  currentDb              = $(database).val();
  availableFields        = [],
  availableContactGroups = [],
  loadImg                = '[data-ajax-load]',
  count                  = 0;

  
  //

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
        cursor : 'move',
        handle : '.handle-js',
        cancel : '',
        helper : fixHelper
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
    // console.log(data);

    if (data) {
      var form = JSON.parse(data);
      console.log(form);
    }

    if (form.type) {
      $(type).val(form.type);
    }
    if (form.db) {
     $(database).val(form.db);
    }
    if (form.contactgroup) {
     $(contactGroup).val(form.contactgroup);
    }


    getDbFields(false, function() {
      count ++;
      var savedFields = form.fields;

      if (savedFields) {
        $.each( savedFields, function(key, value) {
          var fieldLabel = value.altlabel,
          fieldId = value.field.id,
          fieldValue = value.field.value;
          $('option[value="' + fieldId + '"]', dbFields).prop('selected', true);
          addSelectedToTable();  
          // console.log(value.field.type);
          
          //all available field types, check which one it is
          var fieldType = '';
          switch (value.field.type) {
            case 'STRING':
              fieldType = 'text';
            break;
            case 'INTEGER':
              fieldType = 'number';
            break;
            case 'EMAIL':
              fieldType = 'e-mail';
            break;
            case 'BOOLEAN':
              fieldType = 'checkbox';
            break;
            case 'DECIMAL':
              fieldType = 'number';
            break;
            case 'DATE':
              fieldType = 'date';
            break;
            case 'DATETIME':
              fieldType = 'datetime';
            break;
            case 'PICKLIST':
              fieldType = 'select';
              selectOptions = value.field.options.picklistItem;
            break;
            case 'MOBILE':
              fieldType = 'tel';
            break;
            default: 
              fieldType = 'text';
            break;
          }
          if ($.inArray(fieldType, ['text', 'e-mail', 'date', 'datetime', 'number', 'tel', 'checkbox']) != -1) {
            console.log('input: ', fieldType);
            formOutput = $('<input />').prop({
                            'type': fieldType,
                            'name': value.field.label,
                          });
            
          } else if (fieldType === 'select') {
             console.log('select: ', fieldType);
             formOutput = $('<select />').prop('name', value.field.label);
             $.each(selectOptions, function(key, value) {
              $('<option />').
              prop('value', value.key).
              html(value.value).
              appendTo(formOutput);
            });      
          } else {

          }
          

          $('[data-tripolis="generate-form"]').append(
            $('<label>').html(fieldLabel),
            formOutput
            );
        });
      }
    });
  }

  function getFields()
  {
    // Object containing all fields we want to use
    var formArgs = {
      db: $(database).val(),
      type: $(type).val(),
      contactgroup:$(contactGroup).val(),
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

  function addSelectedToTable() 
  {
    $selectedFields = $(':selected', dbFields);

    $selectedFields.each(function(key, item){ 
      item = $(item);
      createDbFieldTableRow(item.attr('value'),item.html());
      item.remove();
    });

    $(dbFields).val('default');

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
    appendTo(dbFields);
  }


 // Empty fields when selecting new database
  function resetDbFields() 
  {

    $(dbFields + ' option').not(':eq(0)').remove();
    $(contactGroup + ' option').not(':eq(0)').remove();
    $('[data-tripolis="fields-selected"]').empty();

    tb_remove();
  }

  // Get fields from selected database
  function getDbFields(required, onReady) 
  {
    if (required == undefined) {
      required = true;
    }
    //reset options in selectbox and rows 
    resetDbFields();

    var currentDb = $(database).val();
    // loadimg positioning 
    $(loadImg).css('display', 'inline-block');

    var callUrl = ajaxurl + '?action=wptripolis_get_database_fields&db=' + currentDb;

    $.getJSON(callUrl, function(data) {

      if ( data.fields ) {
        availableFields = data.fields;
      }

      //put all the fields in the selectbox
      $.each( availableFields, function(key, value) {

        if ( required && value.required) { 
          //make table row for required fields      
          createDbFieldTableRow(value.id, value.label);
        } else {
          //restore all other fields as options in the selectbox
          restoreDbFieldOption(value.id, value.label);
        }
      });

    }).done(function() {
      //hide laodimage when done.
      $(loadImg).hide();
      //do other stuff when done
      if ( onReady !== undefined ) {
        onReady();
      }
    }); 

    getContactGroups();
  }

  function getContactGroups()
  {
    var currentDb = $(database).val(),
        type      = $(type).val(),
        callUrl = ajaxurl + '?action=wptripolis_get_contact_groups&db=' + currentDb + '&type=' + type;

    $.getJSON(callUrl, function(data) {
      availableContactGroups = data.contacts;
      // console.log(availableContactGroups);

      $.each( availableContactGroups, function(key, value) {

          $('<option />').
          prop('value', key).
          prop('data-id', key).
          html(value).
          appendTo($(contactGroup));
      });
    });
  }

  function showContactGroups()
  {
    if ($(type).val() == 'unsubscribe') {
      $action = $('[data-tripolis="unsubscribe-action"]');
      $action.show();


    }
  }

  function handleUnsubscribe()
  {

    if ($(action).val() == 'add') {
    // console.log($(contactGroup + ' label'));
      $(contactGroup + ' label').html('select your unsubscribe group');

    }

  }

  // Thickbox notification to confirm changing database
  function confirmDbChange() 
  {
    count ++; 

    if (count === 1 ) {
      getDbFields();
    } else {
      var url = '#TB_inline?inlineId=confirm_msg';
      tb_show("Let Op!", url, '');   
    }
  }

  function initForm()
  {
    sortableContent();
    getSavedData();
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

  });
  $document.on('change', action, handleUnsubscribe);
  $document.on('change', type, showContactGroups);
  $document.on('change' ,database, confirmDbChange);
  $document.on('click' ,'[data-confirm]', getDbFields);
  $document.on('click', '[data-cancel]', function() {
    $('[data-tripolis="db"]').val(currentDb);
    tb_remove();
  });

  $document.on('change', dbFields , addSelectedToTable);
  $document.on('click', '[data-deselect]',function() {
      restoreDbFieldOption($(this).closest('[data-value]').data('id'), $(this).closest('[data-value]').data('value'));
      $(this).closest('[data-value]').remove();
  });

  $document.on('click', '#publish', getFields);
  $document.ready(initForm);


})(jQuery);