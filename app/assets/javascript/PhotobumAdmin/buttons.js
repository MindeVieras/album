PhotobumAdmin.addUser = function () {
    var data = {
        id: $('#add_user #user_id').val(),
        email: $('#add_user #email').val(),
        display_name: $('#add_user #display_name').val(),
        password: $('#add_user #password').val(),
        confirm_password: $('#add_user #confirm_password').val(),
        attribution_name: $('#add_user #attribution_name').val(),
        access_level: $('#add_user #access_level').val(),
        status: $('#add_user #status').bootstrapSwitch('state')
    };
    $.ajax({
        type: "POST",
        data: data,
        url: '/admin/user/add',
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

PhotobumAdmin.deleteUser = function (info, btn) {
    Photobum.dialog({
        message: 'Comfirm deletion of ' + info.user,
        title: "<i class=\"foreground news fa fa-exclamation-circle pad-right\"></i>Warning",
        buttons: {
            main: {
                label: "No! I've changed my mind",
                className: "btn btn-success",
                dismiss: true
            },
            danger: {
                label: "Yes! Delete them.",
                className: "btn btn-danger",
                dataFunction: "PhotobumAdmin.doDeleteUser",
                additionalData: {
                    user: info.id,
                    ladda: true
                },
                dismiss: false
            }
        }
    });
};

PhotobumAdmin.doDeleteUser = function (info, btn) {
    $.ajax({
        type: "DELETE",
        url: '/admin/user/delete/id/' + info.user,
        data: JSON.stringify({id: info.user}),
        dataType: 'json',
        success: function (data) {
            if (data.ack == 'OK') {
                $('.dismissalertholder').text('').removeClass('alert').removeClass('alert-danger');
                Photobum.closeAllModals(true);
            } else {
                $('.dismissalertholder').text(data.msg).addClass('alert').addClass('alert-danger');
            }
        }
    });

};

