"use strict";
var ProductsStockUpload = function () {
    var _init = function () {
        var _id = '#dropZoneProductsStockUpload';
        var previewNode = $(_id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        $(_id).dropzone({
            url: "/web/distributor/product/stock/upload",
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 100,
            addRemoveLinks: false,
            acceptedFiles: ".xlsx,.xls,.csv",
            createImageThumbnails: false,
            //previewTemplate: previewTemplate,
            //previewsContainer: _id + " .dropzone-items",
            //clickable: "#btnProductsStockUpload",
            accept: function (file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            addedfile: function (file) {
                $("#dropZoneProductsStockUpload").fadeOut();
                $("#dropZoneProductsStockUploadProgressContainer").fadeIn();
            },

            sending: function (file) {

            },
            totaluploadprogress: function (progress) {
                $("#dropZoneProductsStockUploadProgress").html('Uploading: '+ progress + " %");
                $("#dropZoneProductsStockUploadProgress").css('width', progress + "%");
                if(progress == 100){
                    $("#dropZoneProductsStockUploadProgress").html('Upload Completed Successfully');
                    WebApp.block('stockUpdateProcessing');
                }

            },
            complete: function (progress) {
                WebApp.unblock();
            }
        });
    }
    var demo2 = function () {
        var id = '#kt_dropzone_4';
        var previewNode = $(id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        var myDropzone4 = new Dropzone(id, {
            url: "https://keenthemes.com/scripts/void.php",
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            maxFilesize: 1,
            autoQueue: false,
            //previewsContainer: id + " .dropzone-items",
            //clickable: id + " .dropzone-select"
        });
        myDropzone4.on("addedfile", function (file) {
            file.previewElement.querySelector(id + " .dropzone-start").onclick = function () {
                myDropzone4.enqueueFile(file);
            };
            $(document).find(id + ' .dropzone-item').css('display', '');
            $(id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'inline-block');
        });
        myDropzone4.on("totaluploadprogress", function (progress) {
            $(this).find(id + " .progress-bar").css('width', progress + "%");
        });
        myDropzone4.on("sending", function (file) {
            $(id + " .progress-bar").css('opacity', '1');
            file.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
        });
        myDropzone4.on("complete", function (progress) {
            var thisProgressBar = id + " .dz-complete";
            setTimeout(function () {
                $(thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress, " + thisProgressBar + " .dropzone-start").css('opacity', '0');
            }, 300)
        });
        document.querySelector(id + " .dropzone-upload").onclick = function () {
            myDropzone4.enqueueFiles(myDropzone4.getFilesWithStatus(Dropzone.ADDED));
        };
        document.querySelector(id + " .dropzone-remove-all").onclick = function () {
            $(id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
            myDropzone4.removeAllFiles(true);
        };
        myDropzone4.on("queuecomplete", function (progress) {
            $(id + " .dropzone-upload").css('display', 'none');
        });
        myDropzone4.on("removedfile", function (file) {
            if (myDropzone4.files.length < 1) {
                $(id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
            }
        });
    }
    var demo3 = function () {
        var id = '#kt_dropzone_5';
        var previewNode = $(id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        var myDropzone5 = new Dropzone(id, {
            url: "https://keenthemes.com/scripts/void.php",
            parallelUploads: 20,
            maxFilesize: 1,
            previewTemplate: previewTemplate,
            previewsContainer: id + " .dropzone-items",
            clickable: id + " .dropzone-select"
        });
        myDropzone5.on("addedfile", function (file) {
            $(document).find(id + ' .dropzone-item').css('display', '');
        });
        myDropzone5.on("totaluploadprogress", function (progress) {
            $(id + " .progress-bar").css('width', progress + "%");
        });
        myDropzone5.on("sending", function (file) {
            $(id + " .progress-bar").css('opacity', "1");
        });
        myDropzone5.on("complete", function (progress) {
            var thisProgressBar = id + " .dz-complete";
            setTimeout(function () {
                $(thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress").css('opacity', '0');
            }, 300)
        });
    }
    return {
        init: function () {

            _init();
        }
    };
}();
KTUtil.ready(function () {
    ProductsStockUpload.init();
});