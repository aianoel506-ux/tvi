<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/26/2018
 * Time: 4:46 PM
 */

if($id) {
    ?>
    <?php
    /**
     * Created by PhpStorm.
     * User: ITD-SE
     * Date: 5/30/2018
     * Time: 4:24 PM
     */
    $ci = &get_instance();
    $ci->load->model('model_ts');

    $ts_qry = $ci->model_ts->get_ticket_details($id);
    $ts_decode = json_decode($ts_qry);

    if($ts_decode) {
        $ts_info = $ts_decode->qry;
        $ts_traill = $ts_decode->trail;
        $ts_trail_cnt = $ts_decode->trailno;
        $ts_team = $ts_decode->team;

        $middlename = (isset($ts_info->middlename[0])) ? strtoupper($ts_info->middlename[0]) . '.' : '';
        $complainants = ($ts_info->firstname != '') ? $ts_info->lastname . ', ' . $ts_info->firstname . ' ' . $middlename : $ts_info->compname;
        $tcsource = get_types_label_format($ts_info->repsource, false, false, false, false, true);
        $createdby = '(' . get_users_info($ts_info->createdby)->username . ')' . get_users_info($ts_info->createdby)->firstname;

        if ($ts_info->status != 305) {
            $ticket_trail_arr = array(
                'ticketid' => $ts_info->sysid,
                'codes' => 'READ',
                'descs' => 'By: ' . user_info(user_id())->username,
                'createdby' => user_id()
            );
            $this->db->insert('ticketing_details_trails', $ticket_trail_arr);
        }

        $qry_trail_lastread = $this->db->select()->from('ticketing_details_trails')
            ->where(array('ticketid' => $ts_info->sysid, 'codes' => 'READ'))
            ->order_by('datecreated', 'desc')
            ->get()->row();

        $qry_trail_accomp = $this->db->select()->from('ticketing_details_trails')
            ->where(array('ticketid' => $ts_info->sysid, 'codes' => 'TSACCOMPLISHMENT'))
            ->order_by('datecreated', 'desc')
            ->get()->row();

        $qry_trail_findings = $this->db->select()->from('ticketing_details_logs_findings')
            ->where(array('ticketid' => $ts_info->sysid, 'status' => 1))
            ->get()->row();

        $qry_trail_equipments = $this->db->select()->from('ticketing_details_logs_equipments')
            ->where(array('ticketid' => $ts_info->sysid, 'status' => 1))
            ->get()->row();

        ?>

        <div class="row">
            <div class="col-md-4">
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" icon-layers font-green"></i>
                            <span class="caption-subject font-green bold uppercase">Report Details</span>
                            <div class="caption-desc font-grey-cascade"></div>
                        </div>

                        <div class="actions">
                            <div class="btn-group btn-group-devided">
                                <button class="btn btn-default btn-sm" id="btn_new_ticket"><i class="fa fa-print"></i>
                                    Print Ticket Reports
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body ">
                        <ul class="list-group summary column">
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Source</span>
                                <span class="col-md-8 label-default number"><?php echo $tcsource; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Complainants</span>
                                <span class="col-md-8 label-default number"><?php echo $complainants; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">District</span>
                                <span
                                    class="col-md-8 label-default number"><?php echo get_district_name($ts_info->district); ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Landmarks</span>
                                <span class="col-md-8 label-default number"><?php echo $ts_info->landmarks; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Report Stated</span>
                                <span class="col-md-8 label-default number"><?php echo $ts_info->remarks; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Date Created</span>
                                <span class="col-md-8 label-default number"><?php echo $ts_info->datecreated; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Created By</span>
                                <span class="col-md-8 label-default number"><?php echo $createdby; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Status</span>
                                <span
                                    class="col-md-8 label-default number"><?php echo get_types_label_format($ts_info->status); ?></span>
                            </li>
                        </ul>

                        <hr>

                        <div class="btn-broup">
                            <a class="btn btn-default"
                               href="<?php echo base_url('guest'); ?>">Back
                                To List</a>
                        </div>

                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" icon-layers font-green"></i>
                            <span class="caption-subject font-green bold uppercase">Trail</span>
                            <div class="caption-desc font-grey-cascade" style="display: inline-block; width: 100%;">
                                Last Read: <?php echo ($qry_trail_lastread) ? $qry_trail_lastread->datecreated : ''; ?>
                                <span
                                    class="pull-right text-danger"><?php echo ($qry_trail_lastread) ? $qry_trail_lastread->descs : ''; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="mt-element-list">
                            <div class="mt-list-head list-default font-black bg-default">
                                <div class="list-head-title-container">
                                    <div class="list-date"></div>
                                    <h3 class="list-title">Trail List</h3>
                                </div>
                            </div>
                            <div class="mt-list-container list-default">
                                <ul>
                                    <?php if ($ts_traill) {
                                        foreach ($ts_traill as $trow) {
                                            ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <a href="javascript:;">
                                                        <i class="icon-check"></i>
                                                    </a>
                                                </div>
                                                <div class="list-datetime"
                                                     style="margin-right: 10px;"><?php echo $trow->datecreated; ?></div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="javascript:;"><?php echo $trow->codes; ?></a>
                                                    </h3>
                                                    <p><?php echo $trow->descs; ?></p>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                        if ($ts_trail_cnt > 5) {
                                            echo '<li class="mt-list-item done">';
                                            echo '<a class="pull-right" href="' . base_url('module/d321d6f7ccf98b51540ec9d933f20898af3bd71e/data/' . $id . '/logs') . '">View All (' . $ts_trail_cnt . ')</a>';
                                            echo '</li>';
                                        }

                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" icon-layers font-green"></i>
                            <span class="caption-subject font-green bold uppercase">Team</span>
                            <div class="caption-desc font-grey-cascade"></div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="mt-element-list">
                            <div class="mt-list-head  list-simple font-black bg-default">
                                <div class="list-head-title-container">
                                    <div class="list-date"><?php echo ($ts_team) ? $ts_team->datecreated : ''; ?></div>
                                    <h3 class="list-title"><?php echo ($ts_team) ? get_types_label_format($ts_team->typesid) : 'Not Assign!'; ?></h3>
                                </div>
                            </div>
                            <div class="mt-list-container list-simple">
                                <ul>
                                    <?php
                                    if ($ts_team) {

                                        $qry_team_members = $this->db->select('p.firstname, p.lastname, em.empid')
                                            ->from('prime_employee_team_assignments AS eta')
                                            ->join('prime_employee_main AS em', 'em.sysid = eta.empid')
                                            ->join('person AS p', 'p.sysid = em.personid')
                                            ->where(array('eta.teamid' => $ts_team->typesid))
                                            ->get();
                                        if ($qry_team_members->num_rows() > 0) {
                                            foreach ($qry_team_members->result() as $trow) {
                                                ?>
                                                <li class="mt-list-item">
                                                    <div class="list-icon-container">
                                                        <i class="icon-check"></i>
                                                    </div>
                                                    <div class="list-datetime"><?php echo $trow->empid; ?></div>
                                                    <div class="list-item-content">
                                                        <h3 class="uppercase">
                                                            <a href="javascript:;"><?php echo $trow->lastname, ', ' . $trow->firstname; ?></a>
                                                        </h3>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                        } else {
                                            echo '<li class="mt-list-item">No team members</li>';
                                        }

                                    } else {
                                        echo '<li class="mt-list-item">No team assigned!</li>';
                                    } ?>
                                </ul>
                            </div>
                        </div>

                        <hr>
                        <h4>Accomplishments</h4>
                        <ul class="list-group summary column">
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Remarks</span>
                                <span
                                    class="col-md-8 label-default "><?php echo ($qry_trail_accomp) ? $qry_trail_accomp->remarks : 'None'; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Equipments</span>
                                <span
                                    class="col-md-8 label-default "><?php echo ($qry_trail_equipments) ? get_types_label_format($qry_trail_equipments->equipid, false, false, 'top', '', false) : 'None'; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Findings</span>
                                <span
                                    class="col-md-8 label-default "><?php echo ($qry_trail_findings) ? get_types_label_format($qry_trail_findings->findingid, false, false, 'top', '', false) : 'None'; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }

}else{
    page_data_notfound();
}