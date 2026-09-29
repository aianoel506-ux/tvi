var CAD = function() {
    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();

    var cad_trn_list =  $('#cad_trn_list' , document);

    var init_cad_fn = function() {
        var init_val = $('#select2routes', document).val();
        load_cad_datatable(init_val);

        PECO.select2Basic($('#select2routes', document), 'cad/select2routes', 'Select route', true, false);

        $('#select2routes', document).change(function(d) {
            var this_ = $(this);
            var this_val = this_.val();
            load_cad_datatable(this_val);
        });

        PECO.dtSubDetails(cad_trn_list, 'cad/getapplicaitonsubdetails');

        cad_trn_list.on('submit', 'tr #frm_upload_pic', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url:PECO.base_url() + 'query/uploadpp',
                data: new FormData(form[0]),
                dataType: 'json',
                type: 'post',
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData: false,        // To send DOMDocument or non processed data file it is set to false
            }).done(function(d){
                PECO.initAlerts(d.msg, 'Picture Upload', d.func);
            }).fail(function(){
                alert("ERROR PHP");
            });
        });

        cad_trn_list.on('click', '#btn_upload_pic', function(e) {
            e.preventDefault();
            $('#frm_upload_pic', cad_trn_list).trigger('submit');
        });

        $('#ajax_modal').on('shown.bs.modal', function (e) {
            var map = new GMaps({
                el: '#map',
                lat:36.184164,
                lng:43.975181,
                click:function (e) {
                    console.log(e.latLng.lat());
                }
            });
        });
    };

    var load_cad_datatable = function(route,district){
        var route_ = (route) ? route : false;
        var district_ = (district) ? district : false;
        $.ajax({
            url: PECO.base_url() + 'cad/getnewconnectionlist',
            type: 'POST',
            dataType: 'json',
            data: {
                route: route_,
                district : district_
            },
            beforeSend: function(){
                $('#cad-trn-list' , document).dataTable().empty();
                PECO.DTphpLoading(cad_trn_list, 'Loading please wait... ');
            }
        }).done(function (d) {
            cad_trn_list.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.data,
                aoColumns: [
                    {data: 'expand', sWidth: '1%', sClass: 'text-align-center'},
                    {data: 'essrno', sWidth: '4%',sClass: 'text-primary bold'},
                    {data: 'created', sWidth:'10%'},
                    {data: 'updated', sWidth:'10%'},
                    {data: 'rateclass'},
                    {data: 'details', sClass: 'trn', sWidth: '300px'},
                    {data: 'from', sClass: 'text-primary'},
                    {data: 'trn', sClass: 'text-danger'},
                    {data: 'remarks', sClass: 'text-info'},
                    {data: 'status', sClass: 'text-info'},
                    {data: 'control', sClass: 'controls', sWidth: '13%'}
                ],
                searchHighlight: true,
                "order": [[2, "desc"]],
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
                },
                searchHighlight: true,
                pageLength : 50,
                bLengthChange: false,
                fnRowCallback: function(nRow, aData, i) {
                    $('.tooltips', nRow).tooltip();
                    PECO.popOverRow($('.row-pic', nRow), true, true, 'popover-info');
                }
            });
        });
    };

    var inspection_events = function () {
        var select_district = $('#select_district', document);
        var print_inspection_list = $('#print_inspection_list', document);
        load_cad_datatable(1,select_district.val());

        select_district.on('change', function () {
            var this_ = $(this);
            load_cad_datatable(1,this_.val());
        });

        PECO.dtSubDetails(cad_trn_list, 'cad/getapplicaitonsubdetails');

        print_inspection_list.on('click', function () {
            var this_ = $(this);
            var this_html = this_.html();
            //var win = window.open(PECO.base_url() + 'printer/preview', '');
            var win = window.open('','');
            $.ajax({
                url: base_url + 'cad/printinspectionlist',
                type: 'post',
                dataType: 'json',
                data: {
                    route: 2,
                    district: select_district.val()
                },
                beforeSend: function () {
                    PECO.btnLoading(this_,'Preparing List...')
                }
            }).done(function (d) {

                //win.document.title = 'Inspection List';
                //win.document.body.innerHTML = d.html;
                //var html = JSON.stringify(d.html);
                PECO.pdfPreview(win,'Inspection List',d.html);
                PECO.btnSuccess(this_, 'Done', this_html, 'btn-primary');
            });
        });
    };

    var pdfPreview = function (win,title,html) {

        win.document.title = (title) ? title : 'Print Preview';

        const form = document.createElement('form');
        form.method = 'post';
        form.action = PECO.base_url() + 'printer/preview';

        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'html';
        hiddenField.value = html;

        form.appendChild(hiddenField);

        win.document.body.appendChild(form);
        form.submit();
    };

    return {
        init: function() {
            init_cad_fn();
        },
        inspection: function () {
            inspection_events();
        },
        legal: function () {
            var array = [23,67,78];
            load_cad_datatable(array,false);
        }
    }
}();