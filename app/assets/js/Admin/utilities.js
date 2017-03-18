

Photobum.localize = function () {
    $('.localdate').each(function () {
        var format = $(this).data('format');
        if (!format) {
            format = 'YYYY-MM-DD HH:mm:ss';
        }
        if ($(this).data('utc')) {
            var localTime = moment.utc($(this).data('utc')).toDate();
            localTime = moment(localTime).format(format);
            $(this).text(localTime);
            $(this).attr('datetime', localTime);
        } else if ($(this).data('no-date')) {
            $(this).text($(this).data('no-date'));
        }

    });
    $('time.timeago').timeago();
};


Photobum.openModal = function (opts, btn) {
    opts.ladda = typeof opts.ladda !== 'undefined' ? opts.ladda : 1;
    opts.size = typeof opts.size !== 'undefined' ? opts.size : 'normal';
    opts.ident = typeof opts.ident !== 'undefined' ? opts.ident : 'cwmodal';
    opts.backgroundClass = typeof opts.backgroundClass !== 'undefined' ? opts.backgroundClass : '';
    opts.loaderHTML = typeof opts.loaderHTML !== 'undefined' ? opts.loaderHTML : '<p class="text-center"><i class="modalspinner fa fa-refresh fa-spin fa-5x"></i></p>';
    var target = Photobum.makeModalTarget(opts.ident);

    ModalList.push(target);

    $(target).addClass('modal-' + opts.size);
    if (btn) {
        Photobum.populateModalDataFields($(btn));
    }

    $(target).find('.modal-content').empty().addClass(opts.backgroundClass).html(opts.loaderHTML);
    $(target).modal('show');

    var l = opts.ladda ? Ladda.create(btn) : Photobum.dummyLadda.create();


    l.start();
    $.ajax({
        type: "GET",
        url: opts.remote,
        data: ModalData,
        success: function (data) {
            $(target).find('.modal-content').html(data);
            Photobum.initView();
        },
        error: function () {
            $(target).modal('hide');
        }
    }).always(function () {
        l.stop();
    });
};

Photobum.closeModal = function(refresh){
    target = ModalList[ModalList.length - 1];
    $(target).modal('hide');
    if (refresh === true) {
        location.reload();
    }
};

Photobum.closeAllModals = function(refresh){
    for (var i in ModalList) {
        $(ModalList[i]).modal('hide');
    }
    if (refresh === true) {
        location.reload();
    }
};

Photobum.scrollToTopOfModal = function() {
    target = ModalList[ModalList.length - 1];

    $(window).animate({ scrollTop: 0 });
    $(target).animate({ scrollTop: 0 });
};
