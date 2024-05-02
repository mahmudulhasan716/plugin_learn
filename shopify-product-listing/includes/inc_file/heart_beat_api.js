(function ($) {
	// hook into heartbeat-send: client logs data to console
	$(document).on('heartbeat-send', function (e, data) {
		data = data ?? {};
		data.message = $('#my-input').val();

		$('#my-input').val('');
	});

	// hook into heartbeat-tick: client logs server response to console
	$(document).on('heartbeat-tick', function (e, data) {
		if (data.status === 'success' && data.message) {
			//console.log('Response from server: ', data);

			$.ajax({
				// url: '/wp-admin/admin-ajax.php',
				method: 'POST',
				data: {
					action: 'get_data_from_other_user',
				},
				success: function (response) {
					// Handle received data
					console.log('Data received from another user');
					//$('#received-message').text(response);

					$('#received-message').text(data['message']);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error('Error receiving data from another user:', errorThrown);
				},
			});
		}
	});

	// hook into heartbeat-error: client logs error to console
	$(document).on('heartbeat-error', function (e, jqXHR, textStatus, error) {
		console.log('Error: ', jqXHR, textStatus, error);
	});

	$('#send_message_button').on('click', function (e) {
		e.preventDefault();
		// Get message content from input field
		var messageContent = $('#my-input').val();

		// Ensure message content is not empty
		if (messageContent.trim() === '') {
			alert('Please enter a message.');
			return;
		}

		// Get recipient ID from hidden input field
		var recipientId = $('#receiver_id').val();

		var data = {
			action: 'send_message',
			message: messageContent,
			receiver_id: recipientId,
		};

		$.ajax({
			url: '/wp-admin/admin-ajax.php',
			method: 'POST',
			data: data,
			success: function (response) {},
		});

		// $.ajax({
		//     type : "post",
		//     dataType : "json",
		//     url : '<?php echo esc_url( admin_url( '--'));?>',
		//     data : {
		//         action: "send_message",
		//         nonce : $('#send_message_nonce').val(),
		//         message: messageContent,
		//         recipient_id: recipientId
		//     },
		//     success: function( response ) {
		//         console.log(response);
		//     }
		// });
	});

	wp.heartbeat.interval('fast');
})(jQuery);
