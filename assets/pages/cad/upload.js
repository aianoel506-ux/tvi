var UPLOAD = function () {
    var upload_application = function () {
        var csv_contents = $('#csv_contents',document);
        var frm_data = $('#frm_data',document);
        var frm_save_online_application = $('#frm_save_online_application',document);

        $('#datafile').on('fileuploaded', function(event, data, previewId, index) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            console.log('File uploaded triggered',response.details);

            var content = response.details;
            var forms = response.form;
            csv_contents.html('');
            frm_data.html('');
            csv_contents.html(content);
            frm_data.html(forms);
        });

        frm_save_online_application.on('submit',function (e) {
            var this_ = $(this);
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Proceed in uploading Application.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                confirmButtonText: "Yes, Upload Application!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        data: this_.serialize(),
                        dataType: 'json'
                    }).done(function (data) {
                        swal(data.msg , '' , data.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal.close();
                }
            })
        })
    };

    return {
        app: function () {
            upload_application();
        }
    }
}();