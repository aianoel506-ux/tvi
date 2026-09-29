/**
 * Created by IT on 2/21/2018.
 */

var CNC = function () {
    PECO.getHighlightsPlugin();

    var auditpaymenttable = $(document).find('#auditpaymenttable');
    var cncpaymenttable = $(document).find('#cncpaymenttable');

    var init_payment_cnc_head = function(){
        PECO.DTDefault(cncpaymenttable, 'No record found!');
        var dataid = $(document).find('#idhidden').val();
        $.ajax({
            url:PECO.base_url()+"cnc/getpaymentdetails",
            type:"post",
            data:{"dataid":dataid},
            dataType:"json",
            beforeSend: function() {
                cncpaymenttable.dataTable().empty();
                PECO.DTphpLoading(cncpaymenttable, 'Loading OR requests...');
            }
        }).done(function (data) {
            cncpaymenttable.dataTable().empty();
            cncpaymenttable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.list,
                aoColumns: [
                    {"data": "orid",sClass: 'orid'},
                    {"data": "type", sClass: 'type', sWidth: '10%'},
                    {"data": "orno", sClass: 'orno'},
                    {"data": "amttotal", sClass: 'number amttotal text-primary'},
                    {"data": "amtvar", sClass: 'number amtvar'},
                    {"data": "amtfrtx", sClass: 'number amtfrtx'},
                    {"data": "descs", sClass: 'descs', sWidth: '20%'}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    //lucky was here
                    $(nRow).find('td.descs [data-toggle="popover"]').popover({
                        animate: true,
                        html: true,
                        template: '<div class="popover popover-info"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                    });
                }
            });
            init_cnc_summary();
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_cnc_summary = function(){
        var amountotal = 0;
        var amountvat = 0;
        var amountfttx = 0;

        cncpaymenttable.find('td.amttotal').each(function () {
            amountotal +=Number($(this).find('input').val());
        });
        cncpaymenttable.find('td.amtvar').each(function () {
            amountvat +=Number($(this).find('input').val());
        });
        cncpaymenttable.find('td.amtfrtx').each(function () {
            amountfttx +=Number($(this).find('input').val());
        });
        $(document).find('#cncamounttotal').text(amountotal.toFixed(2));
        $(document).find('#cncamountvat').text(amountvat.toFixed(2));
        $(document).find('#cncamountfrtx').text(amountfttx.toFixed(2));
    };

    var populate_audit_table = function(){
        var dataid = $(document).find('#idhidden').val();
        PECO.DTDefault(auditpaymenttable, 'No record found!');
        $.ajax({
            url:PECO.base_url()+"cnc/getpaymentdetails",
            type:"post",
            data:{"dataid":dataid},
            dataType:"json",
            beforeSend: function() {
                auditpaymenttable.dataTable().empty();
                PECO.DTphpLoading(auditpaymenttable, 'Loading OR requests...');
            }
        }).done(function (data) {
            auditpaymenttable.dataTable().empty();
            auditpaymenttable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.list,
                aoColumns: [
                    {"data": "orid",sClass: 'orid'},
                    {"data": "type", sClass: 'type', sWidth: '10%'},
                    {"data": "orno", sClass: 'orno'},
                    {"data": "amttotal", sClass: 'number amttotal text-primary'},
                    {"data": "amtvar", sClass: 'number amtvar'},
                    {"data": "amtfrtx", sClass: 'number amtfrtx'},
                    {"data": "descs", sClass: 'descs', sWidth: '20%'},
                    {"data": "select",sClass:'input select', sWidth: '15%'},
                    {"data": "control",sClass:'control'}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    //lucky was here
                    $(nRow).find('td.select select').select2({
                        "allowClear": true,
                        "placeholder": 'Select..'
                    });
                    $(nRow).find('td.descs [data-toggle="popover"]').popover({
                        animate: true,
                        html: true,
                        template: '<div class="popover popover-danger"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                    });
                }
            });
            init_audit_summary();
        }).fail(function () {
            PECO.phpError();
        });
    };

    var init_audit_summary = function(){
        var amountotal = 0;
        var amountvat = 0;
        var amountfttx = 0;

        auditpaymenttable.find('td.amttotal').each(function () {
            amountotal +=Number($(this).find('input').val());
        });
        auditpaymenttable.find('td.amtvar').each(function () {
            amountvat +=Number($(this).find('input').val());
        });
        auditpaymenttable.find('td.amtfrtx').each(function () {
            amountfttx +=Number($(this).find('input').val());
        });
        $(document).find('#auditamounttotal').text(amountotal.toFixed(2));
        $(document).find('#auditamountvat').text(amountvat.toFixed(2));
        $(document).find('#auditfrtx').text(amountfttx.toFixed(2));
    };
    var accomplishtransaction = function (){
        $(document).on('click','#accomplishbtn',function (e) {
           e.preventDefault();
            var dataid = $(document).find('#idhidden').val();
            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to accomplish this transaction?</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:base_url+"audit/accomplishtransaction",
                        type:"post",
                        data:{"dataid":dataid},
                        dataType:"json"
                    }).done(function (d) {
                        if(d.qry == true){
                            PECO.initAlerts(d.msg ,"Accomplish", d.func);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });
    };
    var init_payment_audit = function(){
        accomplishtransaction();
        populate_audit_table();
        $(document).on('click','#btn_cancel',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            var dataor = this_.attr("data-or");
            var moduleid = $(document).find('#moduleid').val();
            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to cancel this transaction?</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:PECO.base_url()+ "audit/cancelor",
                        type:"post",
                        data:{"dataid":dataid,"moduleid":moduleid,"dataor":dataor},
                        dataType:"json"
                    }).done(function (d) {
                        if(d.qry == true){
                            PECO.initAlerts(d.msg,"OR Transaction",d.func);
                            populate_audit_table();
                        }else{
                            PECO.initAlerts(d.msg,"OR Transaction",d.func);

                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        $(document).on('click','#btn_update',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var dataid = this_.attr("data-id");
            var moduleid = $(document).find('#moduleid').val();
            var datapayform = this_.attr("data-payform");
            var this_tr = this_.closest('tr');
            var selectval = this_tr.find('td.select select').select2('val');
            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to update this transaction?</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url:PECO.base_url()+ "audit/updateor",
                        type:"post",
                        data:{"dataid":dataid,"moduleid":moduleid,"datapayform":datapayform,"selectval":selectval},
                        dataType:"json"
                    }).done(function (d) {
                        if(d.qry == true){
                            PECO.initAlerts(d.msg,"OR Transaction",d.func);
                            populate_audit_table();
                        }else{
                            PECO.initAlerts(d.msg,"OR Transaction",d.func);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });


    };

    return{
        init:function () {

        },

        orvoidcnch: function() {
            init_payment_cnc_head();
        },

        orvoidaudit: function() {
            init_payment_audit();
        }
    };
}();