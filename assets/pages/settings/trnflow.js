
var TRANSACTIONSFLOW = function(){
    PECO.getSelect2Plugins();
    PECO.getHighlightsPlugin();
    var trnflowmaintbl = $('#trnflowmaintbl' , document);
    var trnflowstagestbl = $('#trnflowstagestbl' , document);

    var inittrnmainflow = function () {
        PECO.dtSubDetails(trnflowmaintbl , 'settings/gettransactionflowdetails' , false, 'sub-table');

        inittrnmainflowdata();
        init_events();
    };
    var inittrnmainflowdata = function(){
        $.ajax({
            url: PECO.base_url() + 'settings/getmainflowtrndata',
            type: 'post',
            dataType: 'json',
            data: {}
        }).done(function (data) {

            trnflowmaintbl.dataTable().empty();
            trnflowmaintbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bSort: true,
                bStateSave: false,
                bProcessing: true,
                keys: true,
                language: PECO.DTEmptyMessage(),
                aaData: data.trnlist,
                aoColumns: [
                    {"data":"expand", sClass:'expand text-align-center', sWidth:'10px'},
                    {"data": "codes"},
                    {"data": "flowid"},
                    {"data": "names"},
                    {"data": "desc"},
                    {"data": "control"}
                ],
                "order": [[ 1, "asc" ]],
                "columnDefs": [
                    {"targets": 0, "orderable": false},
                    {"targets": -1, "orderable": false}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data, iDisplayIndex) {
                    PECO.dtExpandBtn(nRow, data.expand);
                }
            });
            PECO.initDTNicescroller();
        }).fail(function () {
            PECO.phpError();
        });
    };
    var inittrnstagesflowdata = function(el,id){
        $.ajax({
            url: PECO.base_url() + 'settings/gettrnflowmainstages',
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            },
            beforeSend: function () {
                PECO.DTphpLoading(trnflowstagestbl,'Loading Flow Stages...');
            }
        }).done(function (data) {
            el.dataTable().empty();
            el.dataTable({
                bDestroy: true,
                bPaginate: false,
                bScrollCollapse: true,
                sScrollY: '200px',
                bFilter: false,
                bInfo: true,
                bSort: false,
                bStateSave: false,
                bProcessing: true,
                keys: true,
                language: PECO.DTEmptyMessage(),
                aaData: data.stagelist,
                aoColumns: [
                    {"data": "levels", sClass: 'text-align-center', sWidth: '10%'},
                    {"data": "desc"},
                    {"data": "moduleid", sWidth: '30%'},
                    {"data": "move", sClass: 'text-align-center re-align', sWidth: '26%'},
                    {"data": "control", sClass: 'text-align-center control', sWidth: '10px'}
                ],
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    {"targets": 0, "orderable": true},
                    {"targets": -1, "orderable": false}
                ],
                searchHighlight: true,
            });
            PECO.initDTNicescroller();
        }).fail(function () {
            PECO.phpError();
        });
    };
    var init_events = function(){

    };

    var init_trn_flow_stages_ = function (id) {
        inittrnstagesflowdata(id);
        trnflowstagestbl.on('click','#move_up,#move_down,#move_top,#move_bottom',function () {
            var this_ = $(this);
            var this_td = this_.closest('td');
            var this_tr = this_.closest('tr');
            var move_top = this_td.find('#move_top');
            var move_up = this_td.find('#move_up');
            var move_down = this_td.find('#move_down');
            var move_bottom = this_td.find('#move_bottom');
            var row = this_.parents("tr:first");
            //--- Parameters ---//
            var action = this_.attr('data-action');
            var moduleid = this_tr.find('#module_id').val();
            var flowid = this_tr.find('#flow_id').val();
            var stageLvl = this_tr.find('#stage_level').val();

            var update = update_trn_flow_level(flowid,action,moduleid,stageLvl);
            alert(update.qry);

            if (update.qry === true) {
                if (this_.is("#move_up")) {
                    if (this_tr.is('tr:last')) {
                        this_tr.prev().find('#move_bottom').addClass('hidden');
                        this_tr.prev().find('#move_down').addClass('hidden');
                        move_down.removeClass('hidden');
                        move_bottom.removeClass('hidden');
                    } else if (this_tr.prev().is(':first-child')) {
                        this_tr.prev().find('#move_top').addClass('hidden');
                        this_tr.prev().find('#move_up').addClass('hidden');
                        move_top.removeClass('hidden');
                        move_up.removeClass('hidden');
                    }
                    row.insertBefore(row.prev());
                } else if (this_.is("#move_down")) {
                    if (this_tr.is(':first-child')) {
                        this_tr.next().find('#move_top').addClass('hidden');
                        this_tr.next().find('#move_up').addClass('hidden');
                        move_up.removeClass('hidden');
                        move_top.removeClass('hidden');
                    }
                    row.insertAfter(row.next());
                } else if (this_.is("#move_top")) {
                    /*
                    row.insertBefore(trnflowstagestbl.find("tr:first"));
                    this_.addClass('hidden');
                    move_up.addClass('hidden');
                    move_bottom.removeClass('hidden');
                    move_down.removeClass('hidden');*/
                    inittrnstagesflowdata(id);
                } else {
                    row.insertAfter(trnflowstagestbl.find("tr:last"));
                    this_.addClass('hidden');
                    move_down.addClass('hidden');
                    move_up.removeClass('hidden');
                    move_top.removeClass('hidden');
                }

                this_tr.find('td:first').html(update.newlevel);
            }
        });

    };

    var init_trn_flow_stages = function (el,id) {
        inittrnstagesflowdata(el,id);
        trnflowstagestbl.on('click','#move_up,#move_down,#move_top,#move_bottom',function () {
            var this_ = $(this);
            var this_td = this_.closest('td');
            var this_tr = this_.closest('tr');
            var move_top = this_td.find('#move_top');
            var move_up = this_td.find('#move_up');
            var move_down = this_td.find('#move_down');
            var move_bottom = this_td.find('#move_bottom');

            //update_trn_stages_level(this_); //Lucky's function
            update_trn_flow_level(this_);


        });

    };

    var update_trn_stages_level = function(this_) {
        var this_tr = this_.closest('tr');
        var this_tr_before = this_tr.prev();
        var this_tr_next = this_tr.next();

        var this_id = this_.attr('data-id');
        var this_level = this_.attr('data-level');
        var this_action = this_.attr('data-action');
        var this_flowid = this_.attr('data-flowid');
        $.ajax({
            url: PECO.base_url() + 'settings/updatestagelevel',
            type: 'post',
            data: {id: this_id, level: this_level, flowid: this_flowid, action: this_action},
            dataType: 'json',
        }).done(function(d) {
            console.log(d);

            if(d.action == 1) {
                this_tr.insertBefore(trnflowstagestbl.find("tr:first"));
            }
            if(d.action == 2) {
                this_tr.insertBefore(this_tr_before);
            }
            if(d.action == 3) {
                this_tr.insertAfter(this_tr_next);
            }
            if(d.action == 4) {
                this_tr.insertAfter(trnflowstagestbl.find("tr:last"));
            }

            this_.attr('data-level', d.level);

            for(b = 0; b <= d.btns.length; b++) {
                var bid = d.btns[b].id;
                var blvl = d.btns[b].level;
                $('.btn_' +bid, document).attr('data-level', blvl);
            }

        }).fail(function() {
            alert('PHP ERROR!');
        });
    };

    var update_trn_flow_level = function(this_) {
        var this_tr = this_.closest('tr');
        var this_tbl = this_.closest('table');
        var this_tr_first = this_tbl.find('tr').eq(1);
        var this_tr_before = this_tr.prev();
        var this_tr_next = this_tr.next();
        var this_tr_last = this_tbl.find("tr:last");
        var this_tr_control = this_tr.find('.control').find('button').html();
        var this_td = this_.closest('td');

        var move_top = this_td.find('#move_top');
        var move_up = this_td.find('#move_up');
        var move_down = this_td.find('#move_down');
        var move_bottom = this_td.find('#move_bottom');

        var this_id = this_.attr('data-id');
        var this_level = this_.attr('data-level');
        var this_action = this_.attr('data-action');
        var this_flowid = this_.attr('data-flowid');
        $.ajax({
            url: PECO.base_url() + 'settings/updatetrnflowlevel',
            type: 'post',
            data: {id: this_id, level: this_level, flowid: this_flowid, action: this_action},
            dataType: 'json',
            beforeSend: function () {
                this_tr.find('.control').find('button').html('<i class="fa fa-spinner fa-spin"></i>');
            }
        }).done(function(d) {
            console.log(d);
            this_tr.find('.control').find('button').html(this_tr_control);

            if(d.action == 1) {
                this_tr.prevAll('tr').each(function () {
                    var num = $(this).find('td:first').text();
                    var newnum = parseInt(num) + 1;
                    $(this).find('td:first').text(newnum);
                });
                this_tr.find('td:first').text(d.newlvl);
                this_tr.insertBefore(this_tr_first);
            }
            if(d.action == 2) {
                var prevnum = this_tr_before.find('td:first').text();
                var thisnum = this_tr.find('td:first').text();
                this_tr_before.find('td:first').text(thisnum);
                this_tr.find('td:first').text(prevnum);
                this_tr.insertBefore(this_tr_before);
            }
            if(d.action == 3) {
                var nextnum = this_tr_next.find('td:first').text();
                var thisnum = this_tr.find('td:first').text();
                this_tr_next.find('td:first').text(thisnum);
                this_tr.find('td:first').text(nextnum);
                this_tr.insertAfter(this_tr_next);
            }
            if(d.action == 4) {
                this_tr.nextAll('tr').each(function () {
                    var num = $(this).find('td:first').text();
                    var newnum = parseInt(num) - 1;
                    $(this).find('td:first').text(newnum);
                });
                this_tr.find('td:first').text(d.newlvl);
                this_tr.insertAfter(this_tr_last);
            }


            //this_.attr('data-level', d.level);

            /*for(b = 0; b <= d.btns.length; b++) {
                var bid = d.btns[b].id;
                var blvl = d.btns[b].level;
                $('.btn_' +bid, document).attr('data-level', blvl);
            }*/

            this_tbl.find('tr').each(function () {
                $(this).find('button').each(function () {
                    $(this).removeClass('hidden');
                });
                if ($(this).is(this_tbl.find('tr').eq(1))) {
                    $(this).find('#move_up').addClass('hidden');
                    $(this).find('#move_top').addClass('hidden');
                } else if ($(this).is(this_tbl.find('tr:last'))) {
                    $(this).find('#move_down').addClass('hidden');
                    $(this).find('#move_bottom').addClass('hidden');
                }
            });
        }).fail(function() {
            alert('PHP ERROR!');
        });
    };

    /*var update_trn_flow_level = function (flowid,action,moduleid,stageLvl) {
        var qry = false;
        var newlevel = false;
        $.ajax({
            url: base_url + 'settings/updatetrnflowlevel',
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            data: {
                flowid: flowid,
                action: action,
                moduleid: moduleid,
                stagelvl: stageLvl
            }
        }).done(function (d) {
            qry = d.qry;
            newlevel = d.newlevel;
        }).fail(function () {

        });

        var values = {
            qry: qry,
            newlevel: newlevel
        };

        return values
    };*/


    return{
        init:function () {
            inittrnmainflow();
        },
        stages: function (el,id) {
            init_trn_flow_stages(el,id);
        }
    }
}();