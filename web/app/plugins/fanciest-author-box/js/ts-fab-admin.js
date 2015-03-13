jQuery(document).ready(function($) {

	// Color picker
	$('.pickcolor').click(function(e) {
		colorPicker = $(this).next('div');
		input = $(this).prev('input');
		clicked = $(this);
	
		$.farbtastic($(colorPicker), function(a) {
			$(input).val(a);
			$(clicked).css('background', a);
		});
	
		colorPicker.show();
		e.preventDefault();
	
		$(document).mousedown( function() { $(colorPicker).hide(); });
	});

	$('.ts-fab-color-input').keyup( function() {
		var a = $(this).val(),
			b = a;

		a = a.replace(/[^a-fA-F0-9]/, '');
		if ( '#' + a !== b )
			$(this).val(a);
		if ( a.length === 3 || a.length === 6 ) {
			$(this).val( '#' + a );
			$(this).parent().find('.pickcolor').css('background', '#' + a);
		}
	});
			
	// Reset default colors
	$('#ts-fab-reset-colors').click(function() {
		$('#inactive_tab_background').val('#fbfbf9');
		$('#pickcolor_inactive_tab_background').css('background', '#fbfbf9');
		$('#inactive_tab_border').val('#f2f2ef');
		$('#pickcolor_inactive_tab_border').css('background', '#f2f2ef');
		$('#inactive_tab_color').val('#404040');
		$('#pickcolor_inactive_tab_color').css('background', '#404040');

		$('#active_tab_background').val('#f2f2ef');
		$('#pickcolor_active_tab_background').css('background', '#f2f2ef');
		$('#active_tab_border').val('#f2f2ef');
		$('#pickcolor_active_tab_border').css('background', '#f2f2ef');
		$('#active_tab_color').val('#252525');
		$('#pickcolor_active_tab_color').css('background', '#252525');

		$('#tab_content_background').val('#f2f2ef');
		$('#pickcolor_tab_content_background').css('background', '#f2f2ef');
		$('#tab_content_border').val('#f2f2ef');
		$('#pickcolor_tab_content_border').css('background', '#f2f2ef');
		$('#tab_content_color').val('#404040');
		$('#pickcolor_tab_content_color').css('background', '#404040');
		
		return false;
	});
	
	$('#custom').change(function() {
		$('#ts_fab_custom_tab_extra').slideToggle(100);
	});

});