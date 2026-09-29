<?php
$firstname = $this->model_query->get_owner_info($dataid)->FIRSTNAME;
echo $dataid;
?>

<div class="tab-pane fade in" id="data">
    <div class="row">
        <div class="col-md-6">

                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Information</span>
                            <span class="caption-helper">General</span>
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
                    <div class="portlet-body">
                        <div class="row">
                                <div class="col-md-3">
                                    <?php if($this->model_query->get_owner_info($dataid)->TYPES==1) { ?>
                                        <img class="img img-bordered" src="<?php echo get_users_pic_url($this->model_query->get_owner_info($dataid)->OWNERSYSID, true); ?>" width="100%" height="100%" />
                                    <?php } else { ?>
                                        <img class="img" src="<?php echo base_url(); ?>uploads/corporation/<?php echo $this->model_query->get_owner_info($dataid)->OWNERSYSID; ?>/primary.jpg" width="100%" height="100%" />
                                    <?php } ?>
                                </div>
                                <div class="col-md-9">

                                    <ul class="list-group summary column no-border">
                                        <?php if($this->model_query->get_owner_info($dataid)->TYPES==1) { ?>
                                        <li class="list-group-item"><div class="row"><h3><span class=" label-name col-md-3">Name </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->FIRSTNAME; ?> <?php echo $this->model_query->get_owner_info($dataid)->MIDDLENAME; ?> <?php echo $this->model_query->get_owner_info($dataid)->LASTNAME; ?></span></span></div></h3></li>
                                        <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Contact Number </span><span class="label label-default col-md-9 pull-right"><span id="name">+639284450000</span></span></div></li>
                                        <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Gender </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo gender($this->model_query->get_owner_info($dataid)->GENDER); ?></span></span></div></li>
                                        <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Birthday </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->BIRTHDAY; ?></span></span></div></li>
                                        <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Civil Status </span><span class="label label-default col-md-9 pull-right"><span id="name">Single</span></span></div></li>
                                        <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Address </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php
                                                        echo $this->model_query->get_owner_info($dataid)->STREET . ', ';
                                                        echo get_district_name($this->model_query->get_owner_info($dataid)->DIST) . ', ';
                                                        echo get_city_name($this->model_query->get_owner_info($dataid)->CITY);
                                                        ?></span></span></div></li>
                                        <?php  } else { ?>
                                        <li class="list-group-item"><div class="row"><h3><span class=" label-name col-md-3">Corp. Name </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->CORPNAME; ?> - <?php echo $this->model_query->get_owner_info($dataid)->CORPDESC; ?></span></span></div></h3></li>
                                        <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Contact Number </span><span class="label label-default col-md-9 pull-right"><span id="name">+639284450000</span></span></div></li>
                                        <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Address </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php
                                                        echo $this->model_query->get_owner_info($dataid)->STREET . ', ';
                                                        echo get_district_name($this->model_query->get_owner_info($dataid)->DIST) . ', ';
                                                        echo get_city_name($this->model_query->get_owner_info($dataid)->CITY);
                                                        ?></span></span></div></li>
                                        <li class="list-group-item"><hr style="margin: 3px 0px;"></li>
                                        <li class="list-group-item"><div class="row"><h4 style="margin: 0px 0px;"><span class=" label-name col-md-3">Representative </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->LASTNAME; ?>, <?php echo $this->model_query->get_owner_info($dataid)->FIRSTNAME; ?></span></span></div></h4></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                    </div>

        </div>
            
            <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="portlet light ">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-edit"></i>
                                        <span class="caption-subject font-green-sharp bold uppercase">Subscription Location</span>
                                        <span class="caption-helper">mapping and specific geodata</span>
                                    </div>

                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-3"><h5 class="text-info"><i class="fa fa-map-o fa-fw"></i> <b>Default Map</b></h5><img src="<?php echo base_url(); ?>assets/global/img/samplemap.gif" width="100%" height="200px"/>
                                            <span style="font-size: 10px">X/Y<code class="pull-right"><?php echo number_format($this->model_query->get_owner_info($dataid)->LAT, 7); ?> / <?php echo number_format($this->model_query->get_owner_info($dataid)->LON, 7); ?></code></span>
                                        </div>


                                        <div class="col-md-9">


                                            <h5 class="text-info"><i class="fa fa-map-marker fa-fw"></i> <b>Location Details</b></h5>
                                            <ul class="list-group summary column no-border">

                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Landmark </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php
                                                        echo $this->model_query->get_owner_info($dataid)->STREET . ', ';
                                                        echo get_district_name($this->model_query->get_owner_info($dataid)->DIST); ?>
                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">House / Gate No </span><span class="label label-default col-md-9 pull-right"><span id="name">223</span></span></div></li>
                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Brgy / Street Name </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->STREET; ?></span></span></div></li>
                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">District </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo get_district_name($this->model_query->get_owner_info($dataid)->DIST); ?></span></span></div></li>
                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Lot & Book </span><span class="label label-default col-md-9 pull-right"><span id="name">01-01</span></span></div></li>
                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Map Updated </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo $this->model_query->get_owner_info($dataid)->GEODATE; ?></span></span></div></li>
                                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-3">Updated By </span><span class="label label-default col-md-9 pull-right"><span id="name"><?php echo get_users_info($this->model_query->get_owner_info($dataid)->GEOUSER)->lastname.', '.get_users_info($this->model_query->get_owner_info($dataid)->GEOUSER)->firstname ; ?></span></span></div></li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Account Details</span>
                                <span class="caption-helper"></span>
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
                        <div class="portlet-body">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Rate </span><span class="label label-default col-md-7 pull-right"><span id="name">Special</span></span></div></li>
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Connection </span><span class="label label-default col-md-7 pull-right"><span id="name">Corporation</span></span></div></li>
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Ownership </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>Individual</span></div></li>
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Land </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>Owned</span></div></li>
                            </ul>
                            
                        </div>
                    </div>
                </div>
            </div> 
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i>
                                <span class="caption-subject font-green-sharp bold uppercase">Assessment</span>
                                <span class="caption-helper"></span>
                            </div>

                        </div>
                        <div class="portlet-body">
                            
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Load </span><span class="label label-default col-md-7 pull-right"><span id="name">Special</span></span></div></li>
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Lot & Bk </span><span class="label label-default col-md-7 pull-right"><span id="name">01-01</span></span></div></li>
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Job Type </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>ECALES</span></div></li>
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Deposit </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>200,000.00</span></div></li>
                                <li class="list-group-item"><div class="row"><span class=" label-name col-md-5">Requirements </span><span class="label label-default col-md-7 pull-right"><span id="name"></span>5 / 10</span></div></li>
                            </ul>
                            

                            <hr>
                            <legend>Status:</legend>
                            <span class="label label-success"><i class="fa fa-check"></i> Initial Deposit</span> 
                            <span class="label label-warning">On-going</span> 

                        </div>
                    </div>
                </div>
            </div>
            
            

    </div>
        <div class="col-md-4">
            <div class="portlet light table">
                <div class="portlet-title"><h2>Requirements</h2></div>
                <div class="portlet-body">
                    
                     <table class="table table-hover table-condensed table-stripped table-borderd" id="req-list">
                                        <strong><h4>Basic Requirements:</h4></strong>
                                        <thead>
                                        <th>#</th>
                                        <th>Requirements</th>
                                        <th>Status</th>
                                        </thead>

                                        <?php
                                        $req_query = $this->model_query->get_account_application_requirements($dataid);
                                        if ($req_query) {
                                           $num = 1;
                                           foreach ($req_query as $row) {
                                              echo '<tr>';
                                              echo '<td><a class="btn btn-danger btn-xs" href="javascript:;"><i class="fa fa-times"></i></a></td>';
                                              echo '<td>' . $row->NAMES . '<em></em></td>';
                                              echo '<td id="td-stats"></td>';
                                              echo '</tr>';
                                           }
                                        } else {
                                           echo '<tr><td colspan="3">No item(s)</td></tr>';
                                        }
                                        ?>
                                        <tfoot>

                                            <tr>

                                                <td></td>
                                                <td></td>
                                                <td>

                                                </td>
                                            </tr>

                                        </tfoot>
                                    </table>
                    
                  <div class="row">
                      <hr>
                      <div class="col-md-12">
                            <div class="form-group form-md-line-input">
                              <label class="col-md-3 control-label" for="assettype">Req.</label>
                              <div class="col-md-9">
                                <input id="acct_req_add" name="acctreqadd" type="text" class="form-control input-sm " placeholder="Additional Requirements">
                              </div>

                            </div>
                      </div>
                      
                      <div class="col-md-12">
                            <div class="form-group form-md-line-input">
                              <label class="col-md-3 control-label" for="assettype">Upload Req.</label>
                              <div class="col-md-9">
                                <input id="acct_req_upload" name="acctrequpload" type="file" class="form-control input-sm " placeholder="Additional Requirements">
                              </div>

                            </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    <div class="col-md-2">
        <div class="portlet light">
            <div class="portlet-title"><h2>Validations</h2></div>
            <div class="portlet-body">
                Validation for account.
            </div>
        </div>
        
        <a href="javascript:;" class="icon-btn margin-top-10" style="width: 100% !important; min-height: 90px;">
            <i class="fa fa-user" style="font-size: 2em;"></i>
            <div>
                <p style="font-size: 18px;">Audit Check</p>
                <p>2016-09-19</p>
            </div>
         
            <span class="badge badge-success">
                <i class="fa fa-check"></i>
            </span>
        </a>
        
        <a href="javascript:;" class="icon-btn margin-top-20" style="width: 100% !important; min-height: 90px;">
            <i class="fa fa-group" style="font-size: 2em;"></i>
            <div>
                <p style="font-size: 18px;">Legal Check</p>
                <p>2016-09-12</p>
            </div>
           
            <span class="badge badge-success">
                <i class="fa fa-check"></i>
            </span>
        </a>
        
        <a href="javascript:;" class="icon-btn margin-top-20" style="width: 100% !important; min-height: 90px;">
            <i class="fa fa-search" style="font-size: 2em;"></i>
            <div>
                <p style="font-size: 18px;">Inspection</p>
                <p>2016-09-12</p>
            </div>
           
            <span class="badge badge-success">
                <i class="fa fa-check"></i>
            </span>
        </a>
        
        <a href="javascript:;" class="icon-btn margin-top-20" style="width: 100% !important; min-height: 90px;">
            <i class="fa fa-tag" style="font-size: 2em;"></i>
            <div>
                <p style="font-size: 18px;">Requirements</p>
                <p>1/12</p>
            </div>
            
            <span class="badge badge-danger">
                <i class="fa fa-times"></i>
            </span>
        </a>
    </div>
 </div>
 <script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script> 