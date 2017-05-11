Photobum.initFrontend = function() {

};

Photobum.initView = function() {
    Photobum.localize();
    Photobum.initDotDtoDot();
    Photobum.initSwitches();
    Photobum.initColorPickers();
    Photobum.initSelect();
    Photobum.initFreewall();
    Photobum.initDatepicker();
    Photobum.initEditors();
    Photobum.initDropzone();
    Photobum.initMap();

    $('*[data-thumb="make"]').each(function(i){
        image = $(this);
        src = image.attr('src');
        image.attr('data-thumb-index', i);
        if(src == '/default.png'){

            url = image.data('thumb-org');
            width = image.data('thumb-width');
            height = image.data('thumb-height');
            fit = image.data('thumb-fit');
            
            var img_data = {
                url: url,
                width: width,
                height: height,
                fit: fit
            };
            //console.log(img_data);
            //return false;
            $.ajax({
                type: "POST",
                data: img_data,
                url: '/api/utilities/generate-thumb',
                async: true,
                dataType: "json",
                success: function (data) {
                    //console.log(data);
                    //return false;
                    if (data.ack == 'ok') {
                        //console.log(data);
                        image.attr('src', data.url);
                    }
                },
                error: function(xhr){
                    console.log(xhr);
                }
            });
        }
    });
    


};

Photobum.initFilters = function() {
    var filterCont = $('#toolbar .filters');
    if(filterCont.length){
        filterCont.addClass('scd');

        var yearFilter = filterCont.find('.filter-year');

        // set initial album list filter
        $('.albums-container').empty().append('<div class="loader-spinner">loading...</div>');
        $.ajax({
            url: '/admin/albums/get/date/'+$(yearFilter).val(),
            success: function(html) {
                $('.albums-container').empty().append(html);
                Photobum.initFreewall();
            }
        });

        // Year filter event
        yearFilter.change(function() {

            console.log(this.value);

            //return false;

            //url = $(this).find('a').attr('href');

            $('.albums-container').empty().append('<div class="loader-spinner">loading...</div>');

            //$('ul.letters-tabs li').removeClass('active');
            //$(this).addClass('active');

            $.ajax({
                url: '/admin/albums/get/date/'+this.value,
                success: function(html){
                    $('.albums-container').empty().append(html);
                    Photobum.initFreewall();
                }
            });

            return false;
        });
    }
};

Photobum.initDotDtoDot = function() {

    $('.title-dot').dotdotdot({
        //  configuration goes here
    });
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

    $('[data-checkbox="privateToggle"]').bootstrapSwitch(
        {
            onText: '<i class="fa fa-unlock"></i>',
            offText: '<i class="fa fa-lock"></i>',
            onColor: 'success',
            offColor: 'danger'
        }
    );
};

Photobum.initColorPickers = function () {
    
    // Album color palettte picker
    if($('.color-album').length){    
        colors = $('.color-album').data('pal');
        palette = colors.split(',');

        $('.color-album').spectrum({
            preferredFormat: 'hex',
            showPaletteOnly: true,
            showPalette: true,
            palette: palette,
            showInput: false,
            change: function(color){
                $(this).attr('data-code', color.toHex()).addClass('color-changed');
                $(this).attr('value', '#'+color.toHex());
                $('.modal-header').css('background-color', '#'+color.toHex());
                //console.log(color.toHex());
            }
        });
    }

    // Colors page pickers
    $('.color-person').spectrum({
        preferredFormat: 'hex',

        change: function(color){
            $(this).attr('data-code', color.toHex()).addClass('color-changed');
            //console.log(color.toName());
        }
    });

    if($('#select-color').length){    
        colors = $('#select-color').data('pal');
        palette = colors.split(',');

        //console.log(palette);
        $('#select-color').spectrum({
            showPaletteOnly: true,
            showPalette: true,
            color: 'blanchedalmond',
            palette: palette
        });
    }
  
};

Photobum.initSelect = function () {
    
    $(".select2-simple").select2();
    $(".select2-multiple").select2();
  
};

Photobum.initFreewall = function () {    

    var usersWall = new Freewall('#album-users');
    usersWall.fitWidth();
    usersWall.reset({
        selector: '.user-block',
        animate: true,
        cellW: 200,
        cellH: 'auto',
        onResize: function() {
            usersWall.fitWidth();
        }
    });

    var albumsWall = new Freewall('.albums-container');
    albumsWall.reset({
        selector: '.item',
        animate: true,
        cellW: 220,
        cellH: 150,
        onResize: function() {
            console.log(this);
            albumsWall.refresh();
        }
    });
    albumsWall.fitWidth();

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
    tinymce.remove();

    $("[data-tinymce]").each(function(){
        tinymce.init({
            skin_url: '/assets/tinymce/skins/lightgray',
            insert_toolbar: false,
            menubar:false,
            selector: '#'+$(this).attr('id'),
            height: '150',
        });
    });
};

Photobum.initDropzone = function() {

    $('#add_album').dropzone({
        init: function() {

            var dropzone = this;
            var field = $('#img_urls');

            i = 1;
            this.on("addedfile", function(file) {
                console.log(file);

                if(file.type.includes('image')){                
                    EXIF.getData(file, function() {
                        var date = EXIF.getTag(this, 'DateTimeOriginal');
                        var make = EXIF.getTag(this, 'Make');
                        var model = EXIF.getTag(this, 'Model');
                        var allMetaData = EXIF.getAllTags(this);
                        var allMetaDataSpan = document.getElementById("allMetaDataSpan");
                        //console.log(allMetaData);

                        $(file.previewElement).find('.file-date-taken').text(Photobum.convertExifDate(date));
                        $(file.previewElement).find('.make-model').text(make+' ('+model+')');
                    });
                }

                var preview = $(file.previewElement);
                preview.attr('data-index', i++);

            });
            this.on("success", function(file, response) {
                indx = $(file.previewElement).attr('data-index');
                w  = indx - 1;
                type = file.type.includes('image') ? 'image' : 'video';
                field.append('<input name="img_url[]" data-type="'+type+'" data-index="'+indx+'" data-weight="'+w+'" class="img_url img_weight" value="'+response.location+'">');
                if(type == 'video'){
                    var basePath = $('#base_path').val();
                    var videoPath = response.location.substring(basePath.length);
                    var video = '<video class="saved-file" width="320" height="210" controls data-thumb-org="'+videoPath+'"><source src="'+videoPath+'" type="video/mp4">Your browser does not support HTML5 video.</video>';
                    $(file.previewElement).find('.preview').append(video);
                    $(file.previewElement).addClass('video-preview-item');
                }
                $(file.previewElement).find('.progress-wrapper').hide();
                $(file.previewElement).find('.file-status').show();
            });
            this.on("removedfile", function(file) {
                indx = $(file.previewElement).attr('data-index');
                $('.img_url[data-index="'+indx+'"]').remove();
            });
            this.on("error", function(file, message) { 
              console.log(message);
            });

        },
        url: "/api/upload",
        thumbnailWidth: 320,
        thumbnailHeight: 210,
        parallelUploads: 3,
        acceptedFiles: ".jpg,.jpeg,.png,.gif,.mp4,.mpg,.mkv,.avi",
        previewTemplate: $('#template').html(),
        headers: { 'Accept': "*/*" },
        previewsContainer: "#previews",
        clickable: ".fileinput-button"
    });

    $('.remove-media-file').click(function(){
        index = $(this).attr('data-index');
        $('.img_url_db[data-index="'+index+'"]').addClass('file_remove');
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


// Photobum.initDropzone_____old = function() {

//     $('#add_album').dropzone({
//         init: function() {

//             var dropzone = this;
//             var field = $('#img_urls');

//             $(".start-upload").hide();
//             $(".cancel-all").hide();

//             $(".cancel-all").click(function() {
//                 dropzone.removeAllFiles(true);
//                 $(".start-upload").hide();
//                 $(this).hide();
//             });

//             $(".start-upload").click(function() {
//                 dropzone.enqueueFiles(dropzone.getFilesWithStatus(Dropzone.ADDED));
//             });

//             i = 1;
//             this.on("addedfile", function(file) {
//                 EXIF.getData(file, function() {
//                     var make = EXIF.getTag(this, 'Make');
//                     var model = EXIF.getTag(this, 'Model');

//                     $(file.previewElement).find('.make-model').text(make+' ('+model+')');
//                 });
//                 //console.log(file);
//                 $(".start-upload").show();
//                 $(".cancel-all").show();
//                 var preview = $(file.previewElement);
//                 preview.attr('data-index', i++);
                
//                 var button = preview.find('.start');
//                 button.click(function() {
//                     dropzone.enqueueFile(file);
//                 });
//             });
//             this.on("success", function(file, response) {
//                 indx = $(file.previewElement).attr('data-index');
//                 w  = indx - 1;
//                 field.append('<input name="img_url[]" data-index="'+indx+'" data-weight="'+w+'" class="img_url img_weight" value="'+response.location+'">');
//             });
//             this.on("removedfile", function(file) {
//                 indx = $(file.previewElement).attr('data-index');
//                 $('.img_url[data-index="'+indx+'"]').remove();
//             });
//             this.on("error", function(file, message) { 
//               console.log(message);
//             });

//         },
//         url: "/api/upload",
//         thumbnailWidth: 80,
//         thumbnailHeight: 80,
//         parallelUploads: 20,
//         previewTemplate: $('#template').html(),
//         headers: { 'Accept': "*/*" },
//         autoQueue: false,
//         previewsContainer: "#previews",
//         clickable: ".fileinput-button"
//     });

//     $('.remove-media-file').click(function(){
//         index = $(this).attr('data-index');
//         $('.img_url_db[data-index="'+index+'"]').remove();
//         $(this).closest('.list-group-item').remove();
//     });
// };
