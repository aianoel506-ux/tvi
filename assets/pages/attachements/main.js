var ATTACHEMENTS = function() {
    var filedropzone = $(document).find('input[type=file]');

    var init_attachedments = function(dataid) {

        handler_file_explorer(dataid);
        $(document).on('click', '#btn_reload_attachments', function() {
            handler_file_explorer(dataid);
        });

        $(document).on('click','#btn_edit_reqlist',function () {
            var req_list = $('#tbl_requirements_list',document);
            req_list.find('#delete_requirement').each(function () {
                var this_ = $(this);
                this_.addClass('')
            });
        });

        $(document).on('click','#btn_reload_reqlist',function () {
            dt_requirement_upload_list(dataid);
        });

        PECO.select2Types($('#select2filetype', document), 'INSFILETYPES', '(optional)..');

        filedropzone.fileinput({
            uploadAsync: true,
            showBrowse: true,
            browseOnZoneClick: true,
            showPreview: false,
            uploadExtraData: function (d) {

                var stageid = $('#input_stageid',document).val();
                var filetype = $('#select2filetype',document).val();
                var measurements = $('#measurments',document).val();
                return {
                    dataid: dataid,
                    stageid: stageid,
                    filetype: filetype,
                    measurements: measurements
                };
            },
        }).on('fileuploaded' , function (event, data, previewId, index) {

            handler_file_explorer(dataid);
            filedropzone.fileinput('clear');

        }).on('fileerror' , function (event, data, previewId, index) {

            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            PECO.initAlerts(response.msg, 'Upload File', 'error', false, false);
            filedropzone.fileinput('clear');

        });

        filedropzone.on('filebatchuploadsuccess', function(event, data, previewId, index) {
            alert("test");
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            PECO.initAlerts(response.msg, 'Upload File', 'error', false, false);
            filedropzone.fileinput('clear');
        });
    };

    var handler_file_explorer = function(dataid) {
        var box_file_explorer = $(document).find('#box_file_explorer');
        var location = box_file_explorer.attr('data-folder');
        var explorer_default = box_file_explorer.html();
        $.ajax({
            url: PECO.base_url() + 'admin/fetchcadpictures',
            dataType: 'json',
            data: {dataid: dataid,location:location},
            type: 'post',
            beforeSend: function() {
                box_file_explorer.html('<h3><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Loading files... </h3>');
            }
        }).done(function(d) {
            var htmllayout = (d.htmllayout != '') ? d.htmllayout : explorer_default;
            box_file_explorer.html(htmllayout);
            //filedropzone.fileinput('clear');
        });

        box_file_explorer.on('click','.btn_delete',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var file = this_.attr('data-file');

            swal({
                title: "Are you sure?",
                text: 'This will delete the selected picture.',
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
                        url: PECO.base_url() + 'admin/deletefile',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            file: file
                        }
                    }).done(function (d) {
                        swal(d.title,d.msg,d.func);
                        if (d.qry) {
                            handler_file_explorer(dataid);
                        }
                    }).fail(function () {
                        swal('Fail!','Failed to delete picture.','error');
                    })
                }
            });
        })
    };

    var dt_requirement_upload_list = function (dataid) {
        var tbl_requirements_list = $('#tbl_requirements_list', document);
        $.ajax({
            url: PECO.base_url() + 'cad/getcustomerrequirements',
            type: 'post',
            data: {
                dataid: dataid,
                upload: true
            },
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_requirements_list, 'Loading requirements...');
            }
        }).done(function(d) {
            tbl_requirements_list.DataTable({
                bDestroy: true,
                bPaginate: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                bLengthChange: false,
                bFilter: true,
                aaData: d.list,
                aoColumns: [
                    {"data":"num"},
                    {"data":"text", sWidth:'350PX', sClass: 'text-primary'},
                    {"data":"status", sClass: 'text-align-center'},
                    {"data":"control", sClass: 'controls'},
                ],
                searchHighlight: false
            });
        }).fail(function() {
            PECO.DTphpError(tbl_requirements_list);
        });
    };

    var iframe_docs_preview = function(dataid) {
        var doc_preview_tabs = $('#doc_preview_tabs',document);
        var tab = $('a[data-toggle="tab"]',doc_preview_tabs);
        create_iframe_preview('tssr',dataid,3436);
        tab.on('shown.bs.tab',function (e) {
            var target = $(e.target).attr('href');
            var id = $(e.target).attr('data-id');
            var elem = target.split('_');
            if (elem[1] !== 'others') {
                create_iframe_preview(elem[1], dataid, id);
            }
        });

        var doc_preview_pane = $('#doc_preview_pane',document);
        var preview_btn = $('a#btn_open_preview',doc_preview_pane);
        var sign_btn = $('a#btn_sign_doc',doc_preview_pane);

        preview_btn.on('click',function (e) {
            var doctype = $(e.target).attr('data-type');
            $.ajax({
                url : PECO.base_url() + 'printer/docspreview',
                type : 'post',
                dataType : 'json',
                data : {
                    id : dataid,
                    doctype : doctype,
                    print : true
                }
            }).done(function (d) {
                var win = window.open('','');
                PECO.pdfPreview(win,d.title,d.html);
            }).fail(function () {

            });
        });

        sign_btn.on('click',function (e) {
            var doctype = $(e.target).attr('data-type');
            var name = $(e.target).attr('data-name');
            //Check if active OTP Exist
            //If None : SWAL Generate OTP
            //If Yes : SWAL Enter OTP

            /*$.ajax({
                url : PECO.base_url() + 'admin/lookupdocsotp',
                type : 'post',
                dataType : 'json',
                data : {
                    id : dataid,
                    doctype : doctype
                }
            }).done(function (d) {
                if (d.otpid) {
                    input_otp(d.otpid,d.name);
                } else {
                    send_otp(dataid,d.name,d.docid);
                }
            }).fail(function () {
                swal("Error!", "PHP Error Occurred!", "error");
            });*/

            swal({
                title: "Sign " + name,
                text: "Do you want to sign this document?",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes!",
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    //var msg = 'docid : ' + docid;

                    //Generate OTP and send. Return if sending is successful.
                    $.ajax({
                        url:PECO.base_url()+'admin/signdocument',
                        type:'post',
                        data:{
                            id : dataid,
                            doctype : doctype
                        },
                        dataType:'json',
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                        if (d.signature) {
                            if (d.qry) {
                                setTimeout(function () {
                                    create_iframe_preview(d.name, dataid, d.doctype);
                                    sign_btn.remove();
                                }, 700);
                            }
                        }
                    }).fail(function () {
                        swal("Error!", "Error applying signature to document!", "error");
                    });
                } else {
                    swal("Cancelled!", "Cancelled OTP generation!", "error");
                }
            });
        });
    };

    var upload_signature = function () {

    };

    var input_otp = function (otpid,name) {
        swal({
            title: "Sign " + name,
            text: "OTP",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            inputPlaceholder: "Provide OTP sent thru your E-Mail."
        }, function (inputValue) {
            if (inputValue === false) return false;
            if (inputValue === "") {
                swal.showInputError("Please enter OTP in order to proceed.");
                return false
            } else {
                var msg = 'otpid : ' + otpid + ' OTP : ' + inputValue;
                swal(name, msg, 'success');
                /*$.ajax({
                    url: PECO.base_url() + 'admin/signdocument',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        appid : dataid,
                        doctype : doctype,
                        onetime : inputValue
                    }
                }).done(function (d) {
                    swal(d.title, d.msg, d.func);

                }).fail(function () {
                    swal("Error!", "Inspection details not saved.", "error");
                })*/
            }
        });
    };

    var send_otp = function (dataid,name,docid) {
        swal({
            title: "Generate OTP for " + name,
            text: "Generate and send OTP to your email address?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Yes, send OTP!",
            closeOnCancel: false,
            showLoaderOnConfirm: true
        }, function(isConfirm){
            if (isConfirm) {
                //var msg = 'docid : ' + docid;

                //Generate OTP and send. Return if sending is successful.
                $.ajax({
                    url:PECO.base_url()+'admin/generatedocsotp',
                    type:'post',
                    data:{
                        id : dataid,
                        docid : docid
                    },
                    dataType:'json',
                }).done(function (d) {
                    swal("Sent!", "OTP for " + name +" has been sent to your email address.", "success");
                }).fail(function () {
                    swal("Error!", "Error generating and/or sending OTP!", "error");
                });

            } else {
                swal("Cancelled!", "Cancelled OTP generation!", "error");
            }
        });
    };

    /*var load_iframe_content = function (elem,dataid,doctype) {
        var ifrm = $('#iframe_'+elem+'_preview',document);
        var body = ifrm.contents().find('body');
        var title = ifrm.contents().find('title');

        if (title.length === 0) {
            var form = $('<form></form>').attr({
                method: 'post',
                action: PECO.base_url() + 'printer/docspreview'
            });

            var idfield = $('<input>').attr({
                type: 'hidden',
                name: 'id',
                value: dataid
            });

            form.append(idfield);

            var doctypefield = $('<input>').attr({
                type: 'hidden',
                name: 'doctype',
                value: doctype
            });

            form.append(doctypefield);

            body.append(form);
            form.submit();
        }
    };*/

    var create_iframe_preview = function (elem,dataid,doctype) {
        var tab = $('#doc_' + elem,document);
        tab.find('#iframe_' + elem + '_preview').each(function () {
            $(this).remove();
        });
        var new_ifrm = $('<iframe></iframe>').attr({
            id: 'iframe_' + elem + '_preview',
            src: '',
            style: 'width:100%; height:500px;'
        });

        tab.append(new_ifrm);
        var ifrm = $('#iframe_'+elem+'_preview',document);
        var body = ifrm.contents().find('body');

        var form = $('<form></form>').attr({
            method: 'post',
            action: PECO.base_url() + 'printer/docspreview'
        });

        var idfield = $('<input>').attr({
            type: 'hidden',
            name: 'id',
            value: dataid
        });

        form.append(idfield);

        var doctypefield = $('<input>').attr({
            type: 'hidden',
            name: 'doctype',
            value: doctype
        });

        form.append(doctypefield);
        body.append(form);
        form.submit();

    };

    return {
        init: function(dataid) {
            init_attachedments(dataid);
            dt_requirement_upload_list(dataid);
        },
        docs: function (dataid) {
            iframe_docs_preview(dataid);
        }
    }
}();