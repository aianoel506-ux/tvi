var BOS = function () {
    // INITIALIZE HIGHLIGHTS SEARCH IN TABLE
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();


    var frm_budget_list = $('#frm_budget_list', document);
    var select2ccid = $('#select2ccid', document);
    var select2budgettype = $('#select2budgettype', document);
    var tbl_budget_list = $('#tbl_budget_list', document);
    var itemstable = $('#itemstable' , document);
    var budgetapprovaltbl = $('#budgetapprovaltbl' , document);

    var init_bos = function() {
        init_filter_budget();

        PECO.select2Basic(select2ccid, 'bos/select2ccid', 'Select Cost Center.. ', true);
        PECO.select2Basic(select2budgettype, 'bos/select2budgettype', 'Select Budget Type.. ', false);
        PECO.DTDefault(tbl_budget_list, 'Filter Budget first!');

        PECO.dtSubDetails(tbl_budget_list, 'bos/subdetails', false, 'sub-table');


        $(document).on('click','#deleteitem',function () {
            var this_ = $(this);
            var this_dataid = this_.attr("data-id");
            var this_tr = this_.closest('tr');

            swal({
                title: "Are you sure?",
                text: "Item will be deleted.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'bos/deleteitem',
                        type:'post',
                        data:{"dataid": this_dataid},
                        dataType:'json'
                    }).done(function (d) {
                        swal("Delete", d.msg, d.func);
                        if(d.qry){
                            this_tr.remove();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });

                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(document).on('click','table.table > tbody > tr > td > a',function () {
            var this_ = $(this);
            var bositemid = this_.attr("data-arr");
            setTimeout(function(){
                fetchitems(bositemid);
            }, 500);
            setTimeout(function(){
                PECO.select2Basic($('#items',document),'bos/getitems','Select Items',false,false,false);
            }, 1000);

        });


        $(document).on('submit','#submititemdetails',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg , "PECO" , d.func);
                if(d.qry == true){
                    var table = $('#itemstable').DataTable();
                    table.row.add( {
                        "num":       d.idcount,
                        "item":  d.descs,
                        "qty":   d.quantity,
                        "control":  ''
                    } ).draw();

                    $('#submititemdetails')[0].reset();
                    $('#items').select2('val','');
                    init_filter_budget();
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#submittransaction',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "New budget will be created.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (d) {
                        swal("PECO", d.msg, d.func);
                        if(d.qry == true){
                            $('#selectgroup').select2('val',  '');
                            $('#selectyear').select2('val',  '');
                            $('#submittransaction')[0].reset();
                            $('#transactionlist').html('');
                            fetchitems(d.bositemid);
                            init_filter_budget();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(document).on('click','#gobtn',function () {
            var count = $('#selectquatercount').val();
            if(count == ''){
                PECO.initAlerts("Incorrect value.","Incorrect","info");
            }else{
                $('#transactionlist').html('');
                var count = $(document).find('#selectquatercount').val();
                var i = 1;
                var html = '';
                html += '<h5>Budget details:</h5>';
                html += '<table class="table table-bordered table-responsive table-striped tbl-sm" id="quarterbudgettable">';
                html += '<thead>';
                html += '<th></th>';
                html += '<th width="190px">Quarter</th>';
                html += '<th width="90px">Item Count</th>';
                html += '<th>Amount Each</th>';
                html += '<th>Total</th>';
                html += '</thead>';
                html += '<tbody>';


                for(i = 1;i<=count;i++){
                    html += '<tr>';
                    html += '<td>'+i+'</td>';
                    html += '<td><select name=\"quarter[]\"  class=\"form-control\" id=\"quarter\">\n" +\n\' +\n' +
                        '                    \'                    "                                                            <option value=\'1\'>1st Quarter</option>\n" +\n\' +\n' +
                        '                    \'                    "                                                            <option value=\'2\'>2nd Quarter</option>\n" +\n\' +\n' +
                        '                    \'                    "                                                            <option value=\'3\'>3rd Quarter</option>\n" +\n\' +\n' +
                        '                    \'                    "                                                            <option value=\'4\'>4th Quarter</option>\n" +\n\' +\n' +
                        '                    \'                    "                                                        </select></td>';
                    html += '<td><input type="text" id="itemcount" name="itemcount[]" placeholder="Count" class="form-control" /></td>';
                    html += '<td><input type="text" id="amteach" name="amteach[]" placeholder="Amount" class="form-control" /></td>';
                    html += '<td><input readonly type="text" id="total" name="total[]" placeholder="Total" class="form-control" /></td>';
                    html += '</tr>';
                }
                html += '</tbody>';
                html += '</table>';
                html += '<button type="submit" class="btn btn-primary "><i class="fa fa-save"></i> Save</button>';

                $('#transactionlist').html(html);
                //  $('#quarterbudgettable').dataTable();
            }

        });

        $(document).on('click','#btn_delete_bos',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Budget will be deleted.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        url:PECO.base_url()+'bos/removebudget',
                        type:'post',
                        data:{"dataid" : dataid},
                        dataType:'json'
                    }).done(function (data) {
                        swal("PECO", data.msg, data.func);
                        if(data.qry == true){
                            init_filter_budget();
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });
        });

        $(document).on('keyup','#itemcount',function () {
            var this_ = $(this);
            var amounteach = this_.closest('tr').find('input#amteach').val();
            var total = this_.closest('tr').find('input#total');
            total.val(this_.val() * amounteach);
        });
        $(document).on('keyup','#amteach',function () {
            var this_ = $(this);
            var amounteach = this_.closest('tr').find('input#itemcount').val();
            var total = this_.closest('tr').find('input#total');
            total.val(this_.val() * amounteach);
        });

        $(document).on('change','#acctcode',function () {
            var this_ = $(this);
            var this_val = this_.val();
            var bosid = this_.closest('tr').find('td input.bosid').val();
            $.ajax({
                url:PECO.base_url()+'bos/updateledgerrefid',
                type:'post',
                data:{"bosid":bosid,"refidval":this_val},
                dataType:'json'
            }).done(function (d) {
                PECO.initAlerts(d.msg,"PECO",d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#btn-expand',function () {
            setTimeout(function(){
                $('input#acctcode.form-control',document).each(function () {
                    var this_ = $(this);
                    PECO.select2Basic(this_, 'bos/getacctcodelist' , 'Select Code List', false, false, false, false, true);
                });
            }, 1000);

        });
        $(document).on('keypress', '#year', function(e) {
            if(e.keyCode == 13 || e.which == 13) {
                init_filter_budget();
                e.preventDefault();
            }
        });

        $(document).on('change', '#filters input', function(e) {
            var this_ = $(this);
            init_filter_budget();
        });

        frm_budget_list.submit(function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Submit for Approval",
                text: "Are you sure to submit your budget preparation?",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if(isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (d) {
                        swal('Submit Budget', d.msg, d.func);
                        setTimeout(function(){
                            swal.close();
                        }, 2000);
                    });
                }else{
                    swal.close();
                }
            });
        });

    };

    var fetchitems = function(bositemid){

            $.ajax({
                url:PECO.base_url()+'bos/getbositems',
                type:'post',
                data:{"bositemid" : bositemid},
                dataType:'json'
                }).done(function (data) {
                console.log(data.bositemslist);
                $('#itemstable').dataTable().empty();
                $('#itemstable').dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: data.bositemslist,
                    aoColumns: [
                        {"data":"num"},
                        {"data":"item"},
                        {"data":"qty"},
                        {"data":"control"}
                    ],

                    searchHighlight: true,
                    fnRowCallback: function(nRow, data, iDisplayIndex) {
                        // CREATE SORT NUMBER
                       // $(nRow).addClass(data.rowcolor);
                        var index = iDisplayIndex;
                        $('td:eq(0)',nRow).html(index + 1);
                    }
                });

            }).fail(function () {
                PECO.phpError();
            });
    };

    var init_filter_budget = function() {


        var ccid = $('#select2ccid', document).val();
        var types = $('#select2budgettype', document).val();
        var year = $('#year', document).val();

        $(document).find('#frm_new_budget').attr('data-arr',year+'-'+ccid);
        $(document).find('#frm_new_budget').attr('data-view',types);

        $.ajax({
            url: PECO.base_url() + 'bos/getbudget',
            type: 'post',
            data: {'types': types, 'ccid': ccid, 'year': year},
            dataType: 'json',
            beforeSend: function() {
                tbl_budget_list.dataTable().empty();
                PECO.DTphpLoading(tbl_budget_list, 'Loading budgets...');
            }
        }).done(function(d) {
            $('#budgetlabel', document).text(d.budgetlabel);
            $('#total_amt', document).text(d.totalamt);
            $('#total_exp', document).text(d.totalexp);
            $('#total_bal', document).text(d.totalbal);
            $('#total_item', document).text(d.totalitem);

            tbl_budget_list.dataTable().empty();
            tbl_budget_list.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                pageLength: 10,
                saveState: true,
                order: [['1', 'asc']],
                aoColumns: [
                    {"data": "expand", sWidth: '', sClass: 'text-align-center', bSortable: false, searchable: false},
                    {"data": "num", sWidth: '', sClass: '', searchable: false},
                    {"data": "codes", sWidth: '', sClass: 'font-blue-steel'},
                    {"data": "desc", sWidth: '20%', sClass: 'text-bold font-yellow-casablanca'},
                    {"data": "acctcode", sWidth: '', sClass: 'text-success number'},
                    {"data": "prevamt", sWidth: '', sClass: 'text-info number'},
                    {"data": "amt", sWidth: '', sClass: 'text-primary number'},
                    {"data": "items", sWidth: '', sClass: 'number'},
                    {"data": "adjp", sWidth: '', sClass: 'number text-info'},
                    {"data": "adjm", sWidth: '', sClass: 'number text-warning'},
                    {"data": "exp", sWidth: '', sClass: 'text-danger number'},
                    {"data": "bal", sWidth: '', sClass: 'text-success number'},
                    {"data": "status", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: '', bSortable: false, searchable: false},
                    {"data": "checkbox", sWidth: '', sClass: '', bSortable: false, searchable: false}
                ],
                "searchHighlight": true,
                "language": PECO.DTEmptyMessage('No budget found!'),
                fnRowCallback: function(nRow, aData, Index) {
                    PECO.iCheckRow($('#selected', nRow), 'minimal','blue');
                },
                fnDrawCallback : function() {
                    $('.dataTables_length select').select2({
                        'placeholder': 'Select...',
                        width: '100px'
                    }).addClass('input-xs');

                    $('.dataTables_filter input').attr('placeholder', 'Search..');
                }
            });
        }).fail(function() {
            PECO.phpError();
        });
    };

    /* --------    for approval budget script   -------------*/
    var init_budgetapproval = function(bosid , ccid , year , types){
        fetchitemstoapprove(bosid , ccid , year , types);
        events(bosid , ccid , year , types);
        PECO.dtSubDetails(budgetapprovaltbl, 'bos/subdetailsapproval', false, 'sub-table');
    };
    var events = function(bosid , ccid , year , types){
        $(document).on('click','#approvebudgetbtn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

        });
        $(document).on('click','#disapprovebudgetbtn',function () {
            var this_ = $(this);
            var dataid = this_.attr("data-id");

        });
    };
    var fetchitemstoapprove = function(bosid , ccid , year , types){
        //budgetapprovaltbl

        $.ajax({
            url: PECO.base_url() + 'bos/getbudget',
            type: 'post',
            data: {'types': types, 'ccid': ccid, 'year': year},
            dataType: 'json',
            beforeSend: function() {
                budgetapprovaltbl.dataTable().empty();
                PECO.DTphpLoading(budgetapprovaltbl, 'Loading budgets...');
            }
        }).done(function(d) {
            $('#budgetlabel', document).text(d.budgetlabel);
            $('#totalamount', document).text(d.totalamt);
            $('#totalexp', document).text(d.totalexp);
            $('#totalbal', document).text(d.totalbal);
            $('#totalitems', document).text(d.totalitem);

            budgetapprovaltbl.dataTable().empty();
            budgetapprovaltbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                aaData: d.list,
                bSort: true,
                pageLength: 10,
                saveState: true,
                order: [['1', 'asc']],
                aoColumns: [
                    {"data": "expand", sWidth: '', sClass: 'text-align-center', bSortable: false, searchable: false},
                    {"data": "num", sWidth: '', sClass: '', searchable: false},
                    {"data": "codes", sWidth: '', sClass: 'font-blue-steel'},
                    {"data": "desc", sWidth: '20%', sClass: 'text-bold font-yellow-casablanca'},
                    {"data": "amt", sWidth: '', sClass: 'text-primary number'},
                    {"data": "items", sWidth: '', sClass: 'number'},
                    {"data": "adjp", sWidth: '', sClass: 'number text-info'},
                    {"data": "adjm", sWidth: '', sClass: 'number text-warning'},
                    {"data": "exp", sWidth: '', sClass: 'text-danger number'},
                    {"data": "bal", sWidth: '', sClass: 'text-success number'},
                    {"data": "approvalbtn", sWidth: '', sClass: 'text-success number'}
                ],
                "searchHighlight": true,
                "language": PECO.DTEmptyMessage('No budget found!')
            });
        }).fail(function() {
            PECO.phpError();
        });

    };
    /* --------    end of approval budget script   -----------*/


    return {
        init: function() {
            init_bos();
        },
        budgetapproval:function (bosid , ccid , year , types) {
            init_budgetapproval(bosid , ccid , year , types);
        }
    }
}();
