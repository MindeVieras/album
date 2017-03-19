
Photobum.initFrontend = function() {

};

Photobum.initView = function() {
    Photobum.localize();
    Photobum.initSwitches();
    Photobum.initDatepicker();
    Photobum.initEditors();
    Photobum.initDropzone();
    Photobum.initMap();
};

Photobum.initSwitches = function() {
    $('[data-checkbox="activeToggle"]').bootstrapSwitch(
        {
            onText: 'Active',
            offText: 'Disabled',
            onColor: 'success',
            offColor: 'danger'
        }
    );

    $('[data-checkbox="yesToggle"]').bootstrapSwitch(
        {
            onText: 'Yes',
            offText: 'No',
            onColor: 'success',
            offColor: 'danger'
        }
    );
};

Photobum.initDatepicker = function () {
    
    // // Start Datepicker
    // $('#start_date').datetimepicker({
    //         format: 'DD-MM-YYYY, HH:mm:ss',
    //         date: moment()
    //     }
    // );

    // $('#start_date').on('dp.change', function (e) {
    //     $('#end_date').data("DateTimePicker").minDate(e.date);
    // });

    // // End Datepicker
    // $('#end_date').datetimepicker({
    //         format: 'DD-MM-YYYY, HH:mm:ss',
    //         date: moment(),
    //         useCurrent: false
    //     }
    // );

    // $("#end_date").on("dp.change", function (e) {
    //     $('#start_date').data("DateTimePicker").maxDate(e.date);
    // });
  
};

Photobum.initEditors = function() {
    // tinymce.remove();
    // $("[data-tinymce]").each(function(){
    //     tinymce.init({
    //         skin_url: '/assets/deps/tinymce/skins/lightgray',
    //         theme: 'inlite',

    //         plugins: 'paste contextmenu textpattern autolink',
    //         insert_toolbar: false,
    //         selection_toolbar: 'bold italic | h2 h3',
    //         inline: true,
    //         selector: '#'+$(this).attr('id'),
    //         height: '400',
    //     });
    // });
};

Photobum.initDropzone = function() {

    $('#add_album').dropzone({
        init: function() {

            var dropzone = this;
            var field = $('#img_urls');

            $(".start-upload").hide();
            $(".cancel-all").hide();

            $(".cancel-all").click(function() {
                dropzone.removeAllFiles(true);
                $(".start-upload").hide();
                $(this).hide();
            });

            $(".start-upload").click(function() {
                dropzone.enqueueFiles(dropzone.getFilesWithStatus(Dropzone.ADDED));
            });

            i = 1;
            this.on("addedfile", function(file) {
                EXIF.getData(file, function() {
                    var make = EXIF.getTag(this, 'Make');
                    var model = EXIF.getTag(this, 'Model');

                    $(file.previewElement).find('.make-model').text(make+' ('+model+')');
                });
                //console.log(file);
                $(".start-upload").show();
                $(".cancel-all").show();
                var preview = $(file.previewElement);
                preview.attr('data-index', i++);
                
                var button = preview.find('.start');
                button.click(function() {
                    dropzone.enqueueFile(file);
                });
            });
            this.on("success", function(file, response) {
                indx = $(file.previewElement).attr('data-index');
                w  = indx - 1;
                field.append('<input name="img_url[]" data-index="'+indx+'" data-weight="'+w+'" class="img_url img_weight" value="'+response.location+'">');
            });
            this.on("removedfile", function(file) {
                indx = $(file.previewElement).attr('data-index');
                $('.img_url[data-index="'+indx+'"]').remove();
            });
            this.on("error", function(file, message) { 
              console.log(message);
            });

        },
        url: "/api/image",
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        previewTemplate: $('#template').html(),
        headers: { 'Accept': "*/*" },
        autoQueue: false,
        previewsContainer: "#previews",
        clickable: ".fileinput-button"
    });

    $('.remove-media-file').click(function(){
        index = $(this).attr('data-index');
        $('.img_url_db[data-index="'+index+'"]').remove();
        $(this).closest('.list-group-item').remove();
    });
};

Photobum.initMap = function() {
    if($('#album_map').length){

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
    }

};