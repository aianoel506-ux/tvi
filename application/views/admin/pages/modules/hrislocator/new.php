<?php
    if(user_id() != 1) {

        $getempid = $this->db->select("pem.sysid")->from("prime_system_users as su")
        ->join("prime_employee_main as pem" ,"pem.personid = su.personid")
        ->where(array("pem.status" => 1 , "pem.type" => 1 , "su.status" => 1 , "su.sysid" => user_id()))
            ->get()->row();

        if($getempid){
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet">
                        <div class="portlet-title">
                            <div class="caption">
                                LOCATOR SLIP
                            </div>
                        </div>
                        <div class="portlet-body">
                            <form action="<?php echo base_url() ?>hris/submitlocator" id="submitlocatorslip" method="post">

                                <div class="form-group">
                                    <input name="locatordate" type="hidden" value="<?php echo date('Y-m-d'); ?>" class="form-control"/>
                                    <input name="empid" type="hidden" value="<?php echo $getempid->sysid; ?>" class="form-control"/>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Locator Type</label><br>
                                            <div>
                                                <input type="radio" id="personal"
                                                       name="locatortype" value="1" />
                                                <label for="personal">Personal</label>
                                            </div>

                                            <div>
                                                <input type="radio" id="company"
                                                       name="locatortype" value="2" />
                                                <label for="company">Company</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>For</label>
                                            <input name="locatorfor" type="text" class="form-control"/>
                                        </div>
                                        <div class="form-group">
                                            <label> The undersigned request to leave his/her post at</label>
                                            <input name="locatorbreakout" type="text" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group">
                                            <label> and will be back at</label>
                                            <input name="locatorbreakin" type="text" class="form-control"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Reason of leaving:</label>
                                            <textarea name="locatorreason" class="form-control" rows="5"
                                                      id="purpose"></textarea>
                                        </div>

                                        <div class="form-group pull-right">
                                            <button type="submit" class="btn btn-default btn-md"><i
                                                        class="fa fa-arrow-right"></i> Send for Approval
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script src="<?php echo base_url() ?>assets/pages/hris/locator.js"></script>

            <script>
                LOCATOR.init();
            </script>
            <?php
        }else{
            echo 'You dont have person ID. please contact your administrator.';
        }

    }else {
        echo 'You\'re not allowed to request locator.';
    }
?>