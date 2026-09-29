<?php
/**
 * Created by PhpStorm.
 * User: FADERON
 * Date: 3/22/2018
 * Time: 8:59 AM
 */
?>
<input type="hidden" name="process" value="1" />
<input type="hidden" name="viewtype" value="1" />
<input type="hidden" name="class" value="<?php echo $payclass; ?>" />
<input type="hidden" name="moduleid" id="moduleid" value="<?php echo $navid; ?>" />

<div class="row margin-bottom-10">
    <div class="col-md-7">
        <div class="row">
            <div class="col-md-2">
                <small>Period Year</small>
                <input class="form-control input-sm" id="periodyear" name ="year" value="<?php echo date("Y"); ?>" />
            </div>
            <div class="col-md-3">
                <small>Period Month</small>
                <input class="form-control input-sm" id="select2month" name="month"  value="<?php echo (int) date('m'); ?>" />
            </div>
            <?php if($payclass==128) { ?>

            <div class="col-md-3">
                <small>Payment Type</small>
                <select required class="form-control input-sm" id="select2paytype" name="paytype">
                    <option></option>
                    <option value="1" selected>1st half</option>
                    <option value="2">2nd half</option>
                </select>
            </div>
            <?php } ?>
            <div class="col-md-4">
                <small>View by Department</small>
                <input class="form-control input-sm" id="deptselect" name="dept" placeholder="Select dept.." />
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="btn-group pull-right margin-top-20">
            <?php if(user_id() == 1) { ?>
          <!--  <button type="button" id="clearpayroll" data-module-id="<?php echo $navid; ?>" class="btn btn-default red-stripe" onclick=""><i class="fa fa-times fa-fw"></i> Clear</button> -->
            <?php } ?>
            <?php if(user_id() != 20) { ?>
            <!--<button type="button" id="printpayslips" class="btn btn-default blue-stripe" onclick=""><i class="fa fa-print fa-fw"></i> Print Payslips</button>-->
            <button type="submit" id="processpayroll" class="btn btn-primary" onclick=""><i class="fa fa-forward fa-fw"></i> Process Payroll</button>
            <?php } ?>
        </div>
    </div>
</div>
