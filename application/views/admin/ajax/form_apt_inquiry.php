<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/22/2018
 * Time: 4:35 PM
 */


?>

<div class="modal-body">
    <div class="form-group">
        <div class="input-icon input-group-lg ">
            <i class="fa fa-search font-red-flamingo" style="margin-top: 5px;"></i>
            <input class="form-control border-yellow-casablanca" placeholder="Search: Applicatoin Number / Service No. / Name" name="searchtxt" id="input_app_search" />
        </div>
    </div>
</div>
<div class="modal-body" style="margin-top: 20px;">
    <div class="row">
        <div class="col-md-12">
            <ul class="list-group summary table column " style="">
                <li class="list-group-item" style="width: 200px;">
                    <span class="col-md-5 label-name">Servno</span>
                    <span class="col-md-7 label-default number" id="apt_inq_servno">None</span>
                </li>
                <li class="list-group-item" style="width: 400px;">
                    <span class="col-md-4 label-name">Name</span>
                    <span class="col-md-8 label-default" id="apt_inq_name">None</span>
                </li>
                <li class="list-group-item">
                    <span class="col-md-4 label-name">District</span>
                    <span class="col-md-8 label-default" id="apt_inq_district">None</span>
                </li>
            </ul>
            <ul class="list-group summary table column " style="">
                <li class="list-group-item" style="width: 200px;">
                    <span class="col-md-5 label-name">ESSR No.</span>
                    <span class="col-md-7 label-default number" id="apt_inq_essrno">None</span>
                </li>
                <li class="list-group-item" style="width: 400px;">
                    <span class="col-md-4 label-name">Address</span>
                    <span class="col-md-8 label-default" id="apt_inq_address">None</span>
                </li>
                <li class="list-group-item">
                    <span class="col-md-4 label-name">Rate</span>
                    <span class="col-md-8 label-default" id="apt_inq_rate">None</span>
                </li>
            </ul>
        </div>
        <div class="col-md-12">
            <h4 class="font-yellow-casablanca text-align-left"><i class="fa fa-reorder"></i> APT</h4>

            <table class="table table-hover table-bordered table-condensed " id="tbl_apt">
                <thead>
                <th>#</th>
                <th>Transaction</th>
                <th>Date</th>
                <th>From</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal-footer" style="text-align: left;">

    <ul class="list-group summary table column no-border" style="">
        <li class="list-group-item">
            <span class="col-md-2 label-name">Last Remarks</span>
            <span class="col-md-10 label-default" id="apt_inq_remarks">None</span>
        </li>
    </ul>
</div>
<script type="text/javascript">
    var tbl_apt = $('#tbl_apt', document);


    var tbl_apt_inq_trn = function(d) {
        tbl_apt.DataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: false,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: d.trns,
            aoColumns: [
                {data: 'num', sWidth: '1%', sClass: ''},
                {data: 'trans', sWidth: '',sClass: 'text-align-left text-primary'},
                {data: 'datecreated', sWidth:'35%', sClass: 'text-align-left'},
                {data: 'createdby', sWidth:'20%', sClass: 'text-align-left'}
            ],
            searchHighlight: true,
            "order": [[2, "desc"]],
            language: {
                "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
            },
            searchHighlight: true,
            bLengthChange: false,
            fnRowCallback: function(nRow, aData, i) {
                // $('.tooltips', nRow).tooltip();;
            }
        });
    };

    $('#tbl_apt', document).DataTable({
        bInfo: false,
        bFilter: false,
        bPaginate: false,
        language: PECO.DTEmptyMessage('No Transaction flow yet!'),
        scrollY: '260px'
    });

    $('#frm_search_apt_stat', document).submit(function (e) {
        e.preventDefault();
    });

    var app_search = $('#input_app_search', document);

    var a = new Bloodhound({
        datumTokenizer: function (e) {
            return e.tokens
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {url: PECO.base_url() + "search/customerapplication?query=%QUERY", wildcard: "%QUERY"}
    });


    a.initialize(), app_search.typeahead(null, {
        hint: false,
        highlight: true,
        minLength: 1,
        displayKey: "appname",
        source: a.ttAdapter(),
        cache: false,
        templates: {
            suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{pic}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b>{{essrno}}</b> - {{appname}}</h5>', "<p>{{addrspec}}</p>", "</div>", "</div>"].join("")),
        },
    }).on('typeahead:selected', function (event, selection) {
        $.ajax({
            url: PECO.base_url() + 'cad/getapplicaitonsubdetails',
            type: 'post',
            data: {id: selection.sysid, mode: 'data'},
            dataType: 'json'
        }).done(function (d) {
            $('#apt_inq_servno', document).text(d.info.servno);
            $('#apt_inq_essrno', document).text(d.info.essrno);
            $('#apt_inq_name', document).text(d.info.appname);
            $('#apt_inq_address', document).text(d.info.address);
            $('#apt_inq_district', document).text(d.info.distname);
            $('#apt_inq_rate', document).text(d.info.rateclass);
            tbl_apt_inq_trn(d);
        });
    }).click(function () {
        PECO.initElScroller($('.tt-dropdown-menu', document));
    });



</script>