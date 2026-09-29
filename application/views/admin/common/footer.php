
<?php

$sysmode = $this->db->sysmode;
if($sysmode=='dev') {
    $sysmode_msg = '<a href="javascript:;" class="font-red-thunderbird tooltips" title="System is in Development Database"><i class="fa fa-gears"></i> Development DB</a>';
}else{
    $sysmode_msg = '<a href="javascript:;" class="font-green-meadow tooltips" title="System is in live Database"><i class="fa fa-database"></i> Live DB</a>';
}
?>
<div class="page-footer">

    <div class="page-footer-inner">

        <?php /* ORIG 2015 */ echo date('Y'); ?> &copy; <?php echo SYSTEM_NAME; ?> - Allrights Reserved
    </div>



    <?php
    if(dev_mode())
    {
        $flatform = $this->agent->platform();
        // RETREIVE NAVIGATION INFORMATION
        // -----------------------------------------------------------------------  //
        $qry_nav_file = $this->db->select()->from('prime_module_navigations_main')  //
        ->where('hashcode', $this->uri->segment(2))                         //
        ->get()->row();                                                     //
        // -----------------------------------------------------------------------  //
        // ###############################
        if($this->uri->segment(3)) {
            ?>
            <div class="col-md-7 page-footer-inner">
                <p class="pull-right">
                    Server Flatform: <b class="text-success"><?php echo PHP_OS; ?></b>
                    <?php
                    if($qry_nav_file) {
                        ?>
                        | Page Filename: <b class="text-success"><?php echo $qry_nav_file->pagefile; ?></b>
                        | Module ID: <b class="text-success"><?php echo $qry_nav_file->sysid; ?></b>
                        | Page Title: <b class="text-success"><?php echo $qry_nav_file->name; ?></b>
                    <?php } ?>
                </p>
            </div>
            <?php
        }else{
            if($this->uri->segment(2)) {
                ?>
                <div class="col-md-7 page-footer-inner">
                    <p class="pull-right">
                        Server Flatform: <b class="text-success"><?php echo PHP_OS; ?></b>
                        <?php
                        if($qry_nav_file) {
                            ?>
                            | Page Filename: <b class="text-success"><?php echo $qry_nav_file->pagefile; ?></b>
                            | Module ID: <b class="text-success"><?php echo $qry_nav_file->sysid; ?></b>
                            | Page Title: <b class="text-success"><?php $qry_nav_file->name; ?></b>

                        <?php } ?>
                        <!-- Server: <?php echo $this->db->hostname; ?> -->
                    </p>
                </div>
                <?php
            }
        }
    }
    ?>

    <div class="pull-right text-success" style="margin-right: 30px;">
        <a class="icon-footer icon-footer-offline" href="javascript:;" title="" id="internet_connect">
            <span class="internet-text">Offline</span>
            <i class="fa fa-wifi"></i>&nbsp;|&nbsp;
        </a>

        <!-- Server Disk Space: <span id="diskspace"></span> | -->
        <?php echo $sysmode_msg; ?>

        <?php
        if($this->uri->segment(3) && $qry_nav_file->sysid>0) {
            echo '<a href="javascript:;" data-id="'.$qry_nav_file->sysid.'" data-toggle="shortcut" class="btn btn-success inline btn-xs btn-fw tooltips" title="Add this page to shortcut?" style="margin: 0px 0px; margin-top: -5px; margin-right: -10px;" ><i class="fa fa-plus" ></i ></a >';
        }
        ?>
    </div>
    <div class="scroll-to-top tooltips " data-container="body" data-placement="left" data-html="true" data-original-title="Scroll to top">
        <i  class="fa fa-angle-double-up"></i>
    </div>
</div>


<div class="modal fade"  id="modal_ajax" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> <span id="modal_title"></span></h4>
            </div>
            <div class="modal-content" id="modal_content">
                <h3><i class="fa fa-refresh fa-spin"></i> Loading map...</h3>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-modal-lg"  id="modal_maps" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> <span id="modal_title"></span></h4>
            </div>
            <div class="modal-content" id="modal_content">
                <h3><i class="fa fa-refresh fa-spin"></i> Loading map...</h3>
            </div>
        </div>
    </div>
</div>


<div class="modal fade"  id="modal_transaction" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="frm_trn_process_direct_submit" action="<?php echo base_url('query/requestprocess'); ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title"><i class="fa fa-edit"></i> <span id="modal_title"></span></h4>
                </div>
                <div class="modal-body" id="modal_content">
                    <input id="trn_process_direct_trnid" name="trnid" type="hidden" class="form-control" value=""/>
                    <input id="trn_process_direct_flowid" name="flowid" type="hidden" class="form-control" value=""/>
                    <input id="trn_process_direct_stageid" name="stageid" type="hidden" class="form-control" value=""/>
                    <input id="trn_process_direct_moduleid" name="moduleid" type="hidden" class="form-control" value="" />
                    <input id="trn_process_direct_dataid" name="dataid" type="hidden" class="form-control" value="" />


                    <ul class="list-group summary column no-border" id="trn_details">
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Current Route</span>
                            <span class="col-md-4 font-green bold" id="text_route_curr">N/A</span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Next Route</span>
                            <span class="col-md-4 font-red bold" id="text_route_next">N/A</span>
                        </li>
                    </ul>

                    <div class="form-group">
                        <input id="trn_process_direct_routeto" class="form-control" name="routeto" placeholder="Route to.." />
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="submit"><i class="fa fa-send"></i> Send</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- END FOOTER -->

</div>
<script>
    PECO.session();
</script>