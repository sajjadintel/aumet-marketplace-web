"use strict";
var ProductsBonusUpload = function () {
    var _init = function () {
        var _id = '#dropZoneProductsBonusUpload';
        var previewNode = $(_id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        $(_id).dropzone({
            url: "/web/distributor/product/bonus/upload",
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 100,
            addRemoveLinks: false,
            acceptedFiles: ".xlsx,.xls,.csv",
            createImageThumbnails: false,
            //previewTemplate: previewTemplate,
            //previewsContainer: _id + " .dropzone-items",
            //clickable: "#btnProductsBonusUpload",
            accept: function (file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            addedfile: function (file) {
                $("#dropZoneProductsBonusUpload").fadeOut();
                $("#dropZoneProductsBonusUploadProgressContainer").fadeIn();
            },

            sending: function (file) {

            },
            totaluploadprogress: function (progress) {
                $("#dropZoneProductsBonusUploadProgress").html('Uploading: '+ progress + " %");
                $("#dropZoneProductsBonusUploadProgress").css('width', progress + "%");
                if(progress == 100){
                }

            },
            complete: function (progress) {
                $("#dropZoneProductsBonusUploadProgress").html('Upload Completed Successfully');
                WebApp.post('/web/distributor/product/bonus/upload/process', null, ProductsBonusUpload.processUpload)
            }
        });
    }

    var _processUpload = function(webReponse){
        $('#productsBonusUploadProcessResultContainer').html(webReponse.data);
    };

    return {
        init: function () {
            _init();
        },
        processUpload: function (webReponse) {
            _processUpload(webReponse);
        }
    };
}();
KTUtil.ready(function () {
    ProductsBonusUpload.init();
});