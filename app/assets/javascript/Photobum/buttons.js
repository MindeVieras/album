
Photobum.updateSeo = function () {
    var data = {
        id: $('#add_user #seo_id').val(),
        url: $('#add_user #url').val(),
        title: $('#add_user #title').val(),
        desc: $('#add_user #description').val()
    };
    $.ajax({
        type: "POST",
        data: data,
        url: '/admin/seo/add',
        dataType: "json",
        success: function (data) {
            if (data.ack == 'OK') {
                $('.alertholder').text('').removeClass('alert').removeClass('alert-danger');
                Photobum.closeModal(true);
            }
            else {
                $('.alertholder').text(data.msg).addClass('alert').addClass('alert-danger');
            }
        }
    });
};

Photobum.subscribeForm = function () {

    $('.subscribe-message').text('');
    $('.subscribe-message').css('visibility','hidden');
    var name = $('#edit-submitted-name').val();
    var email = $('#edit-submitted-email').val();
    if (!name || !email ) {
        return;
    }
    var data = {
        name: name,
        email: email
    };
    $.ajax({
        type: "POST",
        data: data,
        url: '/api/subscribe',
        dataType: "json",
        success: function (resp) {
            if (resp.ack == 'OK') {
                $('.subscribe-message').text('Thanks for subscribing');
                $('.subscribe-message').css('visibility','visible');
                $('.subscribe-form-holder').hide();
            }
            else {
                $('.subscribe-message').text(resp.msg);
                $('.subscribe-message').css('visibility','visible');
            }
        }
    });
};
