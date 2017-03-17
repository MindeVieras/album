PhotobumAdmin.albumsReady = function() {
    //console.log('albums ready');
};

PhotobumAdmin.viewAlbums = function() {
    //console.log('Viewing albums');
};

PhotobumAdmin.addAlbum = function (info, btn) {

    $('.alertholder').text('').removeClass('alert').removeClass('alert-danger');

    var form_data = {
        id: $('#id').val(),
        name: $('#name').val(),
        start_date: $('#start_date').data("DateTimePicker").date().utc().format("YYYY-MM-DD HH:mm:ss"),
        end_date: $('#end_date').data("DateTimePicker").date().utc().format("YYYY-MM-DD HH:mm:ss"),
        location_name: $('#add_album #location_name').val(),
        locations: $('#add_album .album_loc').serializeArray(),
        album_images: $('#add_album .img_url').serializeArray(),
        album_images_db: $('#add_album .img_url_db').serializeArray(),
        album_persons: $('#add_album .album_persons').serializeArray(),
        body: tinyMCE.get('album_body').getContent(),
        private: $('#add_album #private').bootstrapSwitch('state')
    };
    //console.log(form_data);

    // set ladda for save button
    countImg = form_data.album_images.length;
    if(countImg > 0){
        //console.log(countImg);
        $('.delete-button').remove();
        $('.cancel-button').remove();

        // Create a new instance of ladda for the specified button
        var l = Ladda.create(document.querySelector('.save-button'));
        // Start loading
        l.start();

        if(countImg == 1){
            time = 5000;
        } else {
            time = 10000;
        }    
        setTimeout(function(){
            Photobum.closeModal(true);
        },time);
    }

    // make ajax post
    $.ajax({
        type: "POST",
        data: form_data,
        url: '/admin/albums/add',
        dataType: "json",
        success: function (data) {
            //console.log(data);
            if (data.ack == 'ok') {
                $('.alertholder').text('').removeClass('alert').removeClass('alert-danger');
                Photobum.closeModal(true);
            }
            else {
                $('.alertholder').text(data.msg).addClass('alert').addClass('alert-danger');
                Photobum.initView();
                Photobum.scrollToTopOfModal();
            }
        },
        error: function(xhr){
            console.log(xhr);
        }
    });
};

PhotobumAdmin.deleteAlbum = function (info, btn) {
    //console.log(info);
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
                dataFunction: "PhotobumAdmin.doDeleteAlbum",
                additionalData: {
                    item: info.id,
                    dir: info.dir,
                    ladda: true
                },
                dismiss: false
            }
        }
    });
};

PhotobumAdmin.doDeleteAlbum = function (info, btn) {
    $.ajax({
        type: "DELETE",
        url: '/admin/albums/delete/id/'+info.item,
        data: JSON.stringify({id: info.item}),
        dataType: 'json',
        success: function (data) {
            dir_data = {
                dir: info.dir,
            };
            $.ajax({
                type: "POST",
                data: dir_data,
                url: '/api/utilities/delete-files',
                dataType: "json",
                success: function (data) {
                    console.log(data);
                },
                error: function(xhr){
                    console.log(xhr);
                }
            });
            if (data.ack == 'ok') {
                $('.dismissalertholder').text('').removeClass('alert').removeClass('alert-danger');
                Photobum.closeAllModals(true);
            } else {
                $('.dismissalertholder').text(data.msg).addClass('alert').addClass('alert-danger');
            }
        }
    });
};