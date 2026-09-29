<?php

crm_gallery($id, true);
?>

<div class="row">
    <div class="col-md-6">
        <div class="portlet light portlet-fit bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class=" icon-layers font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Assignment</span>
                    <div class="caption-desc font-grey-cascade"></div>
                </div>
                <a class="pull-right" data-arr="<?php echo $id;?>" href="#frm_crm_assignment" data-toggle="ajax-modal"><i class="fa fa-edit"></i> Assign</a>
                <!-- insert assignment to assignment table of ticketing -->
            </div>
            <div class="portlet-body">
                <table class="table table-bordered table-hover">
                    <thead>
                    <th>#</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Date Assigned</th>
                    <th>Control</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>Lucky John Faderon</td>
                        <td>Software Engineer</td>
                        <td><?php echo date('Y-m-d H:i: A');?></td>
                        <td>
                            <a href="#" class="btn btn-danger inline btn-xs"><i class="fa fa-times"></i></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <?php

        crm_transaction($id, true);
        ?>
    </div>
</div>


