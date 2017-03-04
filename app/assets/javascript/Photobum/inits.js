
Photobum.initFrontend = function() {

};

Photobum.initView = function() {
    Photobum.localize();
    Photobum.initSwitches();
    Photobum.initEditors();
    Photobum.initDropzone();
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

Photobum.initEditors = function() {
    tinymce.remove();
    $("[data-tinymce]").each(function(){
        tinymce.init({
            skin_url: '/assets/deps/tinymce/skins/lightgray',
            theme: 'inlite',

            plugins: 'image link paste contextmenu textpattern autolink placeholder',
            insert_toolbar: 'quickimage',
            selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
            inline: true,
            paste_data_images: true,
            selector: '#'+$(this).attr('id'),
            height: '400',
            images_upload_url: '/api/image'
        });
    });
    $("[data-tinymce-lite]").each(function(){
        tinymce.init({
            skin_url: '/assets/deps/tinymce/skins/lightgray',
            theme: 'inlite',
            plugins: 'link paste textpattern autolink placeholder',
            selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
            insert_toolbar: '',
            inline: true,
            selector: '#'+$(this).attr('id'),
            height: '400'
        });
    });
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
                field.append('<input name="img_url[]" data-index="'+indx+'" class="hidden img_url" value="'+response.location+'">');
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
        $('.img_url[data-index="'+index+'"]').remove();
        $(this).closest('.list-group-item').remove();
    });
};