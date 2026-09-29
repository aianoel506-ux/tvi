<?php
$emp_arr = get_user_employee_info();

if($emp_arr) {

    echo '<pre>';
    echo print_r($emp_arr);
    echo '</pre>';

    $info = get_employee_info($emp_arr->sysid);
?>

<div class="portlet light">
    <div class="portlet-title tabbable-line">
        <div class="caption">
            <i class="icon-user font-green"></i>
            <span class="caption-subject font-green bold uppercase">
                <?php echo $emp_arr->lastname . ', '. $emp_arr->firstname; ?>
                <?php echo '<b>'.$info->deptcode.'</b> - ' . $info->deptname; ?>
            </span>
        </div>

        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#exec" data-id="2" data-toggle="tab"> Executives</a>
            </li>
            <li>
                <a href="#jexec" data-id="4" data-toggle="tab" aria-expanded="true"> Junior Executives </a>
            </li>
            <li class="">
                <a href="#execa" data-id="5" data-toggle="tab" aria-expanded="false"> Executive Assistants </a>
            </li>
            <li class="">
                <a href="#depthead" data-id="6" data-toggle="tab" aria-expanded="false"> Department Heads </a>
            </li>
            <li class="">
                <a href="#emp" data-id="0" data-toggle="tab" aria-expanded="false"> Employees </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <div class="tab-content">
            <div class="tab-pane fade in active" id="exec">
                <table class="table table-bordered table-hover" id="executivetbl">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th> Name </th>
                        <th> Position </th>
                        <th> Co-Executive </th>
                        <th> Self </th>
                        <th> Committee </th>
                        <th> PCEO </th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade in" id="jexec">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th> Name </th>
                        <th> Position </th>
                        <th> Self </th>
                        <th> Executive </th>
                        <th> Committee </th>
                        <th> PCEO </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="">
                        <td> 1 </td>
                        <td> VPAF </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                    </tr>
                    <tr class="">
                        <td> 2 </td>
                        <td> AVPO </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                        <td> Column heading </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade in" id="execa">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input class="form-control" id="select2execall" placeholder="Select executive" />
                        </div>
                    </div>
                    <div class="col-md-9">

                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th> # </th>
                                <th> Name </th>
                                <th> Position </th>
                                <th> Self </th>
                                <th> Executive </th>
                                <th> Committee </th>
                                <th> PCEO </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="">
                                <td> 1 </td>
                                <td> VPAF </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                            </tr>
                            <tr class="">
                                <td> 2 </td>
                                <td> AVPO </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="depthead">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input class="form-control" id="select2exec" placeholder="Select executive" />
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="select2execj" placeholder="Select Junior executive" />
                        </div>
                    </div>
                    <div class="col-md-9">

                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th> # </th>
                                <th> Name </th>
                                <th> Position </th>
                                <th> Self </th>
                                <th> Executive </th>
                                <th> Committee </th>
                                <th> PCEO </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="">
                                <td> 1 </td>
                                <td> VPAF </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                            </tr>
                            <tr class="">
                                <td> 2 </td>
                                <td> AVPO </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                                <td> Column heading </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="emp">
                <div class="row">
                    <div class="col-md-3">
                       <form id="sumitemployeefilter" method="post" action="<?php echo base_url() ?>hris/sumitemployeefilter">
                           <div class="form-group">
                               <input class="form-control" name="select2execforemp" id="select2execforemp" placeholder="Select executive" />
                           </div>
                           <div class="form-group">
                               <input class="form-control" name="select2execjforemp" id="select2execjforemp" placeholder="Select Junior executive" />
                           </div>
                           <div class="form-group">
                               <input class="form-control" name="select2deptforemp" id="select2deptforemp" placeholder="Select Department" />
                           </div>
                           <div class="form-group">
                               <button type="submit" class="btn btn-primary pull-right">Filter</button>
                           </div>
                       </form>
                    </div>
                    <div class="col-md-9">

                        <table class="table table-bordered table-hover" id="employeestable">
                            <thead>
                            <tr>
                                <th> # </th>
                                <th> Name </th>
                                <th> Position </th>
                                <th> Self </th>
                                <th> Executive </th>
                                <th> Dept.Head</th>
                                <th> Committee </th>
                                <th> PCEO </th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } else {

    page_data_notfound('No employee tagged on this user account!');

}

?>



<script src="<?php echo base_url(); ?>assets/pages/evaluation/main.js" type="text/javascript"></script>

<script>
    EVALUATION.init();
</script>
