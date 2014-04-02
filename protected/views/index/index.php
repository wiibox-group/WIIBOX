	<?php
	$aryStatus = array( 'success'=>'alert-success' , 'warning'=>'alert-warning' , 'error'=>'alert-danger' );
	?>
	<div class="page-header">
		<h1>Setting! 设置中心<div class="pull-right"><h4>当前版本：<?php echo CUR_VERSION; ?></h4></div></h1>
	</div>
	<div class="alert alert-warning">
		<strong>注意!</strong> 最佳设置是一个矿机对应一个矿工号！ <a target="_blank" href="<?php echo MAIN_DOMAIN; ?>/help#poolset">[为什么?]</a>&nbsp;&nbsp;&nbsp;多个矿工号请用 英文半角"," 隔开！
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
			<div>SHA设置 (不填写则不启动)</div>
			<input class="form-control" placeholder="SHA矿池地址" name="address_btc" value="<?php echo $btc['ad']; ?>" type="text" <?php echo empty($btc['ad']) ? 'autofocus' : ''; ?>/>
			<input class="form-control" placeholder="SHA矿工号 (一个即可，多个矿工默认第一个)" name="account_btc" value="<?php echo $btc['ac']; ?>" type="text" />
			<input class="form-control" placeholder="SHA统一矿工密码" name="password_btc" value="<?php echo $btc['pw']; ?>" type="text" />

			<div>SCRYPT设置 (不填写则不启动)</div>
			<input class="form-control" placeholder="SCRYPT矿池地址" name="address_ltc" value="<?php echo $ltc['ad']; ?>" type="text" />
			<input class="form-control" placeholder="SCRYPT矿工号 (多个请用 英文半角',' 隔开)" name="account_ltc" value="<?php echo $ltc['ac']; ?>" type="text" />
			<input class="form-control" placeholder="SCRYPT统一矿工密码" name="password_ltc" value="<?php echo $ltc['pw']; ?>" type="text" />

			<div>挖矿模式</div>
			<div class="btn-group" style="padding-bottom:10px;">
				<input type="hidden" id="runmodel-input" name="runmodel" value="<?php echo $runmodel; ?>"/>
				<button type="button" tar="L" class="runmodel-bt btn btn-default<?php echo $runmodel === 'L' ? ' active' : '' ?>">SCRYPT单挖</button>
				<button type="button" tar="LB" class="runmodel-bt btn btn-default<?php echo $runmodel === 'LB' ? ' active' : '' ?>">SCRYPT/SHA双挖</button>
			</div>
		</div>
		<p>
			<button class="btn btn-lg btn-primary btn-block" type="submit">保存设置</button>
		</p>
		<p>&nbsp;</p>
		<p>
		  <div id="action-restart-tip" class="alert alert-info important-tip"><strong>重要操作!</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;保存后请重启程序!</div>
		  <button class="btn btn-lg btn-danger btn-block" onclick="actions.restart_home()" type="button" >重启程序</button>
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
