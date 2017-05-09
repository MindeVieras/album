PhotobumAdmin.albumsReady = function() {
    //console.log('albums ready');

    // // make media sortable
    // if($('#previews').length){
    //     var el = document.getElementById('previews');
    //     var sortable = Sortable.create(el, {
            
    //         dataIdAttr: 'data-',
    //         // Element is chosen
    //         onChoose: function (evt) {
    //             evt.oldIndex;  // element index within parent
    //         },

    //         // Element dragging started
    //         onStart: function (evt) {
    //             evt.oldIndex;  // element index within parent
    //         },

    //         // Element dragging ended
    //         onEnd: function (evt) {
    //             console.log(evt.oldIndex);  // element's old index within parent
    //             console.log(evt.newIndex);  // element's new index within parent
    //             console.log(evt.item);
    //             index = $(evt.item).attr('data-index');
    //             $('.img_weight[data-weight="'+evt.newIndex+'"]').attr('data-weight', evt.oldIndex);
    //             $('.img_weight[data-index="'+index+'"]').attr('data-weight', evt.newIndex);
    //         },
    //     });
    // }

};

PhotobumAdmin.updateDates = function() {

    // make array of all dates
    var dates = $('.file-date-taken').map(function(){
        date = $(this).text();
        return date;
    }).get();
    
    dates = dates.filter(Boolean);
    
    dates.sort(function(a,b){
        return moment.utc(a).diff(moment.utc(b));
    });

    start_date = dates[0];
    end_date = dates.slice(-1).pop();

    $('#start_date').data("DateTimePicker").date(new Date(start_date));
    $('#end_date').data("DateTimePicker").date(new Date(end_date));


    console.log(dates);
    console.log(start_date);
    console.log(end_date);
};

PhotobumAdmin.viewAlbums = function() {
    //console.log('Viewing albums');
};
PhotobumAdmin.albumColorPicker = function() {
    $(".color-album").spectrum("toggle");
};
PhotobumAdmin.addAlbum = function (info, btn) {

    $('.alertholder').text('').removeClass('alert').removeClass('alert-danger');

    var form_data = {
        id: $('#id').val(),
        name: $('#name').val(),
        start_date: $('#start_date').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss"),
        end_date: $('#end_date').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss"),
        location_name: $('#location_name').val(),
        locations: $('#add_album .album_loc').serializeArray(),
        album_images: $('#add_album .img_url').map(function(){
            return {
                name: $(this).attr('name'),
                weight: $(this).data('weight'),
                file_type: $(this).data('type'),
                value: $(this).val()
            }
        }).get(),
        files_remove: $('#add_album .file_remove').serializeArray(),
        album_images_db: $('#add_album .img_url_db').map(function(){
            return {
                media_id: $(this).data('id'),
                name: $(this).attr('name'),
                weight: $(this).data('weight'),
                value: $(this).val()
            }
        }).get(),
        album_persons: $('#album_persons').select2('val'),
        body: tinyMCE.get('album_body').getContent(),
        color: $('.color-changed').attr('data-code'),
        private: $('#album_private').bootstrapSwitch('state')
    };
    console.log(form_data);
    //return false;

    // make ajax post
    $.ajax({
        type: "POST",
        data: form_data,
        url: '/admin/albums/add',
        dataType: "json",
        success: function (data) {
            console.log(data);
            //return false;
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