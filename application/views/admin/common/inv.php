<?php
$base_url = base_url();
$add_brand_popover = "
                        <form class=\"\" method=\"post\" action=\"{$base_url}asset/addbrand\" id=\"frm_add_brand\">
                            <div class=\"form-group\">
                                <input class=\"form-control\" placeholder=\"Brand Code\" name=\"brandcode\" /><br>
                                <input class=\"form-control\" placeholder=\"Brad Name\" name=\"branddesc\" />
                            </div>
                            <button class=\"btn btn-primary\" type=\"submit\"> Save </button>
                            <button class=\"btn btn-danger\" id=\"cancelpopover\" type=\"button\"> Cancel </button>
                        </form>
                    ";
?>

<div class="row">

    <div class="col-md-8">
        <div class="portlet blue box table">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-table"></i>
                    <span class="caption-subject font-white bold uppercase">Item List</span>
                    <span class="caption-helper"></span>
                </div>
                <ul class="nav nav-tabs asset-table-tab">
                    <li class="active">
                        <a href="#available" data-toggle="tab" data-val="1">
                            <i class="fa fa-check"></i>
                            <span class="caption-subject  bold uppercase">Available</span>
                        </a>
                    </li>
                    <li class>
                        <a href="#issued" data-toggle="tab" data-val="2">
                            <i class="fa fa-tag"></i>
                            <span class="caption-subject  bold uppercase">Issued</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <table class="table table-borderd table-hover table-striped table-bordered" id="tbl_assets">
                    <thead>
                    <th><i class="fa fa-reorder"></i>
                    </th>
                    <th>Asset Code</th>
                    <th>Asset Desc.</th>
                    <th>Ownership</th>
                    <th>Status</th>
                    <th width="60px">Control</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="portlet box blue">
            <div class="portlet-title">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#details" data-toggle="tab">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject  bold uppercase">Details</span>
                            </a>
                        </li>
                        <li class>
                            <a href="#newasset" data-toggle="tab">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject  bold uppercase">New</span>
                            </a>
                        </li>
                    </ul>
            </div>
        <div class="portlet-body">
                <div class="tab-content" style="padding: 20px 20px;">
                    <div class="portlet-body tab-pane fade in active" id="details">
                        <ul class="list-group summary column no-border ">
                            <input type="hidden" id="hiddenid"  />
                            <input type="hidden" id="moduleid" value="<?php echo $origin; ?>"  />
                            <input type="hidden" id="assetid" />
                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Asset Code </span><span class="label label-default col-md-7 pull-right"><span id="assetcode">N/A</span></span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Brand </span><span class="label label-default col-md-7 pull-right"><span id="brand"> N/A</span></span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Amp </span><span class="label label-default col-md-7 pull-right"><span id="amp"> N/A</span></span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Volts </span><span class="label label-default col-md-7 pull-right"><span id="volts"> N/A</span></span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Descriptions </span><span class="label label-default col-md-7 pull-right"><span id="desc"> N/A</span></span>
                                </div>
                            </li>
                        </ul>
                        <hr>

                        <div class="btn-group btn-group btn-group-justified">
                            <a href="javascript:;" class="btn red" id="untag">
                                <i class="fa fa-times fa-fw"></i> Untag </a>

                            <a href="javascript:;" class="btn blue" id="tag">
                                <i class="fa fa-tag fa-fw"></i> Tag </a>

                            <a href="javascript:;" class="btn green" id="renew">
                                <i class="fa fa-refresh fa-fw"></i> Renew </a>

                        </div>
                    </div>
                    <div class="portlet-body tab-pane fade in" id="newasset">
                        <form name="savenewasset" id="save_new_asset" action="<?php echo base_url() ?>assets/savenewasset" method="post">
                            <input type="hidden" name="dataid" id="dataid" data-id="<?php echo $dataid ?>" value="<?php echo $dataid?>">
                        <ul class="list-group summary column no-border">
                          <!--  <li class="list-group-item">
                                <div class="row form-group"><span class=" label-name col-md-5">Asset Type </span>
                                    <span class="label label-default col-md-7 pull-right">

                                    </span>
                                </div>
                            </li> -->
                            <input  value="320"  class="form-control data-entry" id="assettype" name="assettype" placeholder="Asset Type" />

                            <li class="list-group-item hidden" id="meter_no_input">
                                <div class="row"><span class=" label-name col-md-5">Meter Data </span>
                                    <span class="label label-default col-md-7 pull-right">
                                        <label style="width: 100%; display: inline-block; margin-bottom: 5px;">Meter Number:
                                            <input name="meterno" type="text" class="form-control data-entry input-sm" id="meterno" placeholder="Meter Number" value>
                                        </label>
                                        <label style="width: 100%; display: inline-block; margin-bottom: 5px;">Meter Amps:
                                            <input name="amps" type="text" class="form-control data-entry input-sm" id="meteramps" placeholder="Meter Amps" value>
                                        </label>
                                        <label style="width: 100%; display: inline-block; margin-bottom: 5px;">Meter Volts:
                                            <input name="volts" type="text" class="form-control data-entry input-sm" id="metervolts" placeholder="Meter Volts" value>
                                        </label>
                                    </span>
                                </div>
                            </li>

                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Brand </span>
                                    <span class="label label-default col-md-7 pull-right">
                                        <div class="input-group">
                                        <input name="newbrand" type="text" class="form-control data-entry" id="newbrand" placeholder="Brand" value>
                                        <span class="input-group-btn">
                                            <button type="button" id="btn_add_brand" class="btn btn-default popovers"
                                                    data-container="body" onclick=" "
                                                    data-trigger="click"
                                                    data-placement="left"
                                                    data-content='<?php echo $add_brand_popover; ?>'
                                                    data-original-title="Add Brand"><i class="fa fa-plus"></i></button>
                                        </span>
                                        </div>
                                    </span>
                                </div>
                            </li>

                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Serial </span>
                                    <span class="label label-default col-md-7 pull-right">
                                        <input name="serial" type="text" class="form-control data-entry" id="serial" placeholder="Serial" value>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row"><span class=" label-name col-md-5">Descriptions </span>
                                    <span class="label label-default col-md-7 pull-right">
                                        <input name="description" type="text" class="form-control data-entry" id="description" placeholder="Descriptions" value>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class=" btn-group pull-right" style="margin-top: 20px; margin-right: -15px;">
                                    <button type="submit" name="save" class="btn btn-primary" id="save_btn" >
                                        <i class="fa fa-tag fa-fw"></i> Save </button>
                                    <button type="reset" name="reset" class="btn btn-default" id="reset" >
                                        <i class="fa fa-refresh fa-fw"></i> Reset </button>
                                </div>
                            </li>
                        </ul>
                      </form>
                    </div>
                </div>
        </div>
        </div>
    </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>/assets/pages/inventory/assets.js" type="text/javascript"></script>

<script type="text/javascript">
    ASSETS.init();
    //PECO.initAssetSelect('init', <?php echo $dataid; ?>, false);
    PECO.brandSelectTagging($("#newbrand"), true, false);

</script>