<!-- DATA TABLE CSS START!-->
<link href="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- DATA TABLE CSS END!-->

        <h3 class="page-title">
            Inspection
            <small>beta</small>
        </h3>
        <div class="row">	
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Inspection Data</span>
                            <span class="caption-helper">pending inspections...</span>
                        </div>
                        <div class="pull-right">
                           <a class="btn btn-default btn-xs fullscreen" href="javascript:;"><i class="fa fa-search fa-fw"></i> Expand</a>
                           <!--<a class="btn btn-warning btn-xs" href="javascript:;"><i class="fa fa-print fa-fw"></i> Print</a>!-->
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php $for_inspection_list = $this->model_inspection_queries->get_hashcode($this->model_admin->get_navigation_specific_details($this->uri->segment(2))->sysid); ?>
                            <table class="table table-striped table-hover" id="inspection_lists">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-reorder"></i></th>
                                        <th>TRN</th>
                                        <th>Transaction Description</th>
                                        <th>Applicant Name</th>
                                        <th>Created By</th>
                                        <th>Transaction Status</th>
                                        <th>Date Created</th>
                                        <th><i class="fa fa-wrench"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $inspection_data = $this->model_inspection_queries->get_transaction_details();
                                    $count = 1;
                                    $button_value = '';
                                    $button_pic = '';
                                    $view = 'data';
                                    foreach ($inspection_data as $row){
                                        $status = $row->status;
                                        echo "<tr>";
                                            echo "<td>".$count++."</td>";
                                            echo "<td>".$row->trncode."</td>";
                                            echo "<td>".$row->descriptions."</td>";
                                            echo "<td>".$row->applicant_ln.", ".$row->applicant_fn."</td>";
                                            echo "<td>".$row->employee_ln.", ".$row->employee_fn."</td>";
                                            if ($status == 2){//status 2 means that the data has been disapproved and returned to inspection for correction.
                                                $view = 'edit';
                                                $button_value = 'EDIT';
                                                $button_pic = 'fa-pencil';
                                                echo "<td><span class='label label-danger'>Disapproved</span></td>";
                                            }else if ($status == 1){//status 1 means that the data has been submitted and the user wants to view the data.
                                                $view = 'view';
                                                $button_value = 'VIEW';
                                                $button_pic = 'fa-file';
                                                echo "<td><span class='label label-success'>Submitted</span></td>";
                                            }else{//tjis is status 0 which means that the inspection data of the customer is empty and needs to be populated by new data.
                                                $button_value = 'ADD';
                                                $button_pic = 'fa-plus';
                                                echo "<td><span class='label label-warning'>Needs Data</span></td>";
                                            }
                                            echo "<td>".$row->datecreated."</td>";
                                            echo "<td><form method='post' action='".base_url()."module/".$for_inspection_list->hashcode."/".$view."/1'><span class='btn-group'>";
                                                echo "<input type='hidden' name='trn' value='$row->trncode'/>"
                                                        . "<input type='hidden' name='dataid' value='$row->dataid'/>"
                                                        . "<input type='hidden' name='status' value='$status'/>"
                                                        . "<button type='submit' class='btn btn-success btn-xs'/>"
                                                        . "<i class='fa $button_pic'>$button_value</i>";
                                            echo "</span></form></td>";
                                        echo "</tr>";
                                    }
                                ?>  
                                </tbody>
                            </table>
                    </div>  
                </div>
            </div>
        </div>
   
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script>
    $('.date-picker').datepicker();
    //DATA-TABLE START
    $('#inspection_lists').dataTable();
    //DATA-TABLE END
    
 
</script>








