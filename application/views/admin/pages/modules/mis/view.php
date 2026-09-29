<?php
$qry = $this->db->query("
                SELECT labels, serials FROM assets_main
                WHERE sysid = $dataid
           ")->row();

if($qry) {

    $info = get_asset_info($dataid);
    $asset_code = $info->number . '-' . $info->serial;
    $asset_spec = $info->specs;
    $asset_issued_date = $info->dateissued;
    $asset_issued_by = $info->issuedby;
    $asset_issued_status =  check_asset_status($dataid)->status_text;
    $asset_typesid =  check_asset_status($dataid)->status_id;
    $brand = $info->brand;
    $types = $info->types;

    if(!empty($info->assets_spec_data)){
        $asses_spec = $info->assets_spec_data;
    }


    ?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/fixedcolumn/css/fixedColumns.bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/pages/assets/main.css"/>

    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css"/>


    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                                <span class="caption-helper">General</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="asset-pic " style="text-align: center; width: 100%;">
                                        <?php
                                        echo get_asset_pic($dataid);
                                        ?>
                                    </div>
                                    <div style="text-align: center">
                                        <a href="javascript:;" class="btn btn-default">View All</a>
                                        <a href="#frm_upload_asset_pics" title="Add Asset Picture" data-arr="<?php echo $dataid; ?>" data-toggle="ajax-modal" class="btn btn-default">Add Pic</a>
                                    </div>
                                    <hr>

                                    <div class="legend-info legend-info-small">

                                        <?php
                                        // echo check_asset_status($dataid)->status_text;

                                        $getowner = $this->db->select("ownerid,ownertype")->from("assets_main_owner_history")
                                            ->where(array("assetid" => $dataid , "status" => 1))
                                            ->order_by("sysid" , "desc")
                                            ->limit(1)
                                            ->get()->row();
                                        if($getowner){
                                            if($getowner->ownertype == 3){
                                                $getaccountdetails = $this->db->select("")
                                                    ->from("customer_accounts_main")
                                                    ->where(array("sysid" => $getowner->ownerid))
                                                    ->get()->row();
                                                if($getaccountdetails){
                                                    if($getaccountdetails->types == 5){
                                                        $legacyid = $getaccountdetails->ownerid;
                                                        //GET TO THE CUSTOMER LEGACY
                                                        $getlegacyname = $this->db->select("name")->from("customer_accounts_name_legacy")
                                                            ->where(array("sysid" => $legacyid))
                                                            ->get()->row();
                                                        if($getlegacyname){



                                                            $info = get_active_account_info($getaccountdetails->sysid);
                                                            ?>
                                                            <legend>
                                                                Name
                                                                <strong class="pull-right"><?php echo $getlegacyname->name; ?></strong>
                                                            </legend>  <legend>
                                                                Service No.
                                                                <strong class="pull-right"><?php echo $info->servicenumber ?></strong>
                                                            </legend>

                                                            <legend>
                                                                Meter No.
                                                                <strong class="pull-right"><?php echo $info->mtrno ?> </strong>
                                                            </legend>

                                                            <legend>
                                                                Serial No.
                                                                <strong class="pull-right"><?php echo $info->mtrserial ?> </strong>
                                                            </legend>

                                                            <legend>
                                                                G-D-L-B
                                                                <strong class="pull-right"><?php echo get_gdlb_name($info->gdlb) ?> </strong>
                                                            </legend>

                                                            <legend>
                                                                Date Contract
                                                                <strong class="pull-right"><?php echo $info->datecontract ?></strong>
                                                            </legend>

                                                            <legend>
                                                                Date Connected
                                                                <strong class="pull-right"><?php echo $info->dateconnected ?></strong>
                                                            </legend>

                                                            <legend>
                                                                Specific Address
                                                                <strong class="pull-right"><?php echo $info->address ?></strong>
                                                            </legend>
                                                            <?php
                                                        }
                                                    }else{

                                                    }
                                                }
                                            }else{
                                                //@TODO create owner for another asset owner type...
                                            }

                                        }else{
                                            echo '<span class="label label-success">Available</span>';

                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-7 legend-info">
                                    <legend>Asset Number: <strong class="pull-right"><?php echo (isset($info->number)) ? $info->number : 'N/A'; ?></strong></legend>
                                    <legend>Serial Number:<strong  class="pull-right"><?php echo (isset($info->serial)) ? $info->serial : 'N/A'; ?></strong></legend>
                                    <?php
                                        if($asset_typesid == 3205){
                                    ?>
                                            <legend>Brand:  <strong  class="pull-right "><?php echo $brand; ?></strong></legend>
                                    <?php
                                            if(!empty($asses_spec)){
                                                foreach ($asses_spec as $sid => $rowspec) {
                                                    echo '<legend>'.$rowspec['spec'].':<strong  class="pull-right">  '.$rowspec['val'].'</strong> </legend>';
                                                }
                                            }
                                        }else{
                                    ?>
                                            <legend>Brand:  <strong  class="pull-right "><a  href="javascript:;" id="brand" data-type="select2" data-value="<?php echo $brand; ?>" data-pk="<?php echo $dataid; ?>" data-original-title="Brand" class="editable editable-click" style="display: inline;">  <?php echo $brand; ?></a></strong></legend>
                                            <?php
                                            if(!empty($asses_spec)){
                                                foreach ($asses_spec as $sid => $rowspec) {
                                                    echo '<legend>'.$rowspec['spec'].':<strong  class="pull-right"> <a href="javascript:;" id="'.$rowspec['typesid'].'" data-type="text" data-value="'.$rowspec['val'].'" data-pk="'.$dataid.'" data-original-title="'.$rowspec['spec'].'" class="editable editable-click" style="display: inline;"> '.$rowspec['val'].'</a></strong> </legend>';
                                                }
                                            }
                                        }
                                    ?>
                                    <legend>Date Modified: <strong  class="pull-right"><?php echo (isset($info->dateissued)) ? $info->dateissued : 'N/A';  ?></strong></legend>
                                    <legend>Status: <strong class="pull-right"><?php echo $asset_issued_status; ?></strong></legend>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light table">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Logs</span>
                                <span class="caption-helper">list</span>
                            </div>

                        </div>
                        <div class="portlet-body">

                            <form id="submitremarks" action="<?php echo base_url() ?>assets/submitremarks" method="post">
                                <input type="hidden" name="remarksdataid" value="<?php echo $dataid; ?>" />
                                <div class="form-group">
                                    <label>Type</label>
                                    <input type="text" id="remarkstype" name="remarkstype" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="comment">Remarks</label>
                                    <textarea name="remarkstxt" placeholder="Enter remarks here ..." class="form-control" rows="3" id="comment"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary pull-right">Save</button>
                                </div>
                            </form>
                            <table class="table table-bordered table-condensed" id="remarkstable">
                                <thead>

                                    <th>Type</th>
                                    <th>Remarks</th>
                                    <th>Date Created</th>

                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>


    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/pages/hris/form-editable.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
    <script src="<?php echo base_url() ?>assets/pages/assets/mtr.js"></script>

    <script>
        MTR.viewasset(<?php echo $dataid; ?>);
    </script>

    <?php
} else {
    page_data_notfound('Meter not found!');
}
?>