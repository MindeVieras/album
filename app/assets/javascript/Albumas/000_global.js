var ModalData;
var ModalList = [];
var Music = (function() {
    "use strict";
    return {
        makeModalTarget: function(id) {
            if (!id) {
                id = UUID.generate();
            }
            if (id[0] == '#') {
                id = id.substring(1);
            }
            $("#modaltemplate").clone().attr('id', id).insertAfter("#modaltemplate");
            return '#' + id;
        },
        dummyLadda: new function () {
            this.create = function() {return this;};
            this.start = function() {};
            this.stop = function() {};
        },
        populateModalDataFields: function(btn) {
            ModalData = JSON.parse(JSON.stringify($(btn).data()));
            delete ModalData.loading;
            delete ModalData.remote;
            delete ModalData.style;
            delete ModalData.target;
            delete ModalData.toggle;
            delete ModalData.size;
            delete ModalData.function;
            delete ModalData.functiondelay;
            delete ModalData.ladda;
        }
    }
})();






$(document).ready(function() {
    Dropzone.autoDiscover = false;

    $(document.body).on('click', "[data-remote!=''][data-remote]", function(event) {
        event.preventDefault();
        var options = JSON.parse(JSON.stringify($(this).data()));
        Music.openModal(options, this);
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
            var l = $(this).data('ladda') ? Ladda.create(this) : Music.dummyLadda.create();
            l.start();
            $.when(
                setTimeout(function () {
                    console.log('Executing function: ' + functionName + ' after delay of ' + sleep + ' milliseconds');
                    fn(info, btn);
                }, sleep)
            ).then(l.stop());
        } else {
            console.log('No such function: ' + functionName);
        }
    });

    $(document).on('hidden.bs.modal', function() {
        $(ModalList.pop()).remove();
    });

    $(document).on('show.bs.modal', '.modal', function() {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    Music.initView();


    window.getFunctionFromString = function (string) {
        var scope = window;
        var scopeSplit = string.split('.');
        for (i = 0; i < scopeSplit.length - 1; i++) {
            scope = scope[scopeSplit[i]];
            if (scope == undefined) return;
        }
        return scope[scopeSplit[scopeSplit.length - 1]];
    };


});
