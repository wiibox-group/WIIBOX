<div class="container page-monitor">
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
</div>

<script src="/static/js/monitor.js"></script>