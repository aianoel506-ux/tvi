<style>

    div.scoreboard-html {
        margin-top: 50px;
    }

    div.score-bar{
        display: inline-block;
        width: 8px;
        margin-left: 5px;
        background: #ccc;
        vertical-align:  bottom;
        position: relative;
        height: 50px;
        transition: all 0.5s ease;
        -moz-transition: all 0.5s ease;
    }

    div.score-bar-seq {
        background: #000 !important;
        height: 70px !important;
    }

    .score-seq {
        position: absolute;
        bottom: -20px;
        left: -2px;
        color: red;
    }

    .score-seq.your-score {
        top: -25px !important;
        color: red;
        padding: 2px 2px;
        left: -7px;
        height: 25px;
        border: red 1px solid;
        cursor: pointer;
    }

    .score-curr {
        background: red !important;
        height: 80px !important;
    }
    .rate{
        cursor: pointer;
    }
    .textarenoresize{
        resize: none;
    }

    .popover{
        max-width: 800px; !important; /* Max Width of the popover (depending on the container!) */
    }
</style>

<?php


$username = ($userinfo) ? $userinfo->username : '';


$middlename = 'Unknown';
if($userinfo && $userinfo->personid!='') {
    $user_pic_url = get_owner_pic($userinfo->personid, 'person');
    $qry_person = $this->db->select("p.lastname,p.firstname,p.middlename")
        ->from('person as p')
        ->where(array("p.sysid" => $userinfo->personid))
        ->get()->row();
    $middlename = $qry_person->middlename[0];
    $fullname = $qry_person->lastname . ', '. $qry_person->firstname . ' ' . $qry_person->middlename[0] . '.';
} else {
    $user_pic_url = get_users_pic_url(user_id(), true, true);
    $fullname = $userinfo->lastname . ', ' . $userinfo->firstname;
}


$check_confirm = $this->db->select()
    ->from('prime_system_users_confirmation')
    ->where(array('personid' => $userinfo->personid, 'status' => 2))
    ->get()->row();

$getemail = $this->db->select("contactstring")->from("person_contact_matrix")->where(array("types" => 1053,"status" => 1,"personid" => $userinfo->personid))->get()->row();

$syste_usersid =($userinfo) ? $userinfo->sysid : '';

$fetchuseraccount = $this->db->select("psu.username,psu.password,psurm.roleid")
    ->from("prime_system_users as psu")
    ->join("prime_system_users_roles_matrix as psurm" , "psurm.userid = psu.sysid" , "left")
    ->where(array("psurm.userid" => $syste_usersid))
    ->get()
    ->row();

$eval = get_employee_evaluation_data(user_id(),1,date('Y'),user_id(),false);
$evalclass = ($eval && $eval->qry == true) ? 'disabled' : '';
$strength = ($eval && $eval->qry == true) ? $eval->strength : '';
$weakness = ($eval && $eval->qry == true) ? $eval->weakness : '';
$evaldiscussed = ($eval && $eval->qry == true) ? $eval->evaldiscussed : '';
$groupid = ($eval && $eval->qry == true) ? $eval->groupid : '';
?>


<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/admin/pages/css/profile.css" />
<link href="<?php echo base_url(); ?>assets/admin/pages/css/inbox.css" rel="stylesheet" type="text/css"/>


<div class="page-content-wrapper animated fadeInUp fast">
    <div class="page-content">
        <div class="row">
            <?php //echo $this->uri->segment(4); ?>
            <div class="col-md-12">
                <!-- BEGIN PROFILE SIDEBAR -->
                <div class="profile-sidebar" style="width: 300px; background: #fff">
                    <!-- PORTLET MAIN -->
                    <div class="portlet profile-sidebar-portlet" style="position:relative;padding-top: 20px !important;">
                        <div class="portlet-body tab-content" style="background: #fff;">
                            <!-- SIDEBAR USERPIC -->
                            <div class="profile-userpic">
                                <img src="<?php echo $user_pic_url; ?>" class="img-responsive img-circle" />

                            </div>
                            <!-- END SIDEBAR USERPIC -->
                            <!-- SIDEBAR USER TITLE -->
                            <div class="profile-usertitle">
                                <div class="profile-usertitle-name">
                                    <?php echo $fullname;?>
                                </div>
                                <div class="profile-usertitle-job">
                                    <?php
                                    if ($userid == 1){
                                        echo '<a href="javascript:;" class="label tooltips" data-toggle="tooltip" title="Super Admin" style="background: red; color: white; border-radius: 5px !important;">SU</a>';
                                    }else {
                                        if ($userroles->num_rows() > 0) {
                                            foreach ($userroles->result() AS $role_row) {
                                                echo '<a href="javascript:;" class="label tooltips" data-toggle="tooltip" style="color: white; background: ' . $role_row->color . '; border-radius: 5px !important; -moz-border-radius: 5px !important;" title="'.$role_row->descriptions.'">' . $role_row->code . '</a> ';
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- END SIDEBAR USER TITLE -->
                            <!-- SIDEBAR BUTTONS -->
                            <div class="profile-userbuttons">
                                <button type="button" class="btn btn-circle green-haze btn-sm">Follow</button>
                                <button type="button" class="btn btn-circle btn-danger btn-sm">Message</button>

                            </div>
                            <!-- END SIDEBAR BUTTONS -->
                            <!-- SIDEBAR MENU -->
                            <div class="profile-usermenu">
                                <ul class="nav">
                                    <li class="active"> <a href="#profile" data-toggle="tab" aria-expanded="true"> <i class="fa fa-user fa-fw"></i> Profile </a> </li>
                                    <?php
                                    if(user_id() == 1){
                                        echo '<li > <a href="#usermaintenance" data-toggle="tab" aria-expanded="true"> <i class="fa fa-user fa-fw"></i> User Maintenance </a> </li>';
                                    }

                                    ?>
                                    <li class=""> <a href="#message" data-toggle="tab" aria-expanded="true"> <i class="fa fa-envelope fa-fw"></i> Messages </a> </li>
                                    <li class=""> <a href="#activities" data-toggle="tab" aria-expanded="true"> <i class="icon-bell"></i> Activities </a> </li>


                                    <?php
                                      /*  $checkifemployee = $this->db->select("")
                                            ->from("prime_system_users as psu")
                                            ->join("person as p", "p.sysid = psu.personid")
                                            ->join("prime_employee_main as pem" , "pem.personid = p.sysid")
                                            ->where(array("psu.sysid" => user_id(),"")) */
                                    ?>

                                    <li class=""> <a href="#evaluation" data-toggle="tab" aria-expanded="true"> <i class="icon-book-open"></i>Self Evaluation </a> </li>
                                </ul>


                            </div>

                        </div>
                        <!-- END MENU -->

                    </div>


                </div>

                <div class="profile-content" style="min-height:500px;">
                    <!-- PERSONAL INFORMATION CONTENT -->
                    <div class="tab-content">
                        <div class="note note-success" style="margin-bottom: 0px;">
                            <div class="row">
                                <div class="col-md-5">

                                    <h4 class="profile-desc-title"><i class="fa fa-clock-o fa-fw"></i>Last Login</h4>
                                    <p class="text-danger" style="padding: 2px 2px;"><i class="fa fa-search fa-fw"></i><?php echo $userllog; ?></p>
                                </div>
                                <div class="col-md-7 purple-sharp-stripe">
                                    <h4 class="profile-desc-title"><i class="fa fa-bookmark fa-fw"></i>Last Page Visit</h4>
                                    <p style="padding: 2px 2px; color: #CC0000"><?php echo $userlnav; ?></p>
                                </div>
                            </div>
                        </div>


                            <div id="evaluation" class="tab-pane  fade in">

                                <div class="portlet light">
                                    <div class="portlet-title">
                                        <div class="caption bold">
                                            EMPLOYEE PERFORMANCE EVALUATION

                                        </div>
                                        <div class="tools">
                                            <button data-toggle="popovers" data-content="

                                            <p style='margin: 0px 0px 25px 25px;'><strong> Describe Sixteen(16) personal traits identified with job success or failure.
                                                Decide for each, the level at which the employee performed

                                                for this rating period.click the corresponding value number statement in the rating column, and sum all rating to obtain a total score. Please complete this evaulation carefully and thoroughly. Remember its purpose is to:</strong></p>

                                            <ol>
                                                <li> Provide Objective criteria for personnel performance evaluation on standard basis within the organization</li>
                                                <li>Compel to examine all of the individual traits affecting emplopyee performance</li>
                                                <li>Help you support your conclusion and recommendation for job classification and compensation improvement </li>
                                                <li>Produce fairer evaluation of employees </li>
                                                <li>Ratings 0-1 and 3-4 should be coupled with justifiable reason on way the rated employee is being given such rating</li>
                                            </ol>

                                            " data-trigger="click" data-placement="left" data-title="<i class='fa fa-question text-warning'></i> Tips" class="btn btn-default pull-right popovers"><i class="fa fa-question"></i></button>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>From</label>
                                                    <input type="date" <?php echo $evalclass ?> value="<?php echo (date('Y') - 1).'-06-01' ?>" class="form-control" name="" />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>To</label>
                                                    <input type="date" <?php echo $evalclass ?> value="<?php echo date('Y').'-05-31' ?>" class="form-control" name="" />
                                                </div>
                                            </div>
                                            <table class="table table-bordered table-striped table-condensed  table-responsive" id="evaluationtable">
                                                <thead>
                                                <tr>
                                                    <th rowspan="2">NO.</th>
                                                    <th  rowspan="2" align="center">PERSONAL TRAITS</th>
                                                    <th rowspan="2" align="center">DESCRIPTIONS</th>
                                                    <th style="background-color: #ff8720;color: white;">UNSATISFACTORY</th>
                                                    <th style="background-color: #ff8720;color: white;"> SOME DEFICIENCIES</th>
                                                    <th style="background-color: #ff8720;color: white;"> SATISFACTORY</th>
                                                    <th style="background-color: #ff8720;color: white;"> EXCEPTIONAL</th>
                                                    <th style="background-color: #ff8720;color: white;"> CLEARLY OUTSTANDING</th>
                                                    <td rowspan="2" align="center">
                                                        <i class="fa fa-list"></i>
                                                    </td>

                                                </tr>
                                                <tr align="center">
                                                    <th> 0 </th>
                                                    <th> 1 </th>
                                                    <th> 2 </th>
                                                    <th> 3 </th>
                                                    <th> 4 </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>

                                            </table>
                                        </div>
                                        <div class="row" align="center">
                                            <div id="scoreboardhtml" class="scoreboard-html"></div>
                                        </div>

                                        <form id="submitevaluation" action="<?php echo base_url() ?>hris/submitevaluation" method="post">
                                                <div class="row" style="margin-top: 60px;">
                                                    <input type="hidden" name="empid" value="<?php echo user_id() ?>" />
                                                    <input type="hidden" name="evaltype" value="1" />
                                                    <input type="hidden" name="fromcov" value="<?php echo (date('Y') - 1).'-06-01' ?>" />
                                                    <input type="hidden" name="tocov" value="<?php echo date('Y').'-05-31' ?>" />
                                                    <div class="col-md-6">
                                                        <h4>COMMENTS ON PRINCIPLE STRENGTH</h4>
                                                        <textarea <?php echo $evalclass ?> id="commentonstrengthtxt" class="form-control textarenoresize" name="comps" cols="65" rows="6" required=""><?php echo $strength; ?></textarea>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <h4>COMMENTS ON PRINCIPLE WEAKNESS</h4>
                                                        <textarea <?php echo $evalclass ?> id="commentonweaknesstxt" class="form-control textarenoresize" name="compw" cols="65" rows="6" required=""><?php echo $weakness; ?></textarea>

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div style="resize: none" class="col-md-6">
                                                        <h4>AREAS OF IMPROVEMENT / FOCUS ON (ACTION PLAN)</h4>

                                                        <?php
                                                            $loopfields = $this->db->select("sysid,codes,desc")
                                                                ->from("prime_types_parameter")->where(array("status" => 1,
                                                                    "codes" => 'EVAL'))
                                                                ->order_by("sysid","asc")
                                                                ->get();
                                                            if($loopfields->num_rows() > 0){
                                                                foreach ($loopfields->result() as $row){

                                                                    $getremarks = $this->db->select("remarks")->from("evaluation_other_info")
                                                                        ->where(array("groupid" => $groupid,"types" => $row->sysid , "status" => 1))
                                                                        ->get()->row();
                                                                    $remarks = ($getremarks) ? $getremarks->remarks : '';
                                                                    echo '<textarea '.$evalclass.'  class="form-control textarenoresize" placeholder="'.$row->desc.'" name="remarks[]" cols="40" rows="2" >'.$remarks.'</textarea>';
                                                                }
                                                            }
                                                        ?>

                                                    </div>


                                                    <div style="margin-top: 20px;">
                                                        <label>APPROVED BY :</label> LUIS MIGUEL A. CACHO <br>
                                                        <input <?php echo $evalclass ?> type="radio" id="yes" name="approvechoices" value="1">
                                                        <label for="yes">YES</label>
                                                        <input <?php echo $evalclass ?> type="radio" id="no" name="approvechoices" value="0" checked="">
                                                        <label for="no">NO</label>
                                                    </div>
                                                    <br>
                                                    <?php
                                                        if($eval->qry == false){
                                                            ?>
                                                            <button id="saveevalbtn" type="submit" class="btn btn-primary btn-lg">
                                                                <i class="fa fa-save"></i> SAVE EVALUATION
                                                            </button>
                                                            <br>
                                                            <br>
                                                            <label id="notelabel">Note: <code>By saving your evaluation, you're not allowed to edit or make changes anymore. </code></label>

                                                            <?php
                                                        }

                                                    ?>

                                                </div>
                                        </form>

                                        </div>
                                </div>

                            </div>
                            <div id="profile" class="tab-pane active fade in">
                            <div class="portlet light">
                                <div class="portlet-title  tabbable-line">
                                    <div class="caption"> <i class="fa fa-edit"></i>
                                        <span class="caption-subject font-green-sharp bold uppercase">User's Profile</span>
                                        <span class="caption-helper">account management</span> </div>

                                    <ul class="nav nav-tabs">

                                        <?php
                                        $over_view = '';
                                        if($check_confirm == false) {
                                            $over_view = 'active';
                                        }
                                        $account_view = '';

                                        if($check_confirm) {
                                            $account_view = 'active';
                                        }
                                        ?>

                                        <li class="active">
                                            <a href="#feeds" data-toggle="tab"><i class="fa fa-user fa-fw" aria-hidden="true"></i>
                                                Feeds</a>
                                        </li>

                                        <li class="">
                                            <a href="#overview" data-toggle="tab"><i class="fa fa-user fa-fw" aria-hidden="true"></i>
                                                Overview</a>
                                        </li>

                                        <li class="">
                                            <a href="#account" data-toggle="tab"><i class="fa fa-gear fa-fw" aria-hidden="true"></i>
                                                Account</a>
                                        </li>

                                    </ul>

                                </div>
                                <div class="portlet-body" >
                                    <div class="tab-content" style="min-height: 400px;">
                                        <div class="tab-pane active" id="feeds">
                                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 320px;"><div class="scroller" style="height: 320px; overflow: hidden; width: auto;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" data-initialized="1">
                                                    <ul class="feeds">
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-success">
                                                                            <i class="fa fa-bell-o"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            You have 4 pending tasks. <span class="label label-sm label-info">
                                                                                        Take action <i class="fa fa-share"></i>
                                                                                    </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    Just now
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;">
                                                                <div class="col1">
                                                                    <div class="cont">
                                                                        <div class="cont-col1">
                                                                            <div class="label label-sm label-success">
                                                                                <i class="fa fa-bell-o"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="cont-col2">
                                                                            <div class="desc">
                                                                                New version v1.4 just lunched!
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col2">
                                                                    <div class="date">
                                                                        20 mins
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-danger">
                                                                            <i class="fa fa-bolt"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Database server #12 overloaded. Please fix the issue.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    24 mins
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-info">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            New order received and pending for process.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    30 mins
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-success">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            New payment refund and pending approval.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    40 mins
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-warning">
                                                                            <i class="fa fa-plus"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            New member registered. Pending approval.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    1.5 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-success">
                                                                            <i class="fa fa-bell-o"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Web server hardware needs to be upgraded. <span class="label label-sm label-default ">
                                                                                        Overdue </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    2 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-default">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Prod01 database server is overloaded 90%.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    3 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-warning">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            New group created. Pending manager review.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    5 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-info">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Order payment failed.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    18 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-default">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            New application received.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    21 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-info">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Dev90 web server restarted. Pending overall system check.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    22 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-default">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            New member registered. Pending approval
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    21 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-info">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            L45 Network failure. Schedule maintenance.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    22 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-default">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Order canceled with failed payment.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    21 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-info">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Web-A2 clound instance created. Schedule full scan.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    22 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-default">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            Member canceled. Schedule account review.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    21 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm label-info">
                                                                            <i class="fa fa-bullhorn"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            New order received. Please take care of it.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col2">
                                                                <div class="date">
                                                                    22 hours
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div><div class="slimScrollBar" style="width: 7px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px; height: 169.256198347107px; background: rgb(215, 220, 226);"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(234, 234, 234);"></div></div>
                                        </div>
                                        <div class="tab-pane fade in" id="account">
                                            <?php
                                            if($check_confirm) {
                                                echo '<div class="note note-danger note-bordered"><i class="fa fa-warning"></i> Please change your temporary password.</div>';
                                            }
                                            ?>

                                            <form autocomplete="off" method="post" action="<?php echo base_url('user/updateaccount'); ?>" id="frm_upd_acct">
                                                <input type="hidden" name="userid" value="<?php echo $userid; ?>" />

                                                <h3><i class="fa fa-user"></i> User Login</h3>
                                                <hr>
                                                <ul class="list-group summary column no-border">
                                                    <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            Username
                                                        </span>
                                                        <span class="col-md-8 label label-default">
                                                        <div class="form-group form-md-line-input has-success" style="margin-top: 0px !important; padding-top: 0px;">
                                                            <div class="input-icon" style="margin-top: 0px !important; padding-top: 0px;">
                                                                <input disabled type="text" class="form-control" placeholder="Username" value="<?php echo $username; ?>">
                                                                <i class="fa fa-user"></i>
                                                            </div>
                                                        </div>

                                                    </span>
                                                    </li>

                                                    <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            Current Password
                                                        </span>
                                                        <span class="col-md-8 label label-default">
                                                        <div class="form-group form-md-line-input has-success" style="margin-top: 0px !important; padding-top: 0px;">
                                                            <div class="input-icon" style="margin-top: 0px !important; padding-top: 0px;">
                                                                <input autocomplete="off" name="passwordold" type="password" class="form-control" placeholder="&#9829; &#9829; &#9829; &#9829; &#9829;">
                                                                <i class="fa fa-key"></i>
                                                            </div>
                                                        </div>

                                                    </span>
                                                    </li>

                                                    <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            New Password
                                                        </span>
                                                        <span class="col-md-8 label label-default">
                                                        <div class="form-group form-md-line-input has-success" style="margin-top: 0px !important; padding-top: 0px;">
                                                            <div class="input-icon" style="margin-top: 0px !important; padding-top: 0px;">
                                                                <input autocomplete="off" name="passwordnew" type="password" class="form-control" placeholder="&#9829; &#9829; &#9829; &#9829; &#9829;">
                                                                <i class="fa fa-key"></i>
                                                            </div>
                                                        </div>
                                                    </span>
                                                    </li>

                                                    <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            Confirm Password
                                                        </span>
                                                        <span class="col-md-8 label label-default">
                                                        <div class="form-group form-md-line-input has-success" style="margin-top: 0px !important; padding-top: 0px;">
                                                            <div class="input-icon" style="margin-top: 0px !important; padding-top: 0px;">
                                                                <input autocomplete="off" name="passwordcon" type="password" class="form-control" placeholder="&#9829; &#9829; &#9829; &#9829; &#9829;">
                                                                <i class="fa fa-key"></i>
                                                            </div>
                                                        </div>
                                                    </span>
                                                    </li>
                                                </ul>
                                                <hr>
                                                <button type="button" class="btn btn-danger pull-right"><i class="fa fa-times"></i> Deactivate Account</button>
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade in" id="overview">
                                            <h3><i class="fa fa-user"></i> Basic Information</h3>
                                            <ul class="list-group summary column">

                                                <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            First Name
                                                        </span>
                                                    <span class="col-md-8 label label-default">
                                                            <a href="javascript:;" id="firstname" data-type="text" data-value="2015-12-01" data-pk="1" data-original-title="Modify Firstname" class="editable editable-click" style="display: inline;"><?php echo $userinfo->firstname; ?></a>
                                                        </span>
                                                </li>

                                                <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            Middle Name
                                                        </span>
                                                    <span class="col-md-8 label label-default">
                                                            <a href="javascript:;" id="middlename" data-type="text" data-value="2015-12-01" data-pk="1" data-original-title="Modify Middlename" class="editable editable-click" style="display: inline;"><?php echo $middlename; ?></a>
                                                        </span>
                                                </li>

                                                <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            Last Name
                                                        </span>
                                                    <span class="col-md-8 label label-default">
                                                            <a href="javascript:;" data-type="text" data-value="<span class=&quot;text-info&quot;><i class=&quot;fa fa-male&quot;></i> Male</span>" data-pk="1" data-original-title="Modify Lastname" class="editable editable-click" style="display: inline;"><?php echo $userinfo->lastname; ?></a>
                                                        </span>
                                                </li>

                                                <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            Email Address
                                                        </span>
                                                    <span class="col-md-8 label label-default">
                                                            <a href="javascript:;" id="email" data-type="text" data-value="<span class=&quot;text-info&quot;><i class=&quot;fa fa-male&quot;></i> Male</span>" data-pk="1" data-original-title="Modify Email" class="editable editable-click" style="display: inline;"> <?php echo ($getemail) ? $getemail->contactstring : 'Unknown' ?></a>
                                                        </span>
                                                </li>
                                                <li class="list-group-item">
                                                        <span class="col-md-4 label-name">
                                                            User String
                                                        </span>
                                                    <span class="col-md-8 label label-default">
                                                                <?php echo trim(get_users_pic_url(user_id(), false)); ?>
                                                    </span>
                                                </li>

                                            </ul>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END PERSONAL INFORMATION CONTENT -->
                        </div>
                            <!-- BEGIN NOTIFICATIONS CONTENT -->
                            <div id="activities" class="tab-pane fade in">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet light">

                                            <div class="portlet-title tabbable-line">
                                                <div class="caption caption-md">
                                                    <i class="icon-globe theme-font hide"></i>
                                                    <span class="caption-subject font-blue-madison bold uppercase">Feeds</span>
                                                </div>
                                                <ul class="nav nav-tabs" id="tab_activities">
                                                    <li class="active">
                                                        <a href="#all_notifications" data-toggle="tab">
                                                            <i class="icon-bell"></i> Notificaitons </a>
                                                    </li>
                                                    <li>
                                                        <a href="#all_tasks" data-toggle="tab">
                                                            <i class="icon-handbag"></i> Tasks </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="portlet-body ">
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="all_notifications">
                                                        <table id="tbl_notification_all" class="table table-condensed table-hover table-stripped">
                                                            <thead>
                                                            <th></th>
                                                            <th>Title</th>
                                                            <th>Code</th>
                                                            <th>Description</th>
                                                            <th>From</th>
                                                            <th>Remarks</th>
                                                            <th></th>
                                                            </thead>
                                                        </table>
                                                        <tbody>

                                                        </tbody>
                                                    </div>
                                                    <div class="tab-pane fade in" id="all_tasks">

                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END NOTIFICATIONS CONTENT -->

                            <div id="usermaintenance" class="tab-pane fade in">
                                <div class="portlet light">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            User's Role
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <table class="table table-condensed table-bordered tbl-sm" id="userroles">
                                            <thead>
                                            <th>ID</th>
                                            <th>Code</th>
                                            <th>Descriptions</th>
                                            <th></th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <div id="message" class="tab-pane fade in">
                                <div class="portlet light">
                                    <div class="row inbox margin-top-20">
                                        <div class="col-md-3">
                                            <ul class="inbox-nav margin-bottom-10">
                                                <li class="compose-btn">
                                                    <a href="javascript:;" data-title="Compose" class="btn green">
                                                        <i class="fa fa-edit"></i> Compose </a>
                                                </li>
                                                <li class="inbox active">
                                                    <a href="javascript:;" class="btn" data-title="Inbox">
                                                        Inbox <span class="badge badge-danger pull-right" style="color: #fff">2</span></a>
                                                    <b></b>
                                                </li>
                                                <li class="sent">
                                                    <a class="btn" href="javascript:;" data-title="Sent">
                                                        Sent </a>
                                                    <b></b>
                                                </li>
                                                <li class="draft">
                                                    <a class="btn" href="javascript:;" data-title="Draft">
                                                        Draft </a>
                                                    <b></b>
                                                </li>
                                                <li class="trash">
                                                    <a class="btn" href="javascript:;" data-title="Trash">
                                                        Trash </a>
                                                    <b></b>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="inbox-header">
                                                <h1 class="pull-left">Inbox</h1>
                                            </div>
                                            <div class="">
                                                <table id="tbl_inbox" class="table table-hover table-condensed table-advanced table-striped">
                                                    <thead>
                                                    <th><i class="fa fa-navicon"></i></th>
                                                    <th>Sender</th>
                                                    <th>Subject</th>
                                                    <th>Content</th>
                                                    <th>Control</th>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END LOANS CONTENT -->
                    </div>
                    <!-- END PROFILE CONTENT -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- END CORE PLUGINS -->
<!-- BEGIN: Page level plugins -->
<script src="<?php echo base_url(); ?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
<!-- BEGIN:File Upload Plugin JS files-->
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
<!-- blueimp Gallery script -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]> -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/profile.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/inbox.js" type="text/javascript"></script>

<script>
    PROFILE.init(<?php echo $syste_usersid; ?>);
</script>
