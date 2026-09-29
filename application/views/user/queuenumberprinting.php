<div class="container">
    <div class="row" class="printingBoxRow">
        <?php
            foreach($queboxarr->result() as $row){
         ?>
                <div class="col-md-6" >
                    <a  class="dashboard-stat blue-madison box_print">
                        <div class="visual">
                            <i class="fa <?php echo $row->icon ?>"></i>
                        </div>
                        <div class="details">
                            <div class="number">

                            </div>
                            <div class="descBox">
                               <?php echo  $row->names; ?>
                            </div>
                        </div>
                    </a>
                </div>
        <?php
            }
        ?>
    </div>
</div>

<script>

    $(document).on('click','.box_print',function () {
        var name = $(this).text();

        $.ajax({
            url:base_url+"user/getNumberToPrint",
            type:"POST",
            dataType:"json",
            success:function(data){
                alert(name);
                alert(JSON.stringify(data[0].sysid));

            }
        });
    });
</script>