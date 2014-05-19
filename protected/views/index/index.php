	<?php
	$aryStatus = array( 'success'=>'alert-success' , 'warning'=>'alert-warning' , 'error'=>'alert-danger' );
	?>
	<div class="page-header">
		<h1><?php echo CUtil::i18n('vindex,setting_center');?><div class="pull-right">
		<h4><?php echo CUtil::i18n( 'vindex,this_version' ).CUR_VERSION; ?></h4></div></h1>
	</div>
	<div class="alert alert-warning">
			<?php echo CUtil::i18n('vindex,optimal_setting');?>
		<a target="_blank" href="<?php echo MAIN_DOMAIN; ?>/help#poolset">
			[<?php echo CUtil::i18n('vindex,why');?>]
		</a>&nbsp;&nbsp;&nbsp;
		<?php echo CUtil::i18n('vindex,worker_apart');?>
	</div>
	<div class="jumbotron">
	<form class="form-signin" role="form" method="POST" action="<?php echo $this->createUrl( 'index/index' ); ?>">
		<?php if ( !empty( $tip['status'] ) ) : ?>
		<div id="action-tip" class="alert <?php echo $aryStatus[$tip['status']]; ?> important-tip"><?php echo $tip['text']; ?></div>
		<script type="text/javascript">
			setTimeout(function(){
				$('#action-tip').hide();
			}, 5000);
		</script>
		<?php endif; ?>
		<div class="input-area">
			<div><?php echo CUtil::i18n('vindex,sha_setting');?></div>
			<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,sha_MinePoolAddress')?>" name="address_btc" value="<?php echo $btc['ad']; ?>" type="text" <?php echo empty($btc['ad']) ? 'autofocus' : ''; ?>/>
			<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,sha_workerNum')?>" name="account_btc" value="<?php echo $btc['ac']; ?>" type="text" />
			<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,sha_workerPwd')?>" name="password_btc" value="<?php echo $btc['pw']; ?>" type="text" />

			<div><?php echo CUtil::i18n('vindex,scrypt_setting'); ?></div>
			<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,scrypt_MinePoolAddress'); ?>" name="address_ltc" value="<?php echo $ltc['ad']; ?>" type="text" />
			<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,scrypt_workerNum'); ?>" name="account_ltc" value="<?php echo $ltc['ac']; ?>" type="text" />
			<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,scrypt_workerPwd'); ?>" name="password_ltc" value="<?php echo $ltc['pw']; ?>" type="text" />

			<input type="hidden" id="run_speed" name="run_speed" value="<?php echo $speed; ?>" />
			<div class="btn-group">
				<button type="button" class="btn btn-default"><?php echo CUtil::i18n('vindex,runFrequency')?> <span id="speed-cur"><?php echo $speed; ?></span>M</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul id="speed-select-options" class="dropdown-menu" role="menu">
					<li><a href="javascript:;">700</a></li>
					<li><a href="javascript:;">800</a></li>
					<li><a href="javascript:;">850</a></li>
					<li><a href="javascript:;">900</a></li>
					<li><a href="javascript:;">950</a></li>
					<li><a href="javascript:;">975</a></li>
					<li><a href="javascript:;">1000</a></li>
					<li><a href="javascript:;">1025</a></li>
					<li><a href="javascript:;">1050</a></li>
					<li><a href="javascript:;">1100</a></li>
					<li><a href="javascript:;">1125</a></li>
					<li><a href="javascript:;">1150</a></li>
					<li><a href="javascript:;">1175</a></li>
					<li><a href="javascript:;">1200</a></li>
				</ul>
			</div>
			<span style="font-size:15px;">&nbsp;&nbsp;(<?php echo CUtil::i18n('vindex,frequencyTip');?>)</span>
			<script type="text/javascript">
				$('#speed-select-options li a').click(function(){
					$('#speed-cur').html( $(this).html() );
					$('#run_speed').val( $(this).html() );
				}); 
			</script>
			<p>&nbsp;</p>

<?/*
			<div>挖矿模式</div>
			<div class="btn-group" style="padding-bottom:10px;">
				<input type="hidden" id="runmodel-input" name="runmodel" value="<?php echo $runmodel; ?>"/>
				<button type="button" tar="L" class="runmodel-bt btn btn-default<?php echo $runmodel === 'L' ? ' active' : '' ?>">SCRYPT单挖</button>
				<button type="button" tar="LB" class="runmodel-bt btn btn-default<?php echo $runmodel === 'LB' ? ' active' : '' ?>">SCRYPT/SHA双挖</button>
			</div>
*/?>
		</div>
		<p>
			<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo CUtil::i18n('vindex,setting_save');?></button>
		</p>
		<p>&nbsp;</p>
		<p>
		  <div id="action-restart-tip" class="alert alert-info important-tip">
		  	<strong>
		  		<?php echo CUtil::i18n('vindex,importantOption');?>
		  	</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo CUtil::i18n('vindex,setting_save_tip');?></div>
		  <button class="btn btn-lg btn-danger btn-block" onclick="actions.restart_home()" type="button" ><?php echo CUtil::i18n('vindex,restartProgram');?></button>
		</p>
    </form>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.runmodel-bt').click(function(){
				$('.runmodel-bt').removeClass( 'active' );
				$(this).addClass( 'active' );

				$('#runmodel-input').val( $(this).attr('tar') );
			});
		});
	</script>
