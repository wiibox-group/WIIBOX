<?php
	// 状态
	$aryStatus = array( 'success'=>'alert-success' , 'warning'=>'alert-warning' , 'error'=>'alert-danger' );
	// 运行模式
	$strRunMode = RunModel::model()->getRunMode();
?>
<div class="container page-index">
	<div class="page-header">
		<h1><?php echo CUtil::i18n('vindex,setting_center');?><div class="pull-right">
		<h4><?php echo CUtil::i18n( 'vindex,this_version' ).CUR_VERSION_NUM; ?></h4></div></h1>
	</div>
	<div class="jumbotron">
		<form class="form-setting" role="form" method="post" action="<?php echo $this->createUrl( 'index/index' ); ?>">
			<?php if ( !empty( $tip['status'] ) ) : ?>
			<div id="actionTip" class="alert <?php echo $aryStatus[$tip['status']]; ?> important-tip"><?php echo $tip['text']; ?></div>
			<?php endif; ?>
			<?php if ( $strRunMode === 'B' || $strRunMode === 'LB' ) : ?>
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
			<?php if ( count( $aryBTLSpeed ) ) : ?>
			<div class="form-group">
				<input type="hidden" id="run_speed_btc" name="run_speed_btc" value="<?php echo $speedBTC; ?>" />
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown">
					    <?php echo CUtil::i18n('vindex,runFrequency')?><span id="speed-cur"><?php echo $speedBTC; ?></span>M
					    &nbsp;<span class="caret"></span>
					</button>
					<ul id="selectSpeed" class="dropdown-menu" role="menu">
						<?php foreach ( $aryBTCSpeed as $intSpeed ) { ?>
						<li>
							<a data-value="<?php echo $intSpeed ?>"><?php echo $intSpeed ?>M</a>
						</li>
						<?php } ?>
					</ul>
				</div>
				<span>&nbsp;&nbsp;(<?php echo CUtil::i18n('vindex,frequencyTip');?><?php echo $speedDefBTC; ?>M)</span>
			</div>
			<?php endif; ?>
			<?php endif; ?>
			<?php if ( $strRunMode === 'L' || $strRunMode === 'LB' ) : ?>
			<div class="form-title">
				<div><?php echo CUtil::i18n('vindex,scrypt_setting'); ?></div>
			</div>
			<div class="form-group">
				<input class="form-control input-lg" placeholder="<?php echo CUtil::i18n('vindex,scrypt_MinePoolAddress'); ?>" name="address_ltc" value="<?php echo $ltc['ad']; ?>" type="text" />
			</div>
			<div class="form-group">
				<input class="form-control input-lg" placeholder="<?php echo CUtil::i18n('vindex,scrypt_workerNum'); ?>" name="account_ltc" value="<?php echo $ltc['ac']; ?>" type="text" />
			</div>
			<div class="form-group">
				<input class="form-control input-lg" placeholder="<?php echo CUtil::i18n('vindex,scrypt_workerPwd'); ?>" name="password_ltc" value="<?php echo $ltc['pw']; ?>" type="text" />
			</div>
			<?php if ( count( $aryLTCSpeed ) ) : ?>
			<div class="form-group">
				<input type="hidden" id="run_speed_ltc" name="run_speed_ltc" value="<?php echo $speedLTC; ?>" />
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown">
					    <?php echo CUtil::i18n('vindex,runFrequency')?><span id="speed-cur"><?php echo $speedLTC; ?></span>M
					    &nbsp;<span class="caret"></span>
					</button>
					<ul id="selectSpeed" class="dropdown-menu" role="menu">
						<?php foreach ( $aryLTCSpeed as $intSpeed ) { ?>
						<li>
							<a data-value="<?php echo $intSpeed ?>"><?php echo $intSpeed ?>M</a>
						</li>
						<?php } ?>
					</ul>
				</div>
				<span>&nbsp;&nbsp;(<?php echo CUtil::i18n('vindex,frequencyTip');?><?php echo $speedDefLTC; ?>M)</span>
			</div>
			<?php endif; ?>
			<?php endif; ?>
			<div class="alert alert-warning text-center">
		  		<strong><?php echo CUtil::i18n('vindex,importantOption');?></strong>
		  		&nbsp;&nbsp;&nbsp;&nbsp;
		  		<?php echo CUtil::i18n('vindex,setting_save_tip');?>
		  	</div>
			<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo CUtil::i18n('vindex,setting_save');?></button>
	    </form>
	</div>
</div>
