
<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/21/2018
 * Time: 4:10 PM
 */

?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" />

<style>
    .loading-container {
        position: fixed;
        left: 0px;
        right: 0px;
        top: 45%;
        width: 100%;
        height: 10%;
        padding: 30px auto;
        background: rgba(255,255,255,0.50);
        display: inline-block;
        text-align:center;
    }
    #btn_filters button {
        margin-bottom: 5px;
    }
    #btn_filters button.active {
        border-bottom: 5px solid #000;
        margin-bottom: 0px;
    }
</style>

<div style="margin: 20px 20px; overflow: scroll; height: 100%;" class="scroller">

    <input type="hidden" name="repsource" value="391" />
    <div class="row">
        <span class="pull-left col-md-7" id="" >
            <button class="btn btn-default btn-sm" id="btn_refresh_list"><i class="fa fa-refresh"></i> Refresh Table</button>
            <span class="btn-group margin-left-20" id="btn_filters" style="margin-top: 5px;">
                <button type="button" data-id="0" class="btn btn-default btn-sm"><i class="fa fa-tag fa-fw"></i> All</button>

                <?php
                $query_status = $this->db->select('tp.sysid, tp.names, tp.desc, tp.colorbg, tp.colortxt')
                    ->from('prime_types_parameter AS tp')
                    ->join('ticketing_status_specs_matrix AS tssm', 'tp.sysid = tssm.typesid')
                    ->where('tssm.codes', 'TS')
                    ->get();
                if($query_status->num_rows()>0) {
                    foreach ($query_status->result() as $srow) {
                        if (!ts_status_pending($srow->sysid) || $srow->sysid==300) {
                            echo '<button ';
                            $class = '';
                            if($srow->sysid==300) {
                                $class=' active';
                            }
                            echo ' type="button" data-id="' . $srow->sysid . '" class="btn btn-sm '.$class.'" style="background: ' . $srow->colorbg . '; color: ' . $srow->colortxt . '"><i class="fa fa-tag fa-fw"></i> ' . $srow->desc . '</button>';
                        }
                    }
                }
                ?>
            </span>

        </span>
        <div class="col-md-5">
            <div class="btn-group pull-right">
                <a class="btn btn-primary" href="#form_tc_entry" data-toggle="ajax-modal" data-view="391"><i class="icon-flag"></i> New Trouble Call</a>
                <a class="btn btn-default" href="#form_user_changepass" data-toggle="ajax-modal" data-view="391"><i class="icon-flag"></i> Change Password</a>
                <a class="btn btn-danger" id="btn-logout" data-module="0" data-segs="admin/dashboard/" data-method="post" title="Logout Account" href="#<?php echo base_url('auth/logout'); ?>">
                    <i class="fa fa-sign-out"></i> Log Out </a>
            </div>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="portlet light">
            <div class="portlet-body">
                <div class="col-md-8">
                    <div class="input-group pull-left" style="margin-left: -15px;">

                        <span class="input-group-addon">
                        List Limit
                        </span>
                        <input id="list_limit" class="form-control" value="50" placeholder="Limit View.." />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" id="btn_list_limit">Go</button>
                        </span>

                        <span class="input-group-addon">
                            Fast Search
                        </span>
                        <input style="width: 50%; display: inline-block;" id="search_name" class="form-control search-submit" value="" placeholder="Name" />
                        <input style="width: 50%; display: inline-block;" id="search_addr" class="form-control search-submit" value="" placeholder="Address" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" id="btn_search"><i class="fa fa-search"></i></button>
                       </span>

                        <span class="input-group-addon" style="background: #fff;">
                            <label for="icheckdatefilter" style="margin: 0px 0px;">
                            <input class="checkbox icheck" id="icheckdatefilter"  type="checkbox" value="1" />
                                <i class="fa fa-calendar"></i> Filter
                            </label>
                        </span>
                        <input class="form-control filter-dates disabled-submit" id="filteryear" style="width: 34%;" placeholder="Year" value="<?php echo date('Y'); ?>" />
                        <input class="form-control filter-dates disabled-submit" id="filtermonth" style="width: 33%;" placeholder="Month" />
                        <input class="form-control filter-dates disabled-submit" id="filterday" style="width: 33%;" placeholder="Day" />

                    </div>
                </div>

                <table width="100%" class="table table-hover table-condensed table-striped table-bordered tbl-sm table-resizable table-wrap" id="tbl_ticket_list" style="width: 100%">
                    <thead>
                    <th></th>
                    <th><i class="fa fa-reorder"></i></th>
                    <th>Q</th>
                    <th>TC No.</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Time Lapsed</th>
                    <th>Reports</th>
                    <th>Team</th>
                    <th>Equipment</th>
                    <th>Findings</th>
                    <th>Circuit Lvl</th>
                    <th>ETC</th>
                    <th>Status</th>
                    <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="loading" class="loading-container">
    <div class="loading-bar">
        <h3><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Loading....</h3>
    </div>
</div>

<div class="modal fade bs-modal-lg"  id="modal_ajax" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> <span id="modal_title"></span></h4>
            </div>
            <div class="modal-content" id="modal_content">
                <i class="fa fa-spinner fa-spin fa-pulse"></i>  Loading content...
            </div>
        </div>
    </div>
</div>
<div id="ps_overlay" class="ps_overlay" style="display:none;"></div>
<a id="ps_close" class="ps_close" style="display:none;"></a>
<div id="ps_container" class="ps_container" style="display:none;">
</div>


<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>

<script src="<?php echo base_url(); ?>assets/pages/tsmenu/main.js"></script>

<script>
    TS.list();
</script>


