PhotobumAdmin.saveAccount = function (info, btn) {

	var data = {
		colors: $('.color-changed').map(function(){
			return {
				id: $(this).attr('data-id'),
				code: $(this).attr('data-code')
			}
		}).get(),
		type: 'person'
	};
	console.log(data);
	$.ajax({
		type: "POST",
		data: data,
		url: '/admin/account/save',
		dataType: "json",
		success: function (data) {
			console.log(data);
			if (data.ack == 'ok') {

				$('.color-changed').removeClass('color-changed');
				Photobum.setMsg(data.msg, 'success', 5);
			}
			else {
				//$('.alertholder').text(data.msg).addClass('alert').addClass('alert-danger');
				//Photobum.initView();
				//Photobum.scrollToTopOfModal();
			}
		}
	});

};
