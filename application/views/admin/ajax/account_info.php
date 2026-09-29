<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 11/22/2018
 * Time: 1:41 PM
 */

$id = $this->input->post('ids');
$acct_info = get_active_account_info($id);
/*
echo '<pre>';
print_r($acct_info);
echo '</pre>';
*/
if($acct_info) {
?>
<div style="padding: 10px 10px;">
    <div class="row">
        <div class="col-md-6">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-green-sharp">
                        <i class="icon-speech font-green-sharp"></i>
                        <span class="caption-subject bold uppercase"> Basic</span>
                        <span class="caption-helper">Customer account basic information</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <ul class="list-group summary column">
                        <li class="list-gorup-item">
                            <span class="col-md-4 label-name">Servno</span>
                            <span class="col-md-8 label-default"><?php echo $acct_info->servicenumber; ?></span>
                        </li>
                        <li class="list-gorup-item">
                            <span class="col-md-4 label-name">Name</span>
                            <span class="col-md-8 label-default"><?php echo $acct_info->name; ?></span>
                        </li>
                        <li class="list-gorup-item">
                            <span class="col-md-4 label-name">Address</span>
                            <span class="col-md-8 label-default"><?php echo $acct_info->address; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-green-sharp">
                        <i class="icon-speech font-green-sharp"></i>
                        <span class="caption-subject bold uppercase"> Basic</span>
                        <span class="caption-helper">Customer account basic information</span>
                    </div>
                </div>
                <div class="portlet-body">

                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>

