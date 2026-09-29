/**
 * Created by SE on 0010, May 10, 2017.
 */

var CADMAIN = function() {

    PECO.getHighlightsPlugin();

    var req_tbl = $('#tbl_req_list');

    var init_requirements = function() {
        $("#acct_rate").select2({
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_rate_class/",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data.list, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    return {
                        results: myResults
                    };
                }

            },
        }).change(function () {
            console.log('SPECS: ' + $(this).val());
        });


        $("#stat_conn").select2({
            tags: false,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 1,
            ajax: {
                url: PECO.base_url() + "admin/get_types/SAPPS",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data.list, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    // IF (THIS NOT ALLOW MULTIPLE SELECTION (DISABLED)
                    //if($("#acct_type").val()==""){
                    return {
                        results: myResults
                    };
                    //}

                }

            },
        }).change(function(e) {
            $('.connection .number').html(e.added.id);
            $('.connection .name').html(e.added.text);
        });


        $("#acct_rate").select2({
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_rate_class/",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data.list, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    // IF (THIS NOT ALLOW MULTIPLE SELECTION (DISABLED)
                    //if($("#acct_type").val()==""){
                    return {
                        results: myResults
                    };
                    //}

                }

            },
        }).change(function (e) {
            $('.rate .number').html(e.added.id);
            $('.rate .name').html(e.added.text);
        });


        // INITIALIZE STATUS OF CONNECTION
        $("#owner_type").select2({
            tags: false,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 5,
            ajax: {
                url: base_url + "admin/get_types/STAPPS",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };

                },
                results: function (data) {
                    var myResults = [];
                    $.each(data.list, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    // IF (THIS NOT ALLOW MULTIPLE SELECTION (DISABLED)
                    //if($("#acct_type").val()==""){
                    return {
                        results: myResults
                    };
                    //}

                }

            },
        }).change(function (e) {

            $('.ownership .number').html(e.added.id);
            $('.ownership .name').html(e.added.text);
        });



        $("#loc_type").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_types/STLAPPS",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data.list, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    return {
                        results: myResults
                    };
                }

            },
        }).change(function (e) {
            $('.location .number').html(e.added.id);
            $('.location .name').html(e.added.text);
        });

        //PECO.select2Basic($("#acct_req"), 'admin/searchrequirements/', 'Select requirements', false, false);


        $("#acct_req").select2({
            tags: false,
            multiple: false,
            ajax: {
                url: PECO.base_url() + "admin/searchrequirements",
                dataType: 'json',
                type: "post",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                    };
                },
                results: function (data) {
                    console.log(data);
                    return {
                        results: $.map(data.list, function (item) {
                            return {
                                text: item.text,
                                id: item.id,
                            };
                        })
                    };

                }
            }
        });


        req_tbl.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
        });

        $('#btn_reset').click(function(){
            var this_ = $(this);
            this_.closest('#requirements').find('input').select2('val', '');
            $('.tile .name').html('');
            $('.tile .number').html('');
            req_tbl.dataTable().empty();
            req_tbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
            });
        });

        $('#frm_get_requirements').submit(function(e){
            req_tbl.dataTable().empty();
            var form = $(this);
            e.preventDefault();
            init_req_dtable(form);
        });

        req_tbl.on('click', '#del_btn', function(e){
           var this_ = $(this);
            e.preventDefault();
            $.ajax({
                url: PECO.base_url() + 'admin/deletecadrequirements',
                type: 'post',
                data: {'id': this_.attr('data-id')},
                dataType: 'json',
            }).done(function(d){
                this_.closest('tr').addClass('danger').fadeOut('fast');
                PECO.initAlerts(d.msg, 'Delete', d.func);
                init_req_dtable(form);
            }).fail(function(){
                PECO.phpError();
            });
        });

        $('#frm_get_requirements').on('click', '#btn_add', function(e){
            req_tbl.dataTable().empty();
            var form = $(this).closest('form');
            e.preventDefault();
            $.ajax({
                url: PECO.base_url()+'admin/addcadrequirements',
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d){
                PECO.initAlerts(d.msg, 'Add Requirements', d.func);
                init_req_dtable(form);
            }).fail(function(){
                PECO.phpError();
            });
        });
    };
    var init_req_dtable = function (form) {

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json',
        }).done(function(d){
            req_tbl.dataTable().empty();
            req_tbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {data: 'num', sClass: '', sWidth: '30px'},
                    {data: 'code', sClass: '', sWidth: ''},
                    {data: 'desc', sClass: '', sWidth: ''},
                    {data: 'control', sClass: '', sWidth: '30px'},
                ],
                searchHighlight: true,
            });
        }).fail(function() {
            PECO.DTphpError(req_tbl);
        });
    };
    return {
        requirements: function() {
            init_requirements();
        }
    }
}();
