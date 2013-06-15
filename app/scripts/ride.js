(function($) {
	$(function() {
	//Initialize the Validation Engine
		$('form').validationEngine({
			'autoHidePrompt' : true,
			'autoHideDelay' : 5000,
			'validationEventTrigger' : 'submit'
		});
		
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
		tinymce.init({
			menubar  : false,
			plugins  : [ 'autolink contextmenu image link lists table textcolor' ],
			selector : 'textarea',
			toolbar  : 'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | undo redo | forecolor backcolor'
		});
		
	//Enable the cancel button to leave the form
		$('button.cancel').click(function() {
			var URL = document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'travel-assistant';
			document.location.href = URL;
		});
	});
})(jQuery)