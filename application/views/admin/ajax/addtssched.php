<?php
/**
 * Created by PhpStorm.
 * User: IT
 * Date: 12/12/2018
 * Time: 10:31 AM
 */
?>
<div class="row" style="margin: 0px 10px 0px 10px">
    <form id="submittssched" action="<?php echo base_url() ?>ts/submitsched" method="post">

        <input type="hidden" id="month" name="month" />
        <input type="hidden" id="year" name="year" />
        <input type="hidden" id="type" name="type" />

        <div class="col-md-4">
            <div class="form-group">
                <label>Team</label>
                <input type="text" name="team" id="team" class="form-control" />
            </div>
            <div class="form-group">
                <label>Shift</label>
                <input type="text" name="shift" id="shift" class="form-control" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Monday</label>
                <input type="text" name="mon" id="mon" class="form-control" />
            </div>
            <div class="form-group">
                <label>Tuesday</label>
                <input type="text" name="tue" id="tue" class="form-control" />
            </div>
            <div class="form-group">
                <label>Wednesday</label>
                <input type="text" name="wed" id="wed" class="form-control" />
            </div>
            <div class="form-group">
                <label>Thursday</label>
                <input type="text" name="thu" id="thu" class="form-control" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Friday</label>
                <input type="text" name="fri" id="fri" class="form-control" />
            </div>
            <div class="form-group">
                <label>Saturday</label>
                <input type="text" name="sat" id="sat" class="form-control" />
            </div>
            <div class="form-group">
                <label>Sunday</label>
                <input type="text" name="sun" id="sun" class="form-control" />
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary pull-right">Save</button>
            </div>
        </div>
    </form>
</div>

<script>
    PECO.select2Basic($('#team' , document) , 'ts/getteams' , 'Select Team' , false,false,false);
    PECO.select2Basic($('#shift' , document) , 'ts/getshift' , 'Select Shift' , false,false,false);
    PECO.select2Basic($('#mon' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,false);
    PECO.select2Basic($('#tue' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,false);
    PECO.select2Basic($('#wed' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,false);
    PECO.select2Basic($('#thu' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,false);
    PECO.select2Basic($('#fri' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,false);
    PECO.select2Basic($('#sat' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,false);
    PECO.select2Basic($('#sun' , document) , 'ts/gettsemp' , 'Select Employee' , false,false,false);

    $('#month' , document).val($('#monthts' , document).val());
    $('#year' , document).val($('#yearts' , document).val());
    $('#type' , document).val($('#typets' , document).val());

</script>