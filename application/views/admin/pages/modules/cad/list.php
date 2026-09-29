<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" />

<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
<link href="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>

<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>

<?php
$level_default = '';
if(super_admin() == false) {
    $app_flow_ids_arr = flow_id_arr('APPLICATIONS');
    $role_main_id = array();
    $role_main = get_users_info_roles(user_id());
    if ($role_main && count($role_main) > 0) {
        foreach ($role_main as $rrow) {
            if ($rrow->type == 1) {
                $role_main_id[] = $rrow->roleid;
            }

            $role_main_id_imp = implode(',', $role_main_id);
            $app_flow_ids_imp = implode(',', $app_flow_ids_arr);

        }
        $where = '';
        if(trim($role_main_id_imp) != '') {
            $where .= " AND so.ownergroup IN ($role_main_id_imp) ";
        }
        if(trim($app_flow_ids_imp) != '') {
            $where .= " AND ms.flowid IN ($app_flow_ids_imp) ";
        }
        $qry_flow_level = $this->db->query("
            SELECT ms.levels FROM 
            prime_transaction_flow_main_stages_owners AS so
            INNER JOIN prime_transaction_flow_main_stages AS ms ON ms.sysid = so.levelid
            WHERE so.`status` = 1 
            $where
        ")->row();
        $level_default = ($qry_flow_level) ? $qry_flow_level->levels : '';
    }
}

?>

<div class="row">
    <div class="col-md-12">

        <div class="col-md-6 pull-left">
            <input value="<?php echo $level_default;?>" id="select2routes" class="form-control" style="margin-left: -15px; width: 50%;" placeholder="Select Route.. " />
        </div>

        <table style="width: 100%;" id="cad_trn_list" class="table table-hover table-striped table-condensed table-bordered no-footer tbl-sm" >
            <thead>
                <th></th>
                <th>ESSR</th>
                <th>Date Encoded</th>
                <th>Date Updated</th>
                <th>Grid</th>
                <th>Data</th>
                <th>From</th>
                <th>Transaction</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>View</th>
            </thead>
            <tbody>

            </tbody>
        </table>

        <hr>
        <!--
        <button class="btn btn-danger" id="btn_import_applications_legacy"><i class="fa fa-download"></i> Import Legacy</button>
        -->
        <hr>
        <hr>
    </div>
</div>


<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>



<script src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
<script src="<?php echo base_url() ;?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js"></script>



<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/portfolio.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/form-fileupload.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/dashboard.js"></script>
<script type="text/javascript">
    CAD.init();
</script>