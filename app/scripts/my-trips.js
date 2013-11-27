(function($) {
	$(function() {
	//Listen for a trip delete request
		$('button.delete').click(function() {
			var button = $(this);
			
		//Build the contents of the confirm dialog
			var HTML = '<div class="modal hide fade confirm-dialog" role="dialog">';
			HTML += '<div class="modal-header">';
			HTML += '<button type="button" class="close" data-dismiss="modal">×</button>';
			HTML += '<h3>Delete a Trip</h3>';
			HTML += '</div>';
			HTML += '<div class="modal-body confirm-dialog-details">';
			HTML += '<p>This action will delete this trip from the Travel Assistant. Your trip will be permanently deleted and cannot be restored.<br><br>Do you wish to continue?</p>';
			HTML += '</div>';			
			HTML += '<div class="modal-footer">';
			HTML += '<span class="validate"></span>';
			HTML += '<button class="btn btn-danger confirm">Yes</button>';
			HTML += '<button class="btn close-dialog" data-dismiss="modal">No</button>';
			HTML += '</div>';
			HTML += '</div>';
			
		//Create the Twitter Bootstrap dialog
			var modal = $(HTML);
			var submit = modal.find('button.confirm');
			var validation = modal.find('span.validate');
			
			modal.modal();
			
		//Send the delete
			submit.click(function() {
				submit.attr('disabled', 'disabled').html('Please wait...');
				validation.text('');
				
			//Send the process request to the server
				$.ajax({
					'data' : {
						'ID'   : button.attr('data-id'),
						'type' : button.attr('data-type')
					}, 
					'type' : 'POST',
					'url' : document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'wp-content/plugins/travel-assistant/app/includes/ajax/delete.php',
					'success' : function(data) {
					//If the transaction was successful, close the dialog
						if (data == 'success') {
						//Hide the modal dialog
							modal.modal('hide');
							
						//Decrement the total trip counter near the top of the page
							var total;
							var value;

							if (button.parent().parent().hasClass('needed')) {
								total = $('li.needed p span.count');
								value = parseInt(total.text());
								total.text(--value);
								
								value == 1 ? total.next('span').text('Trip I\'ve Needed') : total.next('span').text('Trips I\'ve Needed');
							}
							
							if (button.parent().parent().hasClass('shared')) {
								total = $('li.shared p span.count');
								value = parseInt(total.text());
								total.text(--value);
								
								value == 1 ? total.next('span').text('Active Recurring Trip') : total.next('span').text('Active Recurring Trips');
							}

						//Show the "Ask/Share a Ride" prompt, if the user deleted the last of the rides
							if (!button.parent().siblings('li').length) {
								!button.parent().parent().siblings('div.none').show();
							}
							
						//Remove the trip from the list
							button.parent().remove();
							
						//Create a success message
							var message = $('<span class="success"/>');
							message.appendTo('body').text('Your trip has been deleted.');
							
							setTimeout(function() {
								message.fadeOut(500, function() {
									message.remove();
								});
							}, 5000);
						} else {
						//Populate the message alert with text
							validation.text(data);
							
						//Only after it contains text can jQuery evaluate whether or not it is visible (responsive CSS styles may hide it)
							if (validation.is(':hidden')) {
								alert(data);
							}
							
						//Restore the submit button
							submit.removeAttr('disabled').html('Yes');
						}
					}
				});
			});
		});
	});
})(jQuery)