<?php
$qry_appr = $this->db->select()->from('trn_apprehensions')->where('sysid', $dataid)->get()->row();
$qry_appr_type = $this->db->select()->from('prime_types_parameter')->where('sysid', $qry_appr->types)->get()->row();
$personid = $qry_appr->personid;

$pic_url = get_users_pic_url($personid, true, false);
$person_qry = get_person_info($personid);
if ($person_qry->qry == true) {
    $name = $person_qry->info->lastname . ', ' . $person_qry->info->firstname . ' ' . $person_qry->info->middlename;
    $gender = $person_qry->info->gender;
    $addrspec = $person_qry->info->addrspec;
    $contact = $person_qry->info->contact;
    $birthdate = $person_qry->info->birthdate;
} else {
    $name = '';
    $gender = '';
    $addrspec = '';
    $contact = '';
    $birthdate = '';
}
?>
<div class="row">
    <div class="col-md-5">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i>
                    <span class="caption-subject font-green-sharp bold uppercase"><?php echo str_pad($dataid, 6, '0', STR_PAD_LEFT); ?></span>
                    <span class="caption-helper">LIS Entry</span>
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
                    <div class="col-md-6">
                        <img class="img img-bordered margin-top-10" src="<?php echo $pic_url; ?>" width="100%" height="100%" />

                        <div class="profile-usermenu">
                            <ul class="nav">
                                <li class="active">
                                    <a href="#tab_ap" data-toggle="tab" aria-expanded="true">
                                        <i class="fa fa-file"></i> Account Receivable</a>
                                </li>
                                <?php if($qry_appr->types==150) { ?>
                                <li class="">
                                    <a href="#tab_apprehension" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-file-text-o"></i> Payment Plan</a>
                                </li>
                                <?php } else {  ?>
                                    <li class="">
                                        <a href="#tab_bchistory" data-toggle="tab" aria-expanded="false">
                                            <i class="fa fa-file-text-o"></i> History</a>
                                    </li>
                                <?php }  ?>
                            </ul>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <ul class="list-group summary row no-border">
                            <li class="list-group-item">
                                <div class="row">
                                    <h3 class="margin-top-0 padding-top-0">
                                        <span class=" label-name col-md-3">Name </span>
                                        <span class="label label-default col-md-9 pull-right">
                                            <?php echo $name; ?>
                                        </span>
                                    </h3>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <span class=" label-name col-md-3">Gender </span>
                                    <span class="label label-default col-md-9 pull-right">
                                        <?php echo $gender; ?>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <span class=" label-name col-md-3">Birth Date </span>
                                    <span class="label label-default col-md-9 pull-right">
                                        <?php echo $birthdate; ?>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <span class=" label-name col-md-3">Address Specific </span>
                                    <span class="label label-default col-md-9 pull-right">
                                        <?php echo $addrspec; ?>
                                    </span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <span class=" label-name col-md-3">Contact </span>
                                    <span class="label label-default col-md-9 pull-right">
                                        <?php echo $contact; ?>
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>


            </div>


        </div>
    </div>

    <div class="col-md-7">
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-edit"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Account Receivable </span>
                    <span class="caption-helper"></span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_ap">
                        <form id="frm_new_ap" action="<?php echo base_url('legal/newap'); ?>" method="post">
                            <div class="form-body">
                                <h4>Account Receivables <span class="pull-right text-danger text-bold"><?php echo $qry_appr_type->names; ?></span></h4>
                                <hr>

                                <?php if($qry_appr->types==150) { ?>
                                    <input type="hidden" value="<?php echo $qry_appr->types; ?>" name="apptype" />

                                    <div class="form-group row">
                                        <label class="col-md-3">AR Descriptions</label>
                                        <div class="col-md-9">
                                            <div class=" input-group  input-icon">
                                                <i class="fa fa-search"></i>
                                                <input class="form-control" placeholder="Descriptions.." name="apdescs" />
                                                <span class="input-group-addon"><?php echo $qry_appr_type->names; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <div class="form-group row">
                                    <label class="col-md-3">AR Terms</label>
                                    <div class="col-md-3">
                                        <div class=" input-icon">
                                        <i class="fa fa-tag"></i>
                                        <input class="form-control" placeholder="Terms" name="terms" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class=" input-icon">
                                        <i class="fa fa-tag"></i>
                                        <input class="form-control" placeholder="Actual Amt" name="actamt" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class=" input-icon">
                                        <i class="fa fa-tag"></i>
                                        <input class="form-control" placeholder="Computed Amt" name="compamt" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3">AR Corrected Amt</label>
                                    <div class="col-md-5">
                                        <div class="  input-icon">
                                        <i class="fa fa-search"></i>
                                        <input class="form-control" placeholder="Corrected Amt." name="coramt" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="  input-icon">
                                            <i class="fa fa-search"></i>
                                            <input class="form-control" placeholder="Downpayment Amt." name="dpamt" />
                                        </div>
                                    </div>
                                </div>

                                <?php }else { ?>
                                    <input type="hidden" value="<?php echo $qry_appr->types; ?>" name="apptype" />
                                    <div class="form-group row">
                                        <label class="col-md-3">Bounced Check Details</label>
                                        <div class="col-md-5">
                                            <div class="input-group input-icon">
                                                <i class="fa fa-search"></i>
                                                <input class="form-control" placeholder="Bank name" name="bankname"  id="bankname"  />
                                                <span class="input-group-btn">
                                                    <?php
                                                    $btn_new_bank_contents = "
                                                        <from id='frm_new_bank_entry' style='width: 200px; display: inline-block;' method='post' action='".base_url('systems/addnewbank')."'>
                                                                <div class='form-group row'>
                                                                    <div class='col-md-12'>
                                                                    <input class='form-control' name='bankcode' placeholder='Bank Code' />
                                                                    </div>
                                                                </div>
                                                                <div class='form-group row'>
                                                                    <div class='col-md-12'>
                                                                    <input class='form-control' name='bankdesc' placeholder='Bank Descriptions' />
                                                                    </div>
                                                                </div>
                                                                <div class='form-group row'>
                                                                    <div class='col-md-12'>
                                                                    <button type='submit' class='btn btn-primary'>Save</button>
                                                                    </div>
                                                                </div>

                                                            </form>
                                                    ";
                                                    ?>
                                                    <?php echo row_popover_button('btn_add_bank', '<i class="fa fa-plus"></i>', $btn_new_bank_contents, 'New Bank Entry', 'left', false, 'btn-default');?>

                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-icon">
                                                <i class="fa fa-search"></i>
                                                <input class="form-control" placeholder="Account Number" name="bankno" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3"></label>
                                        <div class="col-md-9">
                                            <div class="input-group input-icon">
                                                <i class="fa fa-user"></i>
                                                <input class="form-control" placeholder="Holder name" name="holder" />
                                                <span class="input-group-addon"><?php echo $qry_appr_type->names; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3">Bounced Check Transaction</label>
                                        <div class="col-md-3">
                                            <div class=" input-icon">
                                                <i class="fa fa-tag"></i>
                                                <input class="form-control" type="date" placeholder="BP Check Date" name="bpcheckdte" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class=" input-icon">
                                                <i class="fa fa-tag"></i>
                                                <input class="form-control" placeholder="Check Amt" name="checkamt" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class=" input-icon">
                                                <i class="fa fa-tag"></i>
                                                <input class="form-control" placeholder="OR No." name="orno" />
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="form-actions">
                                <hr>
                                <div class="btn-group">
                                    <button class="btn btn-default" type="button">Generate Letter</button>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                    <button class="btn btn-default" type="reset">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="tab_apprehension">
                        <table class="table table-hover table-border table-condensed table-striped">
                            <thead>
                                <th><i class="fa fa-reorder"></i></th>
                                <th>Year</th>
                                <th>Month</th>
                                <th>Duedate</th>
                                <th>Paid</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2018</td>
                                    <td>MAY</td>
                                    <td>5-30-2018</td>
                                    <td><span class="label label-danger">Unpaid</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>2018</td>
                                    <td>JUNE</td>
                                    <td>6-30-2018</td>
                                    <td><span class="label label-danger">Unpaid</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/legal/main.js"></script>


<script>
    LEGAL.verification();
</script>