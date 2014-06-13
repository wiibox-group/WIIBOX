<div class="container page-monitor">

	<div class="page-header">
		<h1><?php echo CUtil::i18n('vmonitor,hashrate_title');?></h1>
	</div>
	<div class="hashrate" data-url="<?php echo $this -> createUrl('speed/speedData')?>"></div>
	
	<div class="page-header">
		<h1><?php echo CUtil::i18n('vmonitor,status_title');?>
			<div class="pull-right">
				<h4>
					<?php echo CUtil::i18n('vmonitor,this_version').CUR_VERSION; ?>
				</h4>
			</div>
		</h1>
	</div>
	<div class="table-responsive">
		<table id="statusTable" class="table table-hover table-bordered table-striped">
			<thead>
				<tr>
					<th><?php echo CUtil::i18n('vmonitor,status_device');?></th>
					<th><?php echo CUtil::i18n('vmonitor,status_state');?></th>
					<th><?php echo CUtil::i18n('vmonitor,status_algorithm');?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="3">loading...</td>
				</tr>
			</tbody>
		</table>
	</div>
	
</div>