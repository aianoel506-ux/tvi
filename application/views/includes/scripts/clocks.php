<script src="<?php echo base_url().PATH_PLUGINS?>/jquery.clock/jquery.mydigitalclock.js"></script>
<script>
	$("#clock1").MyDigitClock(
		{
			fontSize: 20,
			fontFamily:"Century gothic", 
			fontColor: "#000",
			fontWeight:"bold", 
			background:'#fff',
			bAmPm:true,
			bShowHeartBeat:true
		}
	);
</script>