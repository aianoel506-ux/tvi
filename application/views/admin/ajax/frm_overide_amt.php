<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<?php
$id = $ids = $this->input->post('ids');
$query = $this->db->select('c.sysid, c.appid, c.chargeid, c.moduleid, a.codes, a.descs, c.amt, c.vatamt, c.vattype, a.groups')
    ->from('application_customers_charges AS c')
    ->join('prime_chart_of_accounts AS a', 'a.sysid = c.chargeid')
    ->where(array('c.sysid' => $id, 'c.status' => 1))
    ->get()->row();

/*echo "<pre>";
print_r ($query);
echo "</pre>";*/
if ($query) {
    if ($query->vattype == 1) {
        $total = $query->amt;
    } else {
        $total = $query->amt + $query->vatamt;
    }
?>
<div class="modal-body">
        <h2><?php echo upper_ent_quotes($query->descs)?></h2>
        <div class="row">
            <div class="col-md-5">
                <div class="input-group">
                    <div class="input-group-addon">Current Amount</div>
                    <input type="number" class="form-control" value="<?php echo number_format($total,2)?>" readonly>
                </div>
            </div>
            <div class="col-md-7">
                <form id="frm_override_amt" action="<?php echo base_url();?>cad/overrideamt" method="post">
                    <input type="hidden" name="chargesid" value="<?php echo $id;?>">
                    <input type="hidden" name="appid" value="<?php echo $query->appid;?>">
                    <input type="hidden" name="chargeid" value="<?php echo $query->chargeid;?>">
                    <input type="hidden" name="moduleid" value="<?php echo $query->moduleid;?>">
                    <input type="hidden" name="oldamt"  value="<?php echo number_format($total,2)?>">
                    <div class="input-group">
                        <div class="input-group-addon">New Amount</div>
                        <input type="number" step="0.05" class="form-control" name="newamt">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>
    <script type="text/javascript">
        CAD.override(<?php echo $query->appid;?>,<?php echo $query->moduleid;?>);
    </script>

    <?php
}
?>

