(function($) {
	$(function() {
		$('input#when').datetimepicker({
			'minDate' : new Date(),
			'showButtonPanel' : false,
			'showTimezone' : true,
			'timeFormat' : 'hh:mm tt z',
			'timezone' : 'ET',
			'timezoneList' : [ 
				{ 'value' : 'ET', 'label' : 'Eastern'  }, 
				{ 'value' : 'CT', 'label' : 'Central'  }, 
				{ 'value' : 'MT', 'label' : 'Mountain' }, 
				{ 'value' : 'PT', 'label' : 'Pacific'  } 
			]
		});
		
		tinyMCE.init({
			mode : 'textareas',
			theme : 'advanced',
			skin : 'o2k7',
			skin_variant : 'silver',
			plugins :'inlinepopups,spellchecker,tabfocus,autosave,autolink',
			theme_advanced_buttons1 : 'bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink',
			theme_advanced_buttons2 : '',
			theme_advanced_buttons3 : '',
			theme_advanced_resizing : true,
			theme_advanced_statusbar_location : 'bottom',
			theme_advanced_toolbar_location : 'top',
			theme_advanced_toolbar_align : 'left'
		});
		
		$( ".slider" ).slider({
			value:1,
			min: 1,
			max: 9,
			step: 1,
			range: "min"
    	});
	});
})(jQuery)