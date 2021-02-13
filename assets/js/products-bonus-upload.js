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
            acceptedFiles: ".xlsx",
            createImageThumbnails: false,
            accept: function (file, done) {
                done();
            },
            addedfile: function (file) {
                $("#dropZoneProductsBonusUpload").fadeOut();
                $("#dropZoneProductsBonusUploadProgress").fadeIn();
            },
            totaluploadprogress: function (progress) {
                $("#dropZoneProductsBonusUploadProgress").html('Uploading: '+ progress + " %");
                $("#dropZoneProductsBonusUploadProgress").css('width', progress + "%");
            },
            success: function (file, response) {
                $("#goBackContainer").show();
                $("#dropZoneProductsBonusUploadProgress").html(WebAppLocals.getMessage("uploadSuccess"));
                WebApp.post('/web/distributor/product/bonus/upload/process', null, _uploadProcessSuccessCallback)
            },
            error: function (file, error) {
                // Show error message
                WebApp.alertError(WebAppLocals.getMessage("bonusUploadError"));
                
                // Change upload bar
                $("#dropZoneProductsBonusUploadProgress").removeClass('bg-primary');
                $("#dropZoneProductsBonusUploadProgress").addClass('bg-danger');
                $("#dropZoneProductsBonusUploadProgress").css('width', "100%");
                $("#dropZoneProductsBonusUploadProgress").html(WebAppLocals.getMessage("uploadError"));
            
                // Show go back button
                $("#goBackContainer").show();
            }
        });
    }

    var _uploadProcessSuccessCallback = function(webReponse){
        $('#productsBonusUploadProcessResultContainer').addClass('mt-20')
        $('#productsBonusUploadProcessResultContainer').html(webReponse.data);
    };

    return {
        init: function () {
            _init();
        }
    };
}();
KTUtil.ready(function () {
    ProductsBonusUpload.init();
});