<?php
$trn_title = '';
$form_url = '';
$route_name = '';

$qry_stage_info = $this->db->select()->from('prime_transaction_flow_main_stages')
    ->where(array('sysid' => $stageid))
    ->get()->row();
$route_name = ($qry_stage_info) ? $qry_stage_info->desc : 'N/A';
// GET THE LAST FLOW DETAILS	
$trnid = ($this->uri->segment(4)) ? $this->uri->segment(4) : false;


$qry_trl = $this->db->select()->from('transaction_request_main_trails')
    ->where(array('dataid' => $dataid, 'sysid' => $trnid))
    ->get()->row();
$trnmainid = $this->db->select()->from('transaction_request_main')->where(array('sysid' => $qry_trl->trnid))->get()->row();

if($trnmainid) {
    $trn_title = $trnmainid->descs;
}

if ($origin) {

    $flow_stages = task_flows_stages();
    $moduleid = $this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid;

    $flowid = flow_id($origin);

    if ($qry_trl) {
        $createdby = (get_users_info($qry_trl->createdby)) ? get_users_info($qry_trl->createdby)->firstname : 'Unknown';
    } else {
        $createdby = 'Unknown';

    }
} else {
    $check_trn_stat_query_last = $this->db->query("SELECT * FROM transaction_request_main_trails WHERE dataid = $dataid ORDER BY sysid DESC")->row();
    $origin = $check_trn_stat_query_last;
    $createid = $origin->createdby;
    $createdby = (get_users_info($createid)) ? get_users_info($createid)->firstname : 'Unknown';
    $flow_stages = task_flows_stages();
    $flowid = flow_id($origin);
}



$approval_stat = (isset($approval)) ? $approval['approval'] : false;

$approved = false;
$disapproved = false;
$trn_id_approved = 0;

$task_notify = task_notify($flowid, $dataid);
if($task_notify->qry) {
    $task_color = 'red';
    $task_notify_message = $task_notify->msg;
}else{
    $task_color = 'green';
    $task_notify_message = '';
}




$btn_group = '';
$btn_approval_group = '';
$stat_approved = '<h3 class="text-success pull-left"><i class="fa fa-check fa-fw"></i> Approved</h3>';
$stat_disapproved = '<h3 class="text-danger pull-left"><i class="fa fa-times fa-fw"></i> Disapproved</h3>';
$stat_pending = '<h3 class="text-warning pull-left"><i class="fa fa-refresh fa-fw"></i> Pending</h3>';
$btn_disapprove = '<button id="btn-approval" value="88" title="Disapprove" url="' . base_url('query/approval') . '" class="btn btn-danger btn-lg " type="button"><i class="fa fa-times fa-fw"></i> Disapprove</button>';
$btn_send = '<button class="btn btn-primary btn-lg pull-right" type="submit"><i class="fa fa-send fa-fw"></i> Send</button>';

$route_select = false;
if ($approval_stat == 1) {
    // check if approval stage is approved
    $check_approved = $this->db->select()
        ->from('transaction_request_trails_logs')
        ->where(array('trailid' => $trnid, 'activity' => 87))
        ->get()->row();
    if ($check_approved) {
        $btn_approval_group .= $stat_approved;
        $route_select = true;
    }else{
        $check_disapproved = $this->db->select()
            ->from('transaction_request_trails_logs')
            ->where(array('trailid' => $trnid, 'activity' => 88))
            ->get()->row();
        if($check_disapproved) {
            $btn_approval_group .= $stat_disapproved;
        } else {
            $btn_approval_group .= $btn_disapprove;
            $btn_group .= $btn_send;
            $form_url = base_url('query/requestprocess');
            $route_select = true;
        }
    }
}else{
    $check_approved = $this->db->select()
        ->from('transaction_request_trails_logs')
        ->where(array('trailid' => $trnid, 'activity' => 87))
        ->get()->row();
    if(!$check_approved) {
        $btn_group .= $btn_send;
        $form_url = base_url('query/requestprocess');
        $route_select = true;
    }else{
        $btn_approval_group .= $stat_approved;
    }
}

?>
<div class="row margin-top-20">
    <div class="col-md-4">
        <div class="portlet light table">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-history"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Transaction Logs</span>
                    <span class="caption-helper">Transaction logs and route history <br>
                        <?php
                        /*

                          echo '<h1>';
                          echo ($disapproved==true) ? 'Disapproved' : 'Not Disapproved';
                          echo '<br>';
                          echo ($approved==true) ? 'Approved' : 'Not Approved';
                          echo '<br>';
                          echo $trn_id_approved;
                          echo $trnlast;
                          echo '</h1>';
                        */
                        ?>
                    </span>
                </div>
                <div class="tools">
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-condensed table-bordered table-striped table-advanced table-hover" id="trn_logs">
                    <thead>
                    <th>#</th>
                    <th>Descriptions</th>
                    <th></th>

                    </thead>
                    <tbody>
                        <?php

                        $a = $this->model_query->hist_trans($dataid);

                        if ($a->num_rows() > 0) {
                            $num = 1;
                            foreach ($a->result() as $row) {

                                echo '<tr>';
                                echo '<td>' . $num++ . '</td>';
                                echo '<td>' . $row->desc. ' (' .$row->datecreated. ')' . '</td>';
                                echo '<td>';
                                if ($row->status == 1) {
                                    echo '<i class="fa fa-check fa-sm text-success"></i>';
                                } else {
                                    echo '<i clas s="fa fa-times fa-sm text-danger"></i>';
                                };
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">

        <div class="task portlet <?php echo $task_color;?> box">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i>
                    <span class="caption-subject bold uppercase">Task</span>
                    <span class="caption-helper"></span>
                </div>
                <div class="tools">

                    <div class="emphasize-icon">
                        <a href="javascript:;" class="popovers" data-container="body" onclick=" " data-trigger="hover" data-placement="left" data-content="<?php echo $task_notify_message;?>" data-original-title="Attention"><i class="fa fa-warning"></i></a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <form class="" action="<?php echo $form_url; ?>" method="post" id="task-update-frm">
                    <input name="trnid" type="hidden" class="form-control" value="<?php echo $trnmainid->sysid; ?>"/>
                    <input name="flowid" type="hidden" class="form-control" value="<?php echo $flowid; ?>"/>
                    <input name="stageid" type="hidden" class="form-control" value="<?php echo $qry_trl->stageid; ?>"/>
                    <input name="moduleid" type="hidden" class="form-control" value="<?php echo $origin; ?>" />
                    <input name="dataid" type="hidden"class="form-control" value="<?php echo $this->uri->segment(5); ?>" />
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group form-md-line-input has-success">
                                <div class="input-icon right">
                                    <input class="form-control input-lg" value="<?php echo $trn_title ?>" placeholder="Ex: Account Creation - For new client" name="trntitle" />
                                    <label class="input-label">Request Title:</label>
                                    <span class="help-block">Title of the request..</span>
                                    <i class="fa fa-pencil"></i>
                                </div>
                            </div>

                            <hr>  
                            <div class="row margin-top-20">
                                <div class="col-md-4">
                                    <label class="input-label">Status:</label>
                                    <?php
                                    if($route_select == true) {
                                        ?>

                                        <select class="form-control input-lg" name="stats" id="stats">
                                            <option value="0">Draft..</option>
                                            <option value="1">Done</option>
                                            <option value="2">Pending</option>
                                        </select>
                                    <?php } else {
                                        echo '<h4 class="text-danger">N/A</h4>'; }
                                    ?>

                                </div>
                                <div class="col-md-4">
                                    <label class="input-label">Route to: </label>
                                    <?php
                                    if($route_select == true) {
                                    ?>
                                    <select class="form-control input-lg" name="routeto" id="routeto">
                                        <option></option>
                                        <?php
                                        foreach ($flow_stages as $row) {

                                            // GET THE STATUS OF APPROVAL // 

                                            echo '<option ';
                                            if ($qry_trl->stageid == $row->STID) {
                                                echo 'selected="selected"';
                                            }


                                            if ($approved == true) {
                                                if ($row->LEVEL <= $trn_id_approved) {
                                                    $option_att = 'disabled';
                                                } else {
                                                    $option_att = '';
                                                }
                                            } else {
                                                if ($disapproved == true) {
                                                    $option_att = 'disabled';
                                                } else {
                                                    $option_att = '';
                                                }
                                            }
                                            echo ' value="' . $row->STID . '" ' . $option_att . '>' . $row->desc . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <?php } else {
                                        echo '<h4 class="text-primary bold">' . $route_name . '</h4>';
                                    } ?>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input">
                                        <input class="form-control input-lg" disabled value="<?php echo $createdby; ?>" />
                                        <label class="input-label">Created By:</label>
                                        <span class="help-block">Some help goes here...</span>
                                    </div>                        
                                </div>

                            </div>

                            <div class="row margin-top-20">
                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input">
                                        <input class="form-control input-lg" disabled value="<?php echo date('Y-m-d H:s:i') ?>" />
                                        <label class="input-label">Date</label>
                                        <span class="help-block">Some help goes here...</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input">
                                        <input class="form-control input-lg" value="" placeholder="Remarks" name="remarks"/>
                                        <label class="input-label">Remarks:</label>
                                        <span class="help-block">Some help goes here...</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input">
                                        <input class="form-control input-lg" disabled value="<?php echo $trnqry->datecreated; ?>" />
                                        <label class="input-label">Date Submited:</label>
                                        <span class="help-block">Some help goes here...</span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <hr class="margin-top-20">
                            <div class="form-group">

                                <div id="btn-approval-group" class="pull-left">
                                    <?php echo $btn_approval_group; ?>
                                </div>

                                <div id="btn_submit_group" class="pull-right">
                                    <?php echo $btn_group; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>   
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script> 

<script>
    function formatDataSelection(route) {
        if (!route.id) {
            return route.text;
        }
        var $route = $(
                '<span><i class="fa fa-check text-success"></i> ' + route.text + '</span>'
                );
        return $route
    }

    function formatState(route) {
        if (!route.id) {
            return route.text;
        }
        var $route = $(
                '<h4 style="padding: 2px 1px !important; margin: 0px 0px !important;"><i class="fa fa-reply fa-fw text-info"></i> ' + route.text + '</h4>'
                );
        return $route;
    }

    $('#routeto').select2({
        placeholder: 'Select..',
        formatResult: formatState,
        formatSelection: formatDataSelection
    });

    $('#stats').select2({
        formatResult: formatState,
        formatSelection: formatDataSelection
    });


    $('#stats').select2();

    $('#task-update-frm').submit(function (e) {
        var form = $(this);
        e.preventDefault();
        confirm_arr = {title: 'Task Update', 'dataname': 'New Account'};
        PECO.ajaxConfirmForm(form, confirm_arr);
    });

    $('#task-update-frm').on('click', '#btn-approval', function (e) {
        e.preventDefault();
        var this_ = $(this);

        var data_val = this_.val();
        var data_arr = {'approval': data_val, 'dataid': <?php echo $dataid; ?>, 'trnid': <?php echo $trnid; ?>};
        PECO.confirmApproval(this_.attr('url'), data_arr, this_.attr('title'), this_);
    });

    $('#trn_logs').dataTable({
        bDestroy: true,
        bPaginate: true,
        bFilter: false,
        bInfo: false,
        bStateSave: true,
        bLengthChange: true,
        bAutoWidth: false,
        scrollY: '250px',
    }).removeClass('hidden');
    PECO.initDTNicescroller();
</script>
