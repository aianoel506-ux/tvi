var SALES = function () {
    var iframe_prop_preview = $('#iframe_prop_preview',document);

    var init_sales = function (dataid) {
        iframe_prop_preview.attr('src',PECO.base_url() + 'cad/getproposalpdf/' + dataid);
        PECO.select2Basic($('#select2_du',document),'cad/select2du','Distribution Utility...',true,false,false);
        finalize_proposal(dataid);
    };

    var sales_handler = function (dataid) {
        var preview_src = iframe_prop_preview.attr('src');
        $('#btn_reload_preview',document).on('click',function () {
            iframe_prop_preview.attr('src',preview_src);
        });

        $('#btn_open_preview',document).on('click',function () {
            $.ajax({
                url: PECO.base_url() + 'cad/getproposal',
                type: 'post',
                dataType: 'json',
                data: {
                    id: dataid
                }
            }).done(function (d) {
                var win = window.open('','');
                PECO.pdfPreview(win,d.title,d.html);
            }).fail(function () {

            });
        });

        $('#frm_du_update',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data : this_.serialize()  + '&' + $.param({id : dataid})
            }).done(function (d) {
                PECO.initAlerts(d.msg,'DU Update',d.func);
            }).fail(function () {
            })
        });

        $(document).on('click','#btn_finalize_proposal',function () {
            finalize_proposal(dataid,true);
        });

        $(document).on('click','#btn_regenerate_proposal',function () {
            $.ajax({
                url : PECO.base_url() + 'cad/deleteproposal',
                type: 'post',
                dataType: 'json',
                data: {
                    id : dataid,
                }
            }).done(function (d) {
                if (d.qry) {
                    iframe_prop_preview.attr('src',preview_src);
                    $('#preview_actions',document).html(d.buttons);
                }
                PECO.initAlerts(d.msg,'New Proposal',d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var finalize_proposal = function (dataid,finalize) {
        $.ajax({
            url : PECO.base_url() + 'cad/finalizeproposal',
            type: 'post',
            dataType: 'json',
            data: {
                id : dataid,
                finalize : finalize
            }
        }).done(function (d) {
            if (d.msg.length > 0) {
                PECO.initAlerts(d.msg,'Finalize Document',d.func);
            }
            $('#preview_actions',document).html(d.buttons);
        }).fail(function () {
            PECO.phpError();
        })
    };

    return {
        init: function (dataid) {
            init_sales(dataid);
            sales_handler(dataid);
        }
    }
}();