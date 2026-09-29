<style>
    .tt-input  {
        color: #000 !important;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 6/22/2018
 * Time: 4:42 PM
 */


$view = $this->input->post("view");
?>

<form id="frm_jo_entry" method="post" action="<?php echo base_url('jo/savenewjo');?>" enctype="multipart/form-data">
    <div class="modal-body">
        <?php
        if( $view>0 ) {
            echo '<input name="repsource" type="hidden" value="'.$view.'" />';
        }else{ ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="well">
                        <div class="form-group">
                            <label class="form-label col-md-3"><i class="fa fa-flag fa-fw"></i>Branch</label>
                            <div class="col-md-9">

                                <div class="icheck-inline">
                                    <?php
                                    $qry_ts_source = $this->db->query("SELECT * FROM prime_types_parameter WHERE codes = 'COSTGROUP' AND status = 1");
                                    if($qry_ts_source->num_rows()>0) {
                                        foreach($qry_ts_source->result() as $jorow) {
                                            $selected = '';
                                            if($jorow->sysid == 3003) {
                                                $selected = "checked";
                                            }
                                            echo '<label><input '.$selected.' name="repsource" value="'.$jorow->sysid.'" type="radio" class="icheck" /> '.$jorow->names.'</label>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="row">

            <div class="col-md-4 complainants">

                <h4 class="text-primary text-bold">Requester</h4>
                <div class="form-group">
                    <label>Last Name <span class="required"></span></label>
                    <input required class="form-control" placeholder="Lastname" name="lastname" id="lastname" autocomplete="off"/>
                </div>

                <div class="form-group">
                    <label>Firstname Name</label>
                    <input required class="form-control" placeholder="Firstname" name="firstname" id="firstname"/>
                </div>

                <div class="form-group">
                    <label>Middle Name</label>
                    <input class="form-control" placeholder="Middlename" name="middlename" id="middlename"/>
                </div>

                <label>Contact No.<span class="required"></span></label>
                <input type="text" class="form-control" name="contactno" id="contactno" placeholder="0919xxxx .. "  style="text-transform: uppercase">


            </div>

            <div class="col-md-8">

                <h4 class="text-primary text-bold">Job Order Type <span class="required"></span></h4>
                <input class="table-group-action-input form-control" name="joborder" id="select2joborder"/>

                <h4 class="text-primary text-bold">Account Details <span class="required"></span></h4>

                <div class="well">

                    <ul class="list-group summary column no-border">

                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Search Account:</span>
                            <span class="col-md-8 " style="margin: 0px 0px !important; padding: 0px 0px !important;" id="">
                            <div class="input-icon">
                                <i class="fa fa-search" style="margin-top: 5px !important; margin-left: 3px !important;"></i>
                                <input required style="padding-left: 30px !important; padding-top: 0px !important; margin-top: -5px !important;" class="form-control inline" placeholder="Account Search.. " name="acctsearch" id="acctsearch" autocomplete="off"/>
                            </div>
                            <input required type="hidden" name="acctid" id="acctid" autocomplete="off"/>
                        </span>
                        </li>

                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Name:</span>
                            <span class="col-md-8 label-default" id="jo_acct_name">N/A</span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Address:</span>
                            <span class="col-md-8 label-default" id="jo_acct_addr">N/A</span>
                        </li>

                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Meter No.</span>
                            <span class="col-md-8 label-default" id="jo_acct_mtrno">N/A</span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-4 label-name">Serial</span>
                            <span class="col-md-8 label-default" id="jo_acct_serial">N/A</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>


    <div class="modal-footer">
        <button id="submit_btn" type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save Order</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>

</form>

<script>
    PECO.joinit(<?php echo $view;?>);
</script>