PhotobumAdmin.personsReady = function() {
    var options = {
        callback: function (value) {
            data = {
                section: 'persons',
                value: value
            };
            $.ajax({
                type: 'GET',
                url: '/api/utilities/generateslug',
                data: data,
                dataType: 'json',
                success: function (response) {
                    $('.slugholder').text(response.url);
                }
            });
        },
        wait: 250,
        highlight: true,
        allowSubmit: false,
        captureLength: 3
    };

    $("#add_person #person_name").typeWatch(options);



};

PhotobumAdmin.viewPersons = function() {
    console.log('Viewing artists');
};

PhotobumAdmin.addPerson = function (info, btn) {
    $('.alertholder').text('').removeClass('alert').removeClass('alert-danger');
    var data = {
        id: $('#add_person #person_id').val(),
        name: $('#add_person #person_name').val(),
        // headline_image: $('#add_news .single-dropzone').attr('data-image'),
        // hilite_para: tinyMCE.get('hilite_para').getContent(),
        // body: tinyMCE.get('news_body').getContent(),
        // attribution_id: $('#add_news #attrib_id').val(),
        // publish_date: $('#publish_date').data("DateTimePicker").date().utc().format("YYYY-MM-DD HH:mm:ss"),
        // publish: $('#add_news #status').bootstrapSwitch('state')
    };
    console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: '/admin/persons/add',
        dataType: "json",
        success: function (data) {
            if (data.ack == 'OK') {
                $('.alertholder').text('').removeClass('alert').removeClass('alert-danger');
                Photobum.closeModal(true);
            }
            else {
                $('.alertholder').text(data.msg).addClass('alert').addClass('alert-danger');
                Photobum.initView();
                Photobum.scrollToTopOfModal();
            }
        }
    });

};

PhotobumAdmin.deletePerson = function (info, btn) {
    Photobum.dialog({
        message: 'Comfirm deletion of ' + info.name + '<br/>You know you can just unpublish from the edit screen right?',
        title: "<i class=\"foreground news fa fa-exclamation-circle pad-right\"></i>Warning",
        buttons: {
            main: {
                label: "No! I've changed my mind",
                className: "btn btn-success",
                dismiss: true
            },
            danger: {
                label: "Yes! Delete it.",
                className: "btn btn-danger",
                dataFunction: "PhotobumAdmin.doDeletePerson",
                additionalData: {
                    item: info.id,
                    ladda: true
                },
                dismiss: false
            }
        }
    });
};

PhotobumAdmin.doDeletePerson = function (info, btn) {
    $.ajax({
        type: "DELETE",
        url: '/admin/persons/delete/id/' + info.item,
        data: JSON.stringify({id: info.item}),
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

PhotobumAdmin.clearHeadlineImage = function(info, btn) {
    $('.dropzone-previews').html('');
    $('.single-dropzone').show();
    $('.single-dropzone').attr('data-image', '');
};