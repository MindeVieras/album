var ModalData;
var ModalList = [];
var PhotobumInstall = (function() {
    "use strict";
    return {};
})();

$(document).ready(function() {

    $(document.body).on('click', "[data-remote!=''][data-remote]", function(event) {
        event.preventDefault();
        var options = JSON.parse(JSON.stringify($(this).data()));
        //Photobum.openModal(options, this);
    });
    $(document.body).on('click', "[data-function!=''][data-function]", function (event) {
        event.preventDefault();
        var btn = this;
        var functionName = $(this).data('function');
        var fn = getFunctionFromString(functionName);
        var sleep = 0;
        var info = $(this).data();
        for (k in info) {
            info[k] = $(this).attr('data-' + k);
        }
        if (typeof fn === 'function') {

            if ($(this).data('functionDelay')) {
                var sleep = parseInt($(this).data('functionDelay'));
            }
            //var l = $(this).data('ladda') ? Ladda.create(this) : Photobum.dummyLadda.create();
            //l.start();
            $.when(
                setTimeout(function () {
                    console.log('Executing function: ' + functionName + ' after delay of ' + sleep + ' milliseconds');
                    fn(info, btn);
                }, sleep)
            ).then(
                //l.stop()
            );
        } else {
            console.log('No such function: ' + functionName);
        }
    });

    PhotobumInstall.initView();

    window.getFunctionFromString = function (string) {
        var scope = window;
        var scopeSplit = string.split('.');
        for (i = 0; i < scopeSplit.length - 1; i++) {
            scope = scope[scopeSplit[i]];
            if (scope === undefined) return;
        }
        return scope[scopeSplit[scopeSplit.length - 1]];
    };

});

PhotobumInstall.initView = function() {
    PhotobumInstall.initDotDtoDot();
    //PhotobumInstall.checkComposerStatus();
};

PhotobumInstall.initDotDtoDot = function() {

    //console.log('oba');
};

PhotobumInstall.checkComposerStatus = function() {
    
    $('#output').text('Loading...');
    var apiUrl = '/api/installer/composer-get-status';
    
    $.ajax({
        type: "POST",
        data: {function: 'getJson'},
        url: apiUrl,
        dataType: "json",
        success: function (res) {
            //console.log(data);
            if (res.ack == 'ok') {
                if (res.msg.composerJson){
                    $('#output').html('<p class="text-success">composer.json found!</p>');    
                    $.ajax({
                        type: "POST",
                        data: {function: 'getPackages'},
                        url: apiUrl,
                        dataType: "json",
                        success: function (res) {
                            if (res.ack == 'ok') {
                                if (res.msg.composerPackages){
                                    //console.log(res.msg);
                                    $('#output').append('<p class="">'+res.msg.composerPackages.length+' packages found:</p>');
                                    res.msg.composerPackages.map(function(row){
                                        $('#output').append('<p class="">'+row+'</p>');
                                    });
                                    
                                } else {
                                    $('#output').html('<p class="text-danger">Bad server response.</p>');
                                }
                            }
                            else {
                                $('#output').html('<p class="text-danger">Can\'t read composer.json</p>');
                            }
                        }
                    });
                } else {
                    $('#output').html('<p class="text-danger">Can\'t find composer.json</p>');
                }
            }
            else {
                $('#output').html('<p class="text-danger">Bad server response.</p>');
            }
        }
    });
};



//tinyMCE.baseURL = "/assets/deps/tinymce";
