
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
            <table class="table table-bordered table-hover table-striped table-responsive tbl-md" id="itemstableapproval">
                <thead>
                <th></th>
                <th>Item</th>
                <th>Desc</th>
                <th>Quantity</th>
                <th>Date Created</th>
                </thead>
                <tbody>

                </tbody>
            </table>
    </div>
</div>

<script>


    var bositemid = "<?php echo $bositemid; ?>" ;
    var itemstableapproval = $(document).find('#itemstableapproval');



    $.ajax({
        url: PECO.base_url() + 'bos/getapprovalitems',
        type: 'post',
        data: {"bositemid" : bositemid},
        dataType: 'json',
        beforeSend: function() {
            itemstableapproval.dataTable().empty();
            PECO.DTphpLoading(itemstableapproval, 'Loading budgets...');
        }
    }).done(function(d) {

        itemstableapproval.dataTable().empty();
        itemstableapproval.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            aaData: d.list,
            bSort: true,
            pageLength: 10,
            saveState: true,
            order: [['1', 'asc']],
            aoColumns: [
                {"data": "num"},
                {"data": "names"},
                {"data": "desc"},
                {"data": "qty"},
                {"data": "datecreated"}
            ],
            "searchHighlight": true,
            "language": PECO.DTEmptyMessage('No budget found!')
        });
    }).fail(function() {
        PECO.phpError();
    });


</script>