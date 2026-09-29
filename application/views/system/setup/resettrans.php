<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 4/4/2019
 * Time: 10:29 AM
 */
$qry_erp_db_rows = $this->db->query("
SELECT SUM(TABLE_ROWS) AS rows
   FROM INFORMATION_SCHEMA.TABLES
   WHERE TABLE_SCHEMA = 'peco_erp';
   ")->row();
$erp_rows = ($qry_erp_db_rows) ? $qry_erp_db_rows->rows : 0;

$qry_erp_db_tables = $this->db->query("
SELECT count(*) AS tbl FROM information_schema.TABLES where TABLE_SCHEMA = 'peco_erp'
   ")->row();
$erp_tables = ($qry_erp_db_tables) ? $qry_erp_db_tables->tbl : 0;

$qry_erp_db_size = $this->db->query("SELECT
        ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS size 
FROM information_schema.tables
WHERE TABLE_SCHEMA = 'peco_erp'
GROUP BY table_schema;")->row();

if($qry_erp_db_size) {
    if($qry_erp_db_size->size >= 1000) {
        $new_erp_size = $qry_erp_db_size->size / 1000;
        $erp_size = number_format($new_erp_size, 2) . ' GB';
    }else{
        $erp_size = number_format($qry_erp_db_size->size, 2) . ' MB';
    }
}else{
    $erp_size = 0;
}
?>

<style>
    .form-control.code {
        padding: 0px 0px !important;
        font-size: 35px !important;
        font-weight: bold !important;
        text-transform: uppercase !important;
    }
</style>


<h3 class="font-green bold"><i class="icon-refresh font-red"></i> Reset Transaction Command
    <a class="pull-right" href="<?php echo base_url('setup/db'); ?>">Back</a>
</h3>
<hr>
<div class="well">
    <b>Note: </b>in this command, all transaction tables in this system will be wiped-out and data cannot be retrieved, SQL Command: TRUNCATE TABLE; please have a full backup of your database before the reset transaction command process.
</div>
<div class="row">
    <div class="col-md-4">
        <h4><i class="fa fa-table fa-fw"></i> ERP Database</h4>
        <ul class="list-group summary column ">
            <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                <span class="col-md-8 label-name">Tables</span>
                <span class="col-md-4 label-default number"><?php echo number_format($erp_tables);?></span>
            </li>
            <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                <span class="col-md-8 label-name">Records</span>
                <span class="col-md-4 label-default number"><?php echo number_format($erp_rows);?></span>
            </li>
            <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                <span class="col-md-8 label-name">Size</span>
                <span class="col-md-4 label-default number"><?php echo $erp_size; ?></span>
            </li>
        </ul>
        <h4><i class="fa fa-table fa-fw"></i> PECO.apps Database</h4>
        <ul class="list-group summary column ">
            <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                <span class="col-md-8 label-name">Tables</span>
                <span class="col-md-4 label-default number">300</span>
            </li>
            <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                <span class="col-md-8 label-name">Records</span>
                <span class="col-md-4 label-default number">35,329,232</span>
            </li>
            <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                <span class="col-md-8 label-name">Size</span>
                <span class="col-md-4 label-default number">42GB</span>
            </li>
        </ul>
    </div>
    <div class="col-md-8">
        <h4>Command Trigger</h4>

        <?php
        $exec = $this->input->get('exec');
        if($exec == true) {

        }else{
            ?>
            <form autocomplete="off" action="<?php echo base_url('setup/resettranssess'); ?>" method="post" id="frm_command_trans">
                <input type="hidden" name="userid" value="<?php echo user_id(); ?>" />

                <ul class="list-group summary column ">
                <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                    <span class="col-md-6">Email</span>
                    <span class="col-md-6"><input autocomplete="off" class="form-control inline" name="email" placeholder="Email" /></span>
                </li>
                <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                    <span class="col-md-6">Login password</span>
                    <span class="col-md-6"><input autocomplete="off" class="form-control inline" name="password" placeholder="Password" type="password" /></span>
                </li>
                <li class="list-group-item bg-grey-cararra bg-font-grey-cararra">
                    <span class="col-md-6">
                        <div class="g-recaptcha" data-sitekey="6Le6OpsUAAAAABHm4s74NsH8kNtdw-fVPz2JSgsE"></div>
                    </span>
                    <span class="col-md-6">
                        <button type="submit" class="btn btn-lg btn-default" style="height: 75px !important; display: inline-block !important;"><i class="fa fa-envelope-o"></i> Send</button>
                    </span>
                </li>
                </ul>
            </form>

            <ul class="list-group summary column ">
                <li class="list-group-item bg-grey-cararra bg-font-grey-cararra" id="exec_command"></li>
            </ul>
        <?php } ?>
        <hr>
        <code>Note: backup all database first before trigger the execute command!</code>
    </div>
</div>
<div style="display:inline-block; height: 50px; margin-top: 50px; background: url('<?php echo base_url('assets/global/img/caution_stripes_repeat.png'); ?>'); width: 100%;">


<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>
<script type="text/javascript" src='<?php echo base_url(); ?>assets/pages/syssetup.js'></script>

<script type="text/javascript">
    SYSSETUP.db();
</script>