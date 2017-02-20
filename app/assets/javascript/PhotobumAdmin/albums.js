PhotobumAdmin.albumsReady = function() {
    var options = {
        callback: function (value) {
            data = {
                section: 'albums',
                value: value
            };
            $.ajax({
                type: 'GET',
                url: '/api/utilities/generateslug',
                data: data,
                dataType: 'json',
                success: function (response) {
                    $('.slugholder').text(response.url);
                },
                error: function (xhr){
                    console.log(xhr);
                }
            });
        },
        wait: 250,
        highlight: true,
        allowSubmit: false,
        captureLength: 3
    };

    $("#add_album #album_name").typeWatch(options);
};

PhotobumAdmin.viewAlbums = function() {
    //console.log('Viewing albums');
};

PhotobumAdmin.addAlbum = function (info, btn) {
    $('.alertholder').text('').removeClass('alert').removeClass('alert-danger');

    var form_data = {
        id: $('#add_album #album_id').val(),
        name: $('#add_album #album_name').val(),
        start_date: $('#start_date').data("DateTimePicker").date().utc().format("YYYY-MM-DD HH:mm:ss"),
        end_date: $('#end_date').data("DateTimePicker").date().utc().format("YYYY-MM-DD HH:mm:ss"),
        location_name: $('#add_album #location_name').val(),
        locations: $('#add_album .album_loc').serializeArray(),
        album_images: $('#add_album .img_url').serializeArray(),
        album_persons: $('#add_album .album_persons').serializeArray(),
        body: tinyMCE.get('album_body').getContent(),
        private: $('#add_album #private').bootstrapSwitch('state')
    };
    //console.log(form_data);
    $.ajax({
        type: "POST",
        data: form_data,
        url: '/admin/albums/add',
        dataType: "json",
        success: function (data) {
            //console.log(data);
            if (data.ack == 'ok') {
                $.ajax({
                    type: "POST",
                    data: form_data,
                    url: '/api/utilities/rename-files',
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                    },
                    error: function(xhr){
                        console.log(xhr);
                    }
                });
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
    console.log(info);
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
            if (data.ack == 'OK') {
                $('.dismissalertholder').text('').removeClass('alert').removeClass('alert-danger');
                Photobum.closeAllModals(true);
            } else {
                $('.dismissalertholder').text(data.msg).addClass('alert').addClass('alert-danger');
            }
        }
    });
};

PhotobumAdmin.createAlbumMap = function(){

    var field = $('#album_markers');
    var map;
    map = new google.maps.Map(document.getElementById('album_map'), {
        center: {lat: -34.397, lng: 150.644},
        zoom: 15,
        scrollwheel: false,
        clickableIcons: false,
        mapTypeId: 'terrain'
    });

    // This event listener calls addMarker() when the map is clicked.
    clickIndex = 0;
    google.maps.event.addListener(map, 'click', function(event) {

        clickIndex++;
        addMarker(event.latLng, map, clickIndex);

        loc = event.latLng.lat()+','+event.latLng.lng();
        field.append('<input name="album_loc[]" data-index="'+clickIndex+'" class="hidden album_loc" value="'+loc+'">');
    });
    
    // Address autocomplete
    var input = document.getElementById('location_name');
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
        }

        map.setCenter(place.geometry.location);
        // map.setZoom(15);

        // var ac_marker = new google.maps.Marker({
        //     position: place.geometry.location,
        //     map: map,
        //     draggable: true,
        // });
        // loc = place.geometry.location.lat()+','+place.geometry.location.lng();
        // field.append('<input name="album_loc[]" data-index="2000000" class="album_loc" value="'+loc+'">');

        // google.maps.event.addListener(ac_marker, 'dblclick', function(event) {
        //     ac_marker.setMap(null);
        //     $('.album_loc[data-index="2000000"]').remove();

        // });

        // google.maps.event.addListener(ac_marker, 'dragend', function(event) {
        //     $('.album_loc[data-index="2000000"]').val(event.latLng.lat()+','+event.latLng.lng());

        // });

    });

    // add static markers in edit mode
    if($('.album_loc').length){    
        $('.album_loc').each(function(i){

            latLng = $(this).val().split(',');
            var st_marker = new google.maps.Marker({
                position: {lat: parseFloat(latLng[0]), lng: parseFloat(latLng[1])},
                map: map,
                draggable: true,
            });

            i++;
            google.maps.event.addListener(st_marker, 'dblclick', function(event) {
                st_marker.setMap(null);
                index = i+1000000;
                $('.album_loc[data-index="'+index+'"]').remove();

            });

            google.maps.event.addListener(st_marker, 'dragend', function(event) {
                index = i+1000000;
                $('.album_loc[data-index="'+index+'"]').val(event.latLng.lat()+','+event.latLng.lng());

            });

        });
        map.setCenter({lat: parseFloat(latLng[0]), lng: parseFloat(latLng[1])});
    }



    // Adds a marker to the map.
    addMarker = function(location, map, index) {
        
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
        });

        google.maps.event.addListener(marker, 'dblclick', function(event) {
            marker.setMap(null);
            $('.album_loc[data-index="'+index+'"]').remove();

        });

        google.maps.event.addListener(marker, 'dragend', function(event) {

            $('.album_loc[data-index="'+index+'"]').val(event.latLng.lat()+','+event.latLng.lng());

        });
    };      
};

PhotobumAdmin.albumDropzone = function(){

    var field = $('#img_urls');

    $(".start-upload").hide();
    $(".cancel-all").hide();

    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    
    var myDropzone = new Dropzone(document.body, {
        url: "/api/image",
        thumbnailWidth: 80,
        thumbnailHeight: 60,
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        headers: { 'Accept': "*/*" },
        autoQueue: false,
        previewsContainer: "#previews",
        clickable: ".fileinput-button"
    });
    
    i = 1;
    myDropzone.on("addedfile", function(file) {
        $(".start-upload").show();
        $(".cancel-all").show();
        var preview = $(file.previewElement);
        preview.attr('data-index', i++);
        
        var button = preview.find('.start');
        button.click(function() {
            myDropzone.enqueueFile(file);
        });
    });

    myDropzone.on("success", function(file, response) {
        console.log(file.previewElement);
        indx = $(file.previewElement).attr('data-index');
        field.append('<input name="img_url[]" data-index="'+indx+'" class="hidden img_url" value="'+response.location+'">');
    });

    myDropzone.on("removedfile", function(file) {
        indx = $(file.previewElement).attr('data-index');
        $('.img_url[data-index="'+indx+'"]').remove();
    });

    myDropzone.on("totaluploadprogress", function(progress) {
      //document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
    });

    myDropzone.on("sending", function(file) {
      // Show the total progress bar when upload starts
      //document.querySelector("#total-progress").style.opacity = "1";
      // And disable the start button
      //file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
    });

    myDropzone.on("queuecomplete", function(progress) {
      //document.querySelector("#total-progress").style.opacity = "0";
    });

    $(".start-upload").click(function() {
      myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
    });
    $(".cancel-all").click(function() {
      myDropzone.removeAllFiles(true);
      $(".start-upload").hide();
      $(this).hide();
    });

    $('.remove-media-file').click(function(){
        index = $(this).attr('data-index');
        $('.img_url[data-index="'+index+'"]').remove();
        $(this).closest('.list-group-item').remove();
    });
}

PhotobumAdmin.clearAlbumImage = function(info, btn) {
    console.log(info);
    // $('.dropzone-previews').html('');
    // $('.album-dropzone').show();
    // $('.album-dropzone').attr('data-image', '');
};