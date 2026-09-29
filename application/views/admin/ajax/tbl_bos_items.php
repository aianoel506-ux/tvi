<style>
    #items{
        width: 100% !important;
        z-index: 1 !important;
    }
    #submititemdetails{
       margin-left: 15px;
       margin-right: 15px;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: IT
 * Date: 10/24/2018
 * Time: 1:01 PM
 */
    $bositemid = $this->input->post('ids');
    $view = $this->input->post('view');
    $data_arr = explode('-', $view);
    $quarter = $data_arr[0];
    $bosid = $data_arr[1];
    $accountcodeid = $data_arr[2];

    $sql = $this->db->select("codes,descs")->from("prime_chart_of_accounts")
->where(array("status" => 1 , "sysid" => $accountcodeid))->get()->row();

    $getbosmaindesc = $this->db->select("btg.descs")->from("bos_transaction_main as btm")
->join("bos_transaction_group as btg","btm.groupid = btg.sysid","left")
->where(array("btm.sysid" => $bosid))->get()->row();
?>

    <div class="row">

        <div class="col-md-12 modalcontainer">
            <h5><span style="margin-left: 10px;" class="text-primary"><?php echo ($quarter != '') ?  $quarter.' : ' : ''; ?><?php echo ($getbosmaindesc) ? $getbosmaindesc->descs : 'N/A' ?></span> <label style="margin-right: 10px;" class="pull-right"><?php echo ($sql) ? $sql->codes.' - '.$sql->descs : ''; ?></label></h5>
            <hr>
            <form id="submititemdetails" action="<?php echo base_url('bos/submititemdetails'); ?>" method="post">
                <input type="hidden" name="bositemid" value="<?php echo $bositemid; ?>" />
                <table class="table table-bordered table-hover table-striped table-responsive tbl-sm" id="itemstable">
                    <thead>
                    <th></th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                    <tr>
                        <td width="15%">Add Items Here :</td>
                        <td width="100%"><input name="items" id="items" class="form-control inline" placeholder="Select Items" /></td>
                        <td width="18%"><input required name="quantity" class="form-control inline" placeholder="Quantity" /></td>
                        <td width="6%"><button type="submit" class="btn btn-primary "><i class="fa fa-save"></i> Add</button></td>

                    </tr>
                    </tfoot>
                </table>
            </form>
        </div>

    </div>
