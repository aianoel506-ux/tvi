<!-- BEGIN PAGE LEVEL STYLES -->


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css">



<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/clockface/css/clockface.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>


<!-- END PAGE LEVEL STYLES -->


<style>
    .form-md-line-input {
        position: relative !important;	
    }
    .form-md-line-input .fileinput .input-group-addon{
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;	
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }
    .form-md-line-input .select2-container{
        margin-bottom: 0px !important;
    }
    .select2-drop{
        margin-top: -15px !important;
    }
    .portlet.table {
        padding: 0px 0px !important;	
    }

    .table-condensed .md-checkbox.checkonly {
        width: 20px !important;	
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
    .table-condensed .md-checkbox.checkonly label {
        width: 20px !important;	
        margin: 0px 0px !important;
        padding: 0px 0px !important;
    }
</style>



        <div class="row">

            <div class="col-md-4">
                <div class="portlet light table">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">For Reading</span>
                            <span class="caption-helper"></span>
                        </div>

                    </div>

                    <div class="portlet-body">
                        <table class="table table-hover table-striped table-condensed table-bordered " id="">
                            <thead>
                            <th>GDLB</th>
                            <th>Cust. No. / Limit</th>
                            <th>Last Schedule</th>
                            </thead>
                            <tbody>
                                <?php
                                $qry_gdlb = $this->db->select('GDLB.limit AS LMT, COUNT(AGDLB.gdlbid) AS ACCTNO')
                                        ->select("CONCAT(GDLB.g, '-', DIST.codes, '-', GDLB.l, '-', GDLB.b) AS GDLBNAME", false)
                                        ->from('gdlb_main AS GDLB')
                                        ->join('customer_accounts_glb AS AGDLB', 'AGDLB.gdlbid = GDLB.sysid')
                                        ->join('address_districts AS DIST', 'DIST.sysid = GDLB.d')
                                        ->group_by('AGDLB.gdlbid, GDLB.limit')
                                        ->get();
                                if ($qry_gdlb->num_rows() > 0) {
                                    foreach ($qry_gdlb->result() as $row) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row->GDLBNAME; ?></td>
                                            <td><?php echo $row->ACCTNO; ?> / <?php echo $row->LMT; ?></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="portlet light table">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Reading Entry</span>
                            <span class="caption-helper"><?php echo date('F d, Y'); ?></span>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title="">
                            </a>
                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                            </a>
                            <a href="javascript:;" class="reload" data-original-title="" title="">
                            </a>
                            <a href="javascript:;" class="fullscreen" data-original-title="" title="">
                            </a>
                            <a href="javascript:;" class="remove" data-original-title="" title="">
                            </a>
                        </div>
                    </div>

                    <div class="table-toolbar">
                        <form role="form" class="form-horizontal" id="frm_assign_sched" action="<?php echo base_url('mrd/assignreadingschedule'); ?>" method="post">	
                            <div class="form-group form-md-line-input col-md-4">
                                <div class="col-md-12">
                                    <label class="text-danger"><i class="fa fa-search"></i> Filter Area (Scheduled)</label>
                                    <input id="district" name="lotbook" type="text" class="form-control  input-sm " placeholder="Disrict">
                                    <input id="moduleid" name="moduleid" type="hidden" value="<?php echo get_stage_start_module(); ?>">
                                </div>

                                <div class="col-md-12">
                                    <input disabled id="lot_book" name="lotbook" type="text" class="form-control  input-sm " placeholder="Lot & Book">

                                </div>
                            </div>
                            <div class="form-group form-md-line-input col-md-3">
                                <div class="col-md-12">
                                    <label>Meter Reader: </label>
                                    <input id="emp_input" name="empid" type="text" class="form-control  input-sm " placeholder="Meter Reader">
                                </div>
                                <div class="col-md-12 margin-top-10"><button type="button" id="btn-search-reading" class="btn btn-default"><i class="fa fa-search"></i> Search</button></div>
                            </div>
                            <div class="form-group form-md-line-input col-md-4">
                                <div class="col-md-12">
                                    <label>Schedule: </label>
                                    <input id="reading_date" name="readdate" type="text" class="form-control input-lg date-picker" placeholder="Reading Date">
                                </div>

                                <div class="col-md-12">
                                    <h4 id="new_sched_code" class="text-danger">000000</h4>                    	
                                </div>
                            </div>
                            <div class="col-md-2 pull-right margin-top-20">
                                <button type="submit" class="btn blue btn-block" id="btn_assign"><i class="fa fa-save" ></i> Assign</button>
                                <button type="button" class="btn btn-default btn-block margin-top-10"><i class="fa fa-print"></i> Schedule</button>

                            </div>
                        </form>
                    </div>

                    <div class="portlet-body ">

                        <hr>
                        <table class="table table-hover table-striped table-condensed table-bordered" id="tbl_reading_hist">
                            <thead>
                            <th>Seq</th>
                            <th>Service #</th>
                            <th>Name</th>
                            <th>Meter #</th>
                            <th>Previous</th>
                            <th>Duedate</th>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>		

                </div>
            </div>

        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->

<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>





<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>


<script>
    $('.date-picker').datepicker({
        // rtl: PECO.isRTL(),
        orientation: "left",
        autoclose: true,
        format: 'yyyy-mm-dd'
    });

    $(function () {


        /*
         $("#reading_date").inputmask("y-m-d", {
         autoUnmask: true
         }); 
         */

        // CHANGE THIS TO FUNCTION THAT WILL REVIEW EACH FORM INSIDE ROWS
        $('#tbl_reading_hist').on('blur', 'tr #readamt, tr #readstat', function (e) {
            e.preventDefault();
            var input = $(this);
            row_validation(input);
        });

        function row_validation(input) {
            var value = input.val();
            var tr = input.closest('tr');
            var stat = tr.find('#readstat');
            if (stat.val() == '' && value != '') {
                stat.closest('td').addClass('danger');
                tr.addClass('has-success');
            } else {
                if ((value != '' || value > 0) && stat.val() != '') {
                    tr.addClass('success');
                } else {
                    tr.removeClass('success');
                }
                stat.closest('td').removeClass('danger');
                if (stat.val() != '' && value != '') {
                    tr.addClass('has-success');
                } else {
                    tr.removeClass('has-success');
                }
            }
        }



        $("#emp_input").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_users",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data, function (index, item) {
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
            // ADD AJAX UPDATE IF APPLICABLE //
            console.log('TYPE: ' + $(this).val());
        });


        $("#district").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/selectdistrict",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data, function (index, item) {
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
            // ADD AJAX UPDATE IF APPLICABLE //
            console.log('TYPE: ' + $(this).val());
            var this_val = $(this).val();
            if (this_val != '') {

                $("#lot_book").select2({
                    //url: base_url+"admin/sample_select2",
                    tags: true,
                    triggerChange: true,
                    allowClear: true,
                    maximumSelectionLength: 3,
                    ajax: {
                        url: base_url + "mrd/getgdlb/" + this_val,
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term) {
                            return {
                                term: term
                            };
                        },
                        results: function (data) {
                            var myResults = [];
                            $.each(data, function (index, item) {
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
                    // ADD AJAX UPDATE IF APPLICABLE //
                    console.log('TYPE: ' + $(this).val());
                });

                $('#lot_book').select2("enable", true).select2("val", '');
            } else {
                $('#lot_book').select2("false", true).select2("val", '');

            }

        });



    });

    $('#frm_assign_sched').submit(function (e) {
        e.preventDefault();
        alert('form submit!');
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json'
        }).done(function (d) {
            PECO.initAlerts(d.msg, 'Assign Schedule', d.func);
        }).fail(function () {
            PECO.phpError();
        });

    });

    $('#tbl_reading_hist').dataTable({
        bDestroy: true,
        bPaginate: true,
        bFilter: true,
        bInfo: true,
        bStateSave: true,
    });

    $('#btn-search-reading').click(function (e) {
        var gdlb_arr = $('#lot_book').select2('val');
        e.preventDefault();
        $.ajax({
            url: PECO.base_url() + 'mrd/getmrdacctlist',
            data: {'gdlbidarr': gdlb_arr},
            dataType: 'json',
            type: 'POST',
        }).done(function (d) {
            $('#tbl_reading_hist').dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.data,
                aoColumns: [
                    {"data": "seq"},
                    {"data": "serviceno"},
                    {"data": "name"},
                    {"data": "meterno"},
                    {"data": "previous"},
                    {"data": "duedate"},
                ],
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                }
            });
        }).fail(function () {
            PECO.phpError();
        });
    });
    PECO.initDTNicescroller();
    /*    $(function(){
     $('#tbl_reading_hist').dataTable().empty();
     $('#tbl_reading_hist').dataTable({
     bDestroy: true,
     bPaginate: false, 
     bFilter: true, 
     bInfo: true,
     bStateSave: true,
     scrollY: '300px',
     bProcessing: true,
     bServerSide: true,
     //"order": [[ 0, "desc" ], [ 1, "asc" ]],
     oLanguage: {
     sProcessing: "Loading table.. <br>"
     },
     ajax: {
     url: '',
     type : "POST",
     data : "html",
     },
     aoColumns: [
     { "data": "sysid", sWidth: ''}, 
     { "data": "origid", sWidth: ''},
     { "data": "trncode", sWidth: ''},
     { "data": "codes", sWidth: ''},
     { "data": "descriptions", sWidth: ''},
     { "data": "input_reading", sWidth: ''},
     { "data": "stagesidform", sWidth: ''},
     { "data": "select_action", sWidth: ''},
     { "data": "get_status", sWidth: ''}
     ],
     columnDefs: [ 
     { "targets": 6, "orderable": false, "searchable": false },
     { "targets": 7, "orderable": false, "searchable": false },
     { "targets": 8, "orderable": false, "searchable": false }
     ]
     });
     });*/
</script>
