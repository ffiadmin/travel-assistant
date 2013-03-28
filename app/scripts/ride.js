(function($) {
	$(function() {
	//Initialize the Validation Engine
		$('form').validationEngine();
		
	//Initialize the date/time picker
		$('input#when').datetimepicker({
			'minDate' : new Date(),
			'showButtonPanel' : false,
			'showTimezone' : true,
			'stepMinute' : 10,
			'timeFormat' : 'hh:mm tt z',
			'timezone' : 'ET',
			'timezoneList' : [ 
				{ 'value' : 'ET', 'label' : 'Eastern'  }, 
				{ 'value' : 'CT', 'label' : 'Central'  }, 
				{ 'value' : 'MT', 'label' : 'Mountain' }, 
				{ 'value' : 'PT', 'label' : 'Pacific'  } 
			]
		});
		
	//Toggle the trip recurrence days
		var recurringDays = $('input.recurring-day'), recurringLabels = $('label.recurring-label'), untilField = $('input#until');
	
		$('label#recurring-yes-label').click(function() {
			recurringDays.removeAttr('disabled');
			recurringLabels.removeClass('disabled');
			untilField.removeAttr('disabled');
		});
		
		$('label#recurring-no-label').click(function() {
			recurringDays.attr('disabled', 'disabled').removeAttr('checked');
			recurringLabels.addClass('disabled').removeClass('active');
			untilField.val('').attr('disabled', 'disabled');
		});
		
	//Initialize the date picker
		$('input#until').datepicker({
			'minDate' : new Date() 
		});
		
	//Initialize TinyMCE
		tinyMCE.init({
			mode : 'textareas',
			skin : 'o2k7',
			skin_variant : 'silver',
			theme : 'advanced',
			
			plugins :'inlinepopups,spellchecker,tabfocus,autosave,autolink',
			theme_advanced_buttons1 : 'bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,separator,undo,redo',
			theme_advanced_buttons2 : '',
			theme_advanced_buttons3 : '',
			theme_advanced_resizing : true,
			theme_advanced_statusbar_location : 'bottom',
			theme_advanced_toolbar_location : 'top',
			theme_advanced_toolbar_align : 'left'
		});
		
	//Enable the cancel button to leave the form
		$('button.cancel').click(function() {
			var URL = document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'travel-assistant';
			document.location.href = URL;
		});
	});
})(jQuery)