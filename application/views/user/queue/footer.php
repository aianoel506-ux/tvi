
        </div>
    </div>
</div>



        <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/onscreenkeyboard-master/onscreenkeyboard-master/js/jsKeyboard.js"></script>

        <script type="text/javascript">

            $(function(){
                jsKeyboard.init("virtualKeyboard");

                //first input focus
                var $firstInput = $(':input').first().focus();
                jsKeyboard.currentElement = $firstInput;
                jsKeyboard.currentElementCursorPosition = 0;
            });
            $(document).on('click','#submitBtn',function(){
                var name = $('#nameTxt').val();
                var servNo = $('#serviceNo').val();
                var types = "<?php  echo $requesttype; ?>";

                if(name==="" || servNo===""){

                    $('#error_submit').text("Please fill up all the requried fields.").css("color","red");
                    $('#error_submit').show();
                    $('#error_submit').fadeOut(5000);
                }else{
                    $.ajax({
                        url:"<?php echo base_url('user/submitrequest'); ?>",
                        type:"POST",
                        data:{name:name,servNo:servNo,types:types},
                        success:function(){
                            $( "div.success" ).fadeIn( 300 ).delay( 1500 ).fadeOut(400);
                            window.location.href = "<?php echo site_url('user/querequest') ?>";
                        }
                    });
                }
            });
        </script>
