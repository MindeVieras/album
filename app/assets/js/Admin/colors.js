PhotobumAdmin.colorsReady = function() {
    console.log('colors ready');

};

PhotobumAdmin.viewColors = function() {
    console.log('Viewing artists');
};

PhotobumAdmin.addColor = function (info, btn) {
    //console.log(info);
    //return false;
    input = '<div class="color-item"><input type="text" class="color-person-new hidden"></div>';

    if(info.type == 'album'){
        $('#albums-colors').append(input);
    }
    if(info.type == 'person'){
        $('#persons-colors').append(input);
    }

    colorInput = $('.color-person-new');
    
    randomColor = function() {
        var letters = '0123456789ABCDEF';
        var color = '';
        for (var i = 0; i < 6; i++ ) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    var data = {
        color: randomColor(),
        type: info.type
    };
    //console.log(data);
    $.ajax({
        type: "POST",
        data: data,
        url: '/admin/colors/add',
        dataType: "json",
        success: function (data) {
            //console.log(data);
            if (data.ack == 'ok') {
                //$('.alertholder').text('').removeClass('alert').removeClass('alert-danger');
                //Photobum.closeModal(true);
                colorInput.attr('data-id', data.id);
                colorInput.attr('data-code', data.msg);

                colorInput.spectrum({
                    color: data.msg,
                    preferredFormat: 'hex',

                    change: function(color){
                        $(this).attr('data-code', color.toHex()).attr('data-id', data.id).addClass('color-changed');
                        //console.log(color.toHex());
                    }
                });

                colorInput.addClass('color-person').removeClass('color-person-new');
            }
            else {
                //console.log(data);
                //$('.alertholder').text(data.msg).addClass('alert').addClass('alert-danger');
                //Photobum.initView();
                //Photobum.scrollToTopOfModal();
            }
        }
    });   
    

};

PhotobumAdmin.saveColors = function (info, btn) {
    if($('.color-person').length){

        var data = {
            colors: $('.color-changed').map(function(){
                return {
                    id: $(this).attr('data-id'),
                    code: $(this).attr('data-code')
                }
            }).get(),
            type: 'person'
        };
        //console.log(data);
        $.ajax({
            type: "POST",
            data: data,
            url: '/admin/colors/save',
            dataType: "json",
            success: function (data) {
                //console.log(data);
                if (data.ack == 'ok') {
                    $('.color-changed').removeClass('color-changed');
                }
                else {
                    //$('.alertholder').text(data.msg).addClass('alert').addClass('alert-danger');
                    //Photobum.initView();
                    //Photobum.scrollToTopOfModal();
                }
            }
        });   
    }

};
