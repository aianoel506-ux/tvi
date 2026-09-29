<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

<style>

    .panel-collapse.table .table.sub{
        border-left: 1px solid rgba(0,0,0,0.10) !important;
        z-index: 1;
    }


    .select2-data-flat .select2-search-choice {
        border: 1px solid transparent !important;
        padding: 0px 0px !important;
    }
    .select2-data-flat .select2-search-choice div { 
        left: 0px !important;
    }
    .select2-data-flat .select2-search-choice-close {
        display: none;
    }

    .select2-data-flat .select2-container.select2-container-active {     
        border-bottom: transparent 1px solid !important; 
    }
    .select2-data-flat .select2-input.select2-default,
    .select2-data-flat .select2-search-field,
    .select2-data-flat .select2-choices{
        border: transparent 1px solid !important;
        padding: 2px 0px !important;
    }
    .select2-data-flat .select2-choices{
        width: 100% !important;
        height: 30px !important;
    }
    .select2-data-flat .select2-search-field, .select2-data-flat .select2-search-field input{
        height: 30px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
        top: -5px !important;
    }

</style>


        <div class="row">
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Trouble Call</span>
                            <span class="caption-helper">data entry <br> as of <?php echo date('F d, Y'); ?></span>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <form class="" action="<?php echo base_url('query/savetroublecall'); ?>" method="post" id="frm_trouble_callentry">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="input-label">
                                        Source:
                                    </label>
                                    <select class="form-control" name="source" id="source">
                                        <option></option>
                                        <?php
                                        $qry = $this->db->select()->from('prime_types_parameter')->where('codes', 'TCML')->get();
                                        foreach ($qry->result() as $row) {
                                            echo '<option value="' . $row->sysid . '">' . $row->names . ' - ' . $row->desc . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="input-label">
                                        Assigned Trouble Shooter:
                                    </label>
                                    <input class="form-control select2-data-flat" name="troubleshooter" id="troubleshooter">
                                </div>
                                <div class="form-group">
                                    <label class="input-label">
                                        Contact No:
                                    </label>
                                    <input class="form-control" name="contact" id="contact" placeholder="Ex: 63 928 3332455">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">
                                                Address Specific:
                                            </label>
                                            <input class="form-control" name="addrspec" id="addrspec" placeholder="Ex: #23 Tabuk Suba">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">
                                                District:
                                            </label>
                                            <select name="addrdist" type="text" class="form-control data-entry select2-data-flat" id="addrdist">
                                                <option></option>
                                                <?php
                                                if (select_district()->num_rows() > 0) {
                                                    foreach (select_district()->result() as $row) {
                                                        $default = '';
                                                        //   if($row->sysid==1){
                                                        //          $default = 'selected="selected"';
                                                        //      }
                                                        echo '<option ' . $default . ' value="' . $row->sysid . '">' . $row->names . '</option>';
                                                    }
                                                } else {
                                                    echo '<option value="0">No District</option>';
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="input-label">Complaints:</label>
                                    <textarea name="complaints" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-actions noborder">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-group pull-right" >
                                            <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Clear</button>
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="portlet light table">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">List</span>
                            <span class="caption-helper">log as of <?php echo date('F d, Y'); ?></span>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <table class="table table-hover table-striped table-bordered table-condensed tbl-sm" id="tbl_list">
                            <thead>
                            <th></th>
                            <th>Source</th>
                            <th>Trouble Shooter</th>
                            <th>Contact No.</th>
                            <th>Specific Address</th>
                            <th>District</th>
                            <th>Complaints</th>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
 
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script> 

<!--Additional Scripts for testing-->

<script>
    PECO.getHighlightsPlugin();
    $('#frm_trouble_callentry').submit(function(e){
        e.preventDefault();
        var msg_arr = {
            'error': {'title': 'Error', 'msg': 'Error PHP file!', 'func': 'error'},
            'warning': {'title': 'Warning', 'msg': 'Query fail!', 'func': 'warning'},
            'success': {'title': 'Success', 'msg': 'Query Success!', 'func': 'success'}
        };
        PECO.ajaxFormSubmit($(this), msg_arr);
    });
    
    var formatDataSelection = function (route) {

        if (!route.id) {
            return route.text;
        }
        var $route = $('<span><i class="fa fa-check text-success"></i> ' + route.text.split('-', 1) + '</span>');
        return $route
    }

    var formatState = function (route) {
        var text_arr = route.text.split('-');
        if (!route.id) {
            return route.text;
        }
        var $route = $(
                '<p><b>' + text_arr[0] + '</b> - ' + text_arr[1] + '</p>'
                );
        return $route;
    }
    $('select#source').select2({
        placeholder: 'Select..',
        allowClear: true,
        formatResult: formatState,
        formatSelection: formatDataSelection
    });

    $('select#addrdist').select2({
        placeholder: 'Select..',
        allowClear: true,
    });


        $('#tbl_list').dataTable().empty();
        $('#tbl_list').dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            scrollY: false,
            bProcessing: true,
            bServerSide: true,
            //"order": [[ 0, "desc" ], [ 1, "asc" ]],
            oLanguage: {
                sProcessing: "Loading table.. <br>"
            },
            ajax: {
                url: '<?php echo base_url("query/troubledatatable/") ?>',
                type: "POST"
            },
            aoColumns: [
                {"data": "counter", sWidth: ''},
                {"data": "source_id", sWidth: ''},
                {"data": "person_id", sWidth: ''},
                {"data": "contact_number", sWidth: ''},
                {"data": "addr_specific", sWidth: ''},
                {"data": "addr_district", sWidth: ''},
                {"data": "complaint", sWidth: ''}

            ],
            columnDefs: [
                {"targets": -1, "orderable": false, "searchable": false},
                {"targets": -2, "orderable": false, "searchable": false}
            ]

        });
        PECO.initDTNicescroller();


    PECO.personSelectTagging($("#troubleshooter"), true, false);
</script>