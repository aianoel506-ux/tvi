<?php
/**
 * Created by PhpStorm.
 * User: IT
 * Date: 12/12/2018
 * Time: 11:20 AM
 */

$sysid =   $this->input->post("view");

$sql = $this->db->select("mon , tue ,wed,thu , fri , sat , sun")->from("emp_team_schedule")
->where(array("sysid" => $sysid))->get()->row();

$mon =  ($sql) ? $sql->mon : '';
$tue =  ($sql) ? $sql->tue : '';
$wed =  ($sql) ? $sql->wed : '';
$thu =  ($sql) ? $sql->thu : '';
$fri =  ($sql) ? $sql->fri : '';
$sat =  ($sql) ? $sql->sat : '';
$sun =  ($sql) ? $sql->sun : '';
?>

<div class="container">
    <form id="updatetssched" action="<?php echo base_url() ?>ts/updatetssched" method="post">
        <input type="hidden" name="sysid" value="<?php echo $sysid; ?>">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Monday</label>
                    <input type="text" name="mon" id="mon" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Tuesday</label>
                    <input type="text" name="tue" id="tue" class="form-control" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Wednesday</label>
                    <input type="text" name="wed" id="wed" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Thursday</label>
                    <input type="text" name="thu" id="thu" class="form-control" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Friday</label>
                    <input type="text" name="fri" id="fri" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Saturday</label>
                    <input type="text" name="sat" id="sat" class="form-control" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Sunday</label>
                    <input type="text" name="sun" id="sun" class="form-control" />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success pull-right">Update</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

    PECO.select2Basic($('#mon' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,<?php echo $mon; ?>);
    PECO.select2Basic($('#tue' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,<?php echo $tue; ?>);
    PECO.select2Basic($('#wed' , document) , 'ts/gettsemp' , 'Select Employee' , false,false, <?php echo $wed; ?>);
    PECO.select2Basic($('#thu' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,<?php echo $thu; ?>);
    PECO.select2Basic($('#fri' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,<?php echo $fri; ?>);
    PECO.select2Basic($('#sat' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,<?php echo $sat; ?>);
    PECO.select2Basic($('#sun' , document) , 'ts/gettsemp' , 'Select Employee', false,false,<?php echo $sun; ?>);
</script>