  <div class="page-header">
	<h1>SHA 运行状态<div class="pull-right"><h4>当前版本：<?php echo CUR_VERSION; ?></h4></div></h1>
  </div>
  <div id="btc-machine-container" class="row"></div>

  <div class="page-header">
	<h1>SCRYPT 运行状态</h1>
  </div>
  <div id="ltc-machine-container" class="row"></div>
<script type="text/javascript">
var need_show_check_result = true;
function refreshState()
{
	if ( actions.setting.runstate === false ) actions.usbstate();
	//if ( actions.setting.runstate === false ) actions.check();
	setTimeout(function(){
		refreshState();
	},10000);
}
$(document).ready(function(){
	refreshState();
});
</script>
