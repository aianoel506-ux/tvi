var ASSET = function() {
    return {
        initMtrPicUploadRow: function(this_row) {
            $('#frm_read_pic', this_row).submit(function(e) {
                var form = $(this);
                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: 'Adding new pictures',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: form.attr('action'),
                            method: form.attr('method'),
                            dataType: "json",
                            data: new FormData(form[0]),
                            processData: false,
                            contentType: false,
                        }).done(function (d) {
                            swal('Upload Picture', d.msg, d.func);
                            ASSET.initMtrPics(this_row, d.mtrno, d.acctid, d.year, d.month);
                        }).fail(function(){
                            swal("Error404: PHP", "Server side error!", "error");
                        });
                    }else{
                        swal.close();
                    }
                });

            });
        },

        initMtrPics: function(this_row, mtrno, acctno, year, month, type) {
            $.ajax({
                url: PECO.base_url() + 'assets/getassetpic',
                method: 'post',
                dataType: "json",
                data: {'mtrno': mtrno, 'acctno': acctno, 'year': year, 'month': month, 'type': type},
                beforeSend: function() {
                    $('#mtr_pics', this_row).html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Loading pictures..');
                }
            }).done(function (d) {
                $('#mtr_pics', this_row).html(d.html);

                /*

                $('#mtr_pics .fancybox-button', this_row).fancybox({
                    maxWidth: 900,
                    maxHeight: 700,
                    fitToView: true,
                    width: '80%',
                    height: '80%',
                    autoSize: true,
                    closeClick: false,
                    openEffect: 'stretch',
                    closeEffect: 'stretch'
                });

                */

            });
        },


        initDeletePicRow: function() {
            $(document).on('click', '#mtr_pics #btn_delete', function(e){
                e.preventDefault();
                var this_ = $(this);
                this_.closest('div').addClass('border-red-flamingo');
                swal({
                    title: "Are you sure?",
                    text: 'Adding new pictures',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: PECO.base_url() + 'mrd/deletemtrpic',
                            method: 'post',
                            dataType: "json",
                            data: {
                                'homedir': this_.attr('data-dir'),
                                'file': this_.attr('data-file'),
                                'year': this_.attr('data-year'),
                                'month': this_.attr('data-month'),
                            },
                        }).done(function (d) {

                            this_.closest('div').addClass('border-red-flamingo').fadeOut();
                            swal.close();

                            var this_tr = this_.closest('tr');

                            setTimeout(function(){
                                JO.initMtrPics(this_tr, $('#frm_read_pic input[name=mtrno]', this_tr).val(), $('#frm_read_pic input[name=acctid]', this_tr).val(), this_.attr('data-year'), this_.attr('data-month'));
                            },500);

                        }).fail(function(){
                            swal("Error404: PHP", "Server side error!", "error");
                        });
                    }else{
                        swal.close();
                    }
                });

            });
        },

    }
}();