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
	});
})(jQuery)

//Custom function for validating that the cities are not the same
	function locDifferent(field, rules, i, options) {
		var fromCity = $('input#from-where-city').val();
		var fromState = $('input#from-where-state').val();
		var toCity = $('input#to-where-city').val();
		var toState = $('input#to-where-state').val();

		if (fromCity == toCity && fromState == toState) {
			return "The origin and destination locations must be different";
		}
	}
