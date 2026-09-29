/* 
 * AUTHOR: Lucky John Faderon
 * DATE: 03/31/2017
 * For User's activities and statistics
 */

var ACTIVITIES = function () {
    PECO.getHighlightsPlugin();
    var activity_filter = $('#activity_filter');
    var activity_trnlist = $('#trn-list');

    var init_act = function () {
        $.container = $('#dashboardview-container');
        $('#dashboardview-menu').on('click', 'a', function (e) {
            e.preventDefault();
            $.this_ = $(this);
            $('#dashboardview-menu li').removeClass('active');
            $.this_.closest('li').addClass('active');
            labels = {title: $.this_.text(), desc: ''};
            // PECO.ajaxContentLoad($.this_, $.container);

            init_loader($.this_, $.container);
        });

        // LOAD DEFAULT CONTENT
        $.default_cont = $('#dashboardview-menu li.active a');
        // PECO.ajaxContentLoad($.default_cont, $.container);
        init_loader($.default_cont, $.container);
    };

    var init_loader = function(object, container) {
        $.ajax({
            url: object.attr('href').replace('#', ''),
            type: 'post',
            beforeSend: function () {
                $('.page-breadcrumb li#ajax-breadcrumb').remove();
                container.html('<h4><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Loading content, please wait...</h4>');
                $('.page-breadcrumb').append('<li id="ajax-breadcrumb"><i class="fa fa-angle-right"></i> <a href="javascript:;"><b class="">Loading...</b></a></li>');
            }
        }).done(function (data) {
            container.html(data);
            $('.page-breadcrumb li#ajax-breadcrumb').remove();
            $('.page-breadcrumb').append('<li id="ajax-breadcrumb"><i class="fa fa-angle-right"></i> <a href="javascript:;"><b class="">' + object.text() + '</b></a></li>');
            PECO.initNicescroll();
            $.getScript( PECO.base_url() + "assets/pages/dashboard.js", function( data, textStatus, jqxhr ) {
                console.log( data ); // Data returned
                console.log( textStatus ); // Success
                console.log( jqxhr.status ); // 200
                console.log( "Load was performed." );
            });
        }).fail(function () {
            container.html('<h4 class="text-danger"><i class="fa fa-times fa-fw"></i> Fail to load content</h4>');
            PECO.initAlerts('Fail to load HTML content from <strong>' + object.attr('href').replace('#', '') + '</strong>', 'ERROR URL', 'error');
        });
    };

    var init_user = function () {
        loadTRNSummary();
        loadTRN();

        activity_filter.change(function (e) {
            e.preventDefault();
            var this_ = $(this);
            loadTRN(this_.val());
        });

        activity_trnlist.on('click', '#btn-expand', function () {
            console.log('details clicked!');
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            if (this_.hasClass('expanded') == false) {
                $.ajax({
                    url: PECO.base_url() + 'user/gettrndetails',
                    type: 'post',
                    dataType: 'json',
                    data: {'id': this_.attr('data-id')},
                    beforeSend: function () {
                        thisTr.after('<tr id="loading"><td colspan="' + thisTr_child + '" >Loading..</td></tr>');
                    }
                }).done(function (data) {
                    if (data.qry == true) {

                        var data_details = '';
                        data_details += '<table class="table table-hover table-striped tbl-sm sub">';
                        data_details += '<thead><th></th><th>Transaction Name</th><th>Submited By</th><th>Date Submited</th><th width="30px"><i class="fa fa-comment"></i></th></thead>';
                        data_details += '<tbody>';
                        var apt_subs = data.data.length;
                        for (t = 0; t < apt_subs; t++) {
                            //var num_min = Number(apt_subs) - t;
                            var tt = (t + 1);
                            data_details += '<tr class="withsub">';
                            data_details += '<td width="20px">' + tt + '</td>';
                            data_details += '<td>' + data.data[t].lastupd + '</td>';
                            data_details += '<td>' + data.data[t].createdby + '</td>';
                            data_details += '<td>' + data.data[t].date + '</td>';
                            data_details += '<td>' + data.data[t].comm + '</td>';
                        }
                        data_details += '</tbody>';
                        data_details += '</table>';
                        thisTr.after('<tr class="animated fadeIn fast compact" id="details"><td colspan="' + thisTr_child + '" style="padding: 2px 0px !important; padding-left: 20px !important;"><div class="col-md-8">' + data_details + '</div>' +
                                '<div class="col-md-4"><div class="portlet light margin-top-10">' +
                                '<div class="portlet-title"><div class="caption"> <i class="fa fa-edit"></i> <span class="caption-subject font-green-sharp bold uppercase">Summary</span> <span class="caption-helper">over all sammary report</span> </div></div>' +
                                '<div class="portlet-body">' +
                                '<ul class="list-group summary column no-border">' +
                                '<li class="list-group-item"><div class="row"><span class=" label-name col-md-4">Total Routes </span><span class="label label-default col-md-8 pull-right"><span id="name">' + data.totalroutes + '</span></span></div></li>' +
                                '<li class="list-group-item"><div class="row"><span class=" label-name col-md-4">Total Time Spent </span><span class="label label-default col-md-8 pull-right"><span id="name">' + data.timespent + '</span></span></div></li>' +
                                '<li class="list-group-item"><div class="row"><span class=" label-name col-md-4">Total Comments </span><span class="label label-default col-md-8 pull-right"><span id="name">' + data.totalcomm + '</span></span></div></li>' +
                                '</ul>' +
                                '</div></div>' +
                                '</div>' +
                                '</td></tr>');



                    } else {
                        thisTr.after('<tr class="animated fadeIn fast compact"  id="details"><td colspan="' + thisTr_child + '"><i class="fa fa-warning text-warning"></i> No Record Found!</td></tr>');
                    }

                }).fail(function () {
                    thisTr.after('<tr class="animated fadeIn fast compact"  id="details"><td colspan="' + thisTr_child + '"><i class="fa fa-warning text-warning"></i> No Record Found!</td></tr>');
                });
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                thisTr.next('#loading').remove();

            } else {

                thisTr.next('#details').remove();
                thisTr.next('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');
        });

        activity_trnlist.on('click', '.accordion-toggle', function (a) {
            a.preventDefault();
            var a = $(this);
            $(a.attr('href')).toggleClass('open');
            if ($(a.attr('href')).hasClass('open')) {
                a.find('i.pull').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            } else {
                a.find('i.pull').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
        });


        $('.tiles').on('click', '.tile', function (e) {
            $(this).toggleClass('selected');
        });
    };
    var loadTRNSummary = function () {
        $.ajax({
            url: PECO.base_url() + 'user/inittrnsummary/',
            type: 'POST',
            dataType: 'json',
        }).done(function (data) {
            activity_filter.select2({
                placeholder: 'Filter Activities..',
                allowClear: true,
                formatResult: PECO.formatState,
                formatSelection: PECO.formatDataSelection,
                data: data.actsumary
            });
        });
    };
    var loadTRN = function (origid) {
        var origid = (origid) ? origid : false;
        $.ajax({
            url: PECO.base_url() + 'user/inittrn',
            type: 'POST',
            dataType: 'json',
            data: {'origid': origid},
        }).done(function (data) {
            //if(data.qry==true) {
            PECO.getHighlightsPlugin();
            var table = $('#trn-list');
            table.dataTable().empty();
            table.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.data,
                aoColumns: [
                    {data: 'expand', sWidth: '10px', sClass: 'text-align-center'},
                    {data: 'num'},
                    {data: 'lastupd'},
                    {data: 'details', sClass: 'trn'},
                    {data: 'trn'},
                    {data: 'control', sClass: 'controls', sWidth: '100px'}
                ],
                searchHighlight: true,
                "order": [[2, "desc"]],
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
                },
                searchHighlight: true,
            });
            //}
        });

    };

    return {
        init: function () {
            init_act();
        },
        user: function () {
            init_user();
        }
    }
}(jQuery);