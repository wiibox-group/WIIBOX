<?php
	$aryStatus = array( 'success'=>'alert-success' , 'warning'=>'alert-warning' , 'error'=>'alert-danger' );
?>
<div class="container page-index">
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
		<form class="form-setting" role="form" method="post" action="<?php echo $this->createUrl( 'index/index' ); ?>">
			<?php if ( !empty( $tip['status'] ) ) : ?>
			<div id="actionTip" class="alert <?php echo $aryStatus[$tip['status']]; ?> important-tip"><?php echo $tip['text']; ?></div>
			<?php endif; ?>
			<div class="form-title"><?php echo CUtil::i18n('vindex,sha_setting');?></div>
			<div class="form-group">
				<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,sha_MinePoolAddress')?>" name="address_btc" value="<?php echo $btc['ad']; ?>" type="text" <?php echo empty($btc['ad']) ? 'autofocus' : ''; ?>/>
			</div>
			<div class="form-group">
				<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,sha_workerNum')?>" name="account_btc" value="<?php echo $btc['ac']; ?>" type="text" />
			</div>
			<div class="form-group">
				<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,sha_workerPwd')?>" name="password_btc" value="<?php echo $btc['pw']; ?>" type="text" />
			</div>
			<div class="form-title">
				<div><?php echo CUtil::i18n('vindex,scrypt_setting'); ?></div>
			</div>
			<div class="form-group">
				<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,scrypt_MinePoolAddress'); ?>" name="address_ltc" value="<?php echo $ltc['ad']; ?>" type="text" />
			</div>
			<div class="form-group">
				<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,scrypt_workerNum'); ?>" name="account_ltc" value="<?php echo $ltc['ac']; ?>" type="text" />
			</div>
			<div class="form-group">
				<input class="form-control" placeholder="<?php echo CUtil::i18n('vindex,scrypt_workerPwd'); ?>" name="password_ltc" value="<?php echo $ltc['pw']; ?>" type="text" />
			</div>
			<div class="form-group">
				<input type="hidden" id="run_speed" name="run_speed" value="<?php echo $speed; ?>" />
				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					    <?php echo CUtil::i18n('vindex,runFrequency')?><span id="speed-cur"><?php echo $speed; ?></span>M
					    &nbsp;<span class="caret"></span>
					</button>
					<ul id="selectSpeed" class="dropdown-menu" role="menu">
						<li>
							<a data-value="700">700M</a>
						</li>
						<li>
							<a data-value="800">800M</a>
						</li>
						<li>
							<a data-value="850">850M</a>
						</li>
						<li>
							<a data-value="900">900M</a>
						</li>
						<li>
							<a data-value="950">950M</a>
						</li>
						<li>
							<a data-value="975">975M</a>
						</li>
						<li>
							<a data-value="1000">1000M</a>
						</li>
						<li>
							<a data-value="1025">1025M</a>
						</li>
						<li>
							<a data-value="1050">1050M</a>
						</li>
						<li>
							<a data-value="1100">1100M</a>
						</li>
						<li>
							<a data-value="1125">1125M</a>
						</li>
						<li>
							<a data-value="1150">1150M</a>
						</li>
						<li>
							<a data-value="1175">1175M</a>
						</li>
						<li>
							<a data-value="1200">1200M</a>
						</li>
					</ul>
				</div>
				<span>&nbsp;&nbsp;(<?php echo CUtil::i18n('vindex,frequencyTip');?>)</span>
			</div>
			<div class="alert alert-warning text-center">
		  		<strong><?php echo CUtil::i18n('vindex,importantOption');?></strong>
		  		&nbsp;&nbsp;&nbsp;&nbsp;
		  		<?php echo CUtil::i18n('vindex,setting_save_tip');?>
		  	</div>
			<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo CUtil::i18n('vindex,setting_save');?></button>
	    </form>
	</div>
</div>
