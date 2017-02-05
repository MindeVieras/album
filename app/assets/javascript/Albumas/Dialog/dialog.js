Music.dialog = function(init) {
    var target = Music.makeModalTarget();
    ModalList.push(target);
    $(target).attr('data-backdrop', 'static');
    $(target).modal('show');
    content = $(target).find('.modal-content');
    content.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title">'+init.title+'</h4></div>');
    content.append('<div class="modal-body"><div class="dismissalertholder"></div><p>'+init.message+'</p></div><div class="modal-footer"></div>');
    footer = $(target).find('.modal-footer');
    $.each(init.buttons, function(k, v){
        b = $('<button type="button" class="btn"/>');
        b.text(v.label);
        b.addClass('dialog-btn-'+k).addClass(v.className);
        if (v.dismiss) {
            b.attr('data-dismiss', 'modal')
        }
        if (v.dataFunction) {
            b.attr('data-function', v.dataFunction)
        }
        if(v.additionalData) {
            $.each(v.additionalData, function(dk, dv){
                b.attr('data-'+dk, dv);
            })
        }
        footer.append(b);
    });
}