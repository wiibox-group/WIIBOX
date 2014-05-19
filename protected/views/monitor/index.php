  <div class="page-header">
	<h1><?php echo CUtil::i18n('vmonitor,sha_runStatus');?>
		<div class="pull-right">
			<h4>
				<?php echo CUtil::i18n('vmonitor,this_version').CUR_VERSION; ?>
			</h4>
		</div>
	</h1>
  </div>
  <div id="btc-machine-container" class="row"></div>

  <div class="page-header">
	<h1><?php echo CUtil::i18n('vmonitor,scrypt_runStatus');?></h1>
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
