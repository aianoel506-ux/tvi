<style>
    #tbl_requirements_list_filter {
        display: inline-block !important;
        width: 150px;
    }
    #tbl_requirements_list_filter input{
        width: 100% !important;
    }
</style>
<?php
$check_contract = $this->model_cad->check_contract($dataid);

$requirements = '';

$online = $this->db->select()->from('application_customers_online_ticket_ref')
    ->where(array('appid' => $dataid, 'status' => 1))->get()->row();

$trans = $this->db->select('trmt.*,	tfms.`desc`')
    ->from('transaction_request_main_trails as trmt')
    ->join('prime_transaction_flow_main_stages as tfms','trmt.stageid = tfms.sysid','left')
    ->where('trmt.dataid',$dataid)
    ->order_by('trmt.datecreated','DESC')
    ->get()->row();
?>

<div class="tab-pane fade in <?php ($task_flow == false) ? 'active' : ''; ?>" id="data">
    <div class="row">
        <div class="col-md-7">
            <?php customer_application_basicinfo($dataid, true);  ?>
        </div>
        <div class="col-md-5">
            <?php echo customer_application_view_right($dataid); ?>
            <?php echo customer_application_requirements_list($dataid); ?>
        </div>
    </div>
</div>

<div class="modal fade draggable-modal" id="assignfilemodallist" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog" style="width: 900px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Assign File Requirements List</h4>
            </div>
            <div class="modal-body table">
                <div class="row">
                    <div id="divfiles" class="well col-md-12" style="height: 300px;overflow-y: scroll;background-color: white;">
                        <div class="row" style="border: 1px dashed lightslategray;margin: 2px !important;height: 150px;">
                            <?php
                            $dirname = base_url()."pictures";
                            $images = glob($dirname."*.*");

                            foreach($images as $image) {
                                echo '<img src="'.$image.'" /><br />';
                            }

                            ?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-5">
                        <span id="requirementtitle" style="color: #ff8718;font-weight: bold;font-size: 14px !important;">My Title</span>
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-2" style="padding: 0px !important;">
                                <label for="usr">File:</label>
                            </div>
                            <div class="col-md-10" style="padding-left: 0px !important;">
                                <input type="text" class="form-control" id="usr">
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>

<!--
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
-->

<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/maps/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
        $("a.iframe").fancybox({
            'width': 640, // or whatever you want
            'height': 480, // or whatever you want
            'type': 'iframe'
        });
    });

    CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid;?>);
</script>
