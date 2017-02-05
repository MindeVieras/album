
Music.initFrontend = function() {

};

Music.initView = function() {
    Music.localize();
    Music.initSwitches();
    Music.initEditors();
    Music.initDropzone();
    Music.initMenu();
    Music.initSliders();
    Music.initAccordions();
    Music.initSpinners();
    Music.initDatepicker();
    Music.initBackToTop();
};

Music.initSwitches = function() {
    $('[data-checkbox="activeToggle"]').bootstrapSwitch(
        {
            onText: 'Active',
            offText: 'Disabled',
            onColor: 'success',
            offColor: 'danger'
        }
    )

    $('[data-checkbox="yesToggle"]').bootstrapSwitch(
        {
            onText: 'Yes',
            offText: 'No',
            onColor: 'success',
            offColor: 'danger'
        }
    )
}

Music.initEditors = function() {
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
}

Music.initDropzone = function() {

    $('.avatar-dropzone').dropzone(
        {
            init: function() {
                this.on("addedfile", function(file) {
                  console.log('added');
                  //$('.single-dropzone').hide();
                });
                this.on("success", function(file, response) {

                  console.log('success');
                  console.log(response);
                    $('.single-dropzone').attr('data-image', response.location);
                });
                this.on("removedfile", function(file) {
                    //$('.single-dropzone').show();
                    console.log(file);
                    $('.single-dropzone').attr('data-image', '');
                });
                this.on("maxfilesreached", function(file) {
                  //this.removeFile(file);
                });
  
                // File upload Progress
                this.on("totaluploadprogress", function (progress) {
                  console.log("progress ", progress);
                  //$('.roller').width(progress + '%');
                });

                this.on("error", function(file, message) { 
                  console.log(message);
                  this.removeFile(file); 
                });

            },
            url:'/api/image',
            uploadMoultiple: true,
            maxFiles: 1000,
            headers: { 'Accept': "*/*" },
            previewsContainer: '.dropzone-previews',
            previewTemplate: $('.dz-preview').html(),
            addRemoveLinks: true
        }
    );

    // $('.album-dropzone').dropzone(
    //     {
    //         init: function() {
    //             this.on("addedfile", function(file) {
    //               console.log('added');
    //               //$('.single-dropzone').hide();
    //             });
    //             this.on("success", function(file, response) {

    //               console.log('success');
    //               console.log(response);
    //                 $('.single-dropzone').attr('data-image', response.location);
    //             });
    //             this.on("removedfile", function(file) {
    //                 //$('.single-dropzone').show();
    //                 console.log(file);
    //                 $('.single-dropzone').attr('data-image', '');
    //             });
    //             this.on("maxfilesreached", function(file) {
    //               //this.removeFile(file);
    //             });
  
    //             // File upload Progress
    //             this.on("totaluploadprogress", function (progress) {
    //               console.log("progress ", progress);
    //               //$('.roller').width(progress + '%');
    //             });

    //             this.on("error", function(file, message) { 
    //               console.log(message);
    //               this.removeFile(file); 
    //             });

    //         },
    //         url:'/api/image',
    //         uploadMoultiple: true,
    //         maxFiles: 1000,
    //         headers: { 'Accept': "*/*" },
    //         previewsContainer: '.dropzone-previews',
    //         previewTemplate: $('.dz-preview').html(),
    //         addRemoveLinks: true
    //     }
    // );
      //console.log('dropppppdbxp');
}

Music.initSliders = function () {
  // HOME PAGE slider
  $('#home-slider').owlCarousel({
    nav: true,
    navText: ['<','>'],
    dots: false,
    loop: true,
    touchDrag: true,
    mouseDrag: true,
    items: 1
  });
  // EVENT HOME PAGE slider
  $('#event-slider').owlCarousel({
    nav: true,
    navText: ['<','>'],
    dots: false,
    loop: true,
    touchDrag: true,
    mouseDrag: true,
    items: 1
  });
  // Adopt An Animal slider
  $('#adopt-animal-slider').owlCarousel({
    nav: false,
    navText: ['',''],
    dots: true,
    loop: true,
    touchDrag: true,
    mouseDrag: true,
    items: 1
  });
}

Music.initSpinners = function () {
  // INPUT number Jquery UI spinner
  $('.spinner').spinner({
    min: 0
  });
  
  $(".spinner").on( "spin", function( event, ui ) {

    if (ui.value > 0){
      $(this).closest('.animal').find('.silhouette').css('color', '#9bc31c');
      $(this).parent().find('.ui-spinner-button').css('background-color', '#9bc31c');
    } else if (ui.value == 0){
      $(this).closest('.animal').find('.silhouette').css('color', '#1c8370');
      $(this).parent().find('.ui-spinner-button').css('background-color', '#1c8370');
    }
  });
  
}

Music.initDatepicker = function () {
  // Datepicker
  $('.datepicker').datepicker();
  
}

Music.initAccordions = function () {
  // FAQS Accordion
  $('#faqs-accordion').accordion({
    collapsible: true
  });
  // Get In Touch Accordion
  $('#get-in-touch-accordion').accordion({
    collapsible: true
  });
}

Music.initBackToTop = function () {
  // Back to top Button
  if($('#back-to-top').length) {
    $('#back-to-top').on('click', function (e) {
      e.preventDefault();
      $('html,body').animate({
        scrollTop: 0
      }, 700);
    });
  }
}

Music.initMenu = function () {
  // Hamburger Button
  var mainNav = $('.main-nav-wrapper');
  $('.hamburger').on('click', function () {
    $(this).toggleClass('is-active');
    mainNav.toggleClass('is-open');

    $('html, body').toggleClass('menu-is-open');
  
  });

  $('.expandable a').on('click', function(e){
    e.preventDefault;
    $(this).toggleClass('expanded');
    $(this).parent().find('.submenu').toggleClass('submenu-is-open');
  });



  $(window).scroll( function() {
    var value = $(this).scrollTop();
    if ( value > 60 )
      $('header').addClass('small');
    else
      $('header').removeClass('small');
  });


}
