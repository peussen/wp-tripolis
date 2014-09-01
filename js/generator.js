/**
 * Created by petereussen on 28/08/14.
 */

var wptripolis_shortcode = {
	type: '',
	database: '',
	fields: [],
	subscribegroup: '',
	ubsubscribegroup: ''
};


(function($){

	function bindOptions()
	{
		// Remove Any old handlers
		$(document).off('change','.wptripols-gen-option',wptripolisOptionChanged);
		$(document).off('change','#wptripolisfields',wpkeepmandatoryfields);
		// Rebind
		$(document).on('change','.wptripols-gen-option',wptripolisOptionChanged);
		$(document).on('change','#wptripolisfields',wpkeepmandatoryfields);
	}

	function wpkeepmandatoryfields()
	{
		$('#wptripolisfields option').each(function() {
			if ( $(this).data('required') ) {
				$(this).attr('selected','selected');
			}
		});
	}

	function wptripolisOptionChanged()
	{
		var selected = $(this).find('option:selected');

		if (selected.length > 1) {
			wptripolis_shortcode[$(this).attr('name')] = [];

			selected.each(function(){
				wptripolis_shortcode.fields.push($(this).val());
			});
		} else {
			wptripolis_shortcode[$(this).attr('name')] = selected.val();
		}

		$(document).wptripolis_generate();

		if ( $(this).attr('name') == 'database') {
			$('#fieldoption').html('<img src="' + wptripolis.plugin_url + '/img/loading.gif" />');

			wptripolis_shortcode.fields = [];

			$.ajax({
				url: wptripolis.admin_ajax_url,
				dataType: 'json',
				data: {
					'action': 'wp-tripolis_ajax',
					'fn': 'fields',
					'data': {'database': wptripolis_shortcode.database }
				},
				success: function (results ) {
					if ( results ) {

						$('#fieldoption').html(results);
						bindOptions();
						$(document).wptripolis_generate();
					}
				}
			});
		}

		if ( wptripolis_shortcode.database && wptripolis_shortcode.type &&
				($(this).attr('name') == 'database' || $(this).attr('name') == 'type')) {
			$('#targetgroup').html('<img src="' + wptripolis.plugin_url + '/img/loading.gif" />');
			wptripolis_shortcode.subscribegroup = '';
			wptripolis_shortcode.ubsubscribegroup = '';

			$.ajax({
				url: wptripolis.admin_ajax_url,
				dataType: 'json',
				data: {
					'action': 'wp-tripolis_ajax',
					'fn': 'groups',
					'data': {'database': wptripolis_shortcode.database, 'type' : wptripolis_shortcode.type }
				},
				success: function (results ) {
					if ( results ) {
						$('#targetgroup').html(results);
						bindOptions();
						$(document).wptripolis_generate();
					}
				}
			});
		}
	}

	$.fn.wptripolis_generate = function() {
		var text = '[wptripolis';

		if ( wptripolis_shortcode.type ) {
			text += ' type=' + wptripolis_shortcode.type;
		}

		if ( wptripolis_shortcode.database ) {
			text += ' database=' + wptripolis_shortcode.database;
		}

		if ( wptripolis_shortcode.fields.length ) {
			text += ' fields=' + wptripolis_shortcode.fields.toString();
		}

		if ( wptripolis_shortcode.subscribegroup ) {
			text += ' subscribegroup=' + wptripolis_shortcode.subscribegroup;
		}

		if ( wptripolis_shortcode.unsubscribegroup ) {
			text += ' unsubscribegroup=' + wptripolis_shortcode.unsubscribegroup;
		}
		text += ']';
		$('#rendered_shortcode').html(text)
	};

	bindOptions();
})(jQuery);