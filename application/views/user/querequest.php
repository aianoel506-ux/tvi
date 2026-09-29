

            <div class="col-md-10 queue-btn">

              <h4 class="titleClass">Request Categories </h4>

                <?php
                if($queboxarr->num_rows() > 0) {
                foreach($queboxarr->result() as $row) {
                ?>
                <div class="col-md-6">
                    <a class="dashboard-stat dashboard-stat-light green-soft requestBox" href="<?php echo base_url().'user/querequest/'.$row->sysid; ?>">

                        <div class="visual">
                            <i class="fa <?php echo $row->icon; ?>"></i>
                        </div>
                        <div class="details">
                            <div class="number">

                            </div>
                            <div class="desc" style="font-size: 25px;">
                                <?php echo $row->names; ?>
                            </div>
                        </div>
                    </a>
                </div>
                    <?php
                }

                }
                ?>
            </div>
