<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo $this->createUrl( 'index/index' ); ?>">
				<?php echo CUtil::i18n('vlayout,wiibox_setting');?>
			</a>
		</div>

		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li <?php echo $this->id == 'index' ? ' class="active"' : '' ?>>
					<a href="<?php echo $this->createUrl( 'index/index' ); ?>">
						<?php echo CUtil::i18n('vlayout,setting_center');?>
					</a>
				</li>
				<li <?php echo $this->id == 'monitor' ? ' class="active"' : '' ?>>
					<a href="<?php echo $this->createUrl( 'monitor/index' ); ?>">
						<?php echo CUtil::i18n('vlayout,localMonitoring');?>
					</a>
				</li>
				<li>
					<a target="_blank" href="http://www.wiibox.net">
						<?php echo CUtil::i18n('vlayout,remoteMonitoring');?>
					</a>
				</li>
				<li>
					<a href="/check/index.html"">
						<?php echo CUtil::i18n('vlayout,selfTest');?>
					</a>
				</li>
			</ul>
			<ul id="action-header" class="nav navbar-nav navbar-right">
				<li>
					<a href="javascript:;" id="action-restart">
						<?php echo CUtil::i18n('vlayout,restartNow');?>
					</a>
				</li>
	    		<?php/*<li><a href="javascript:;" id="action-run">正常运行</a></li>*/?>
	    		<li>
	    			<a href="javascript:;" id="action-super">
	    				<?php echo CUtil::i18n('vlayout,running');?>
	    			</a>
	    		</li>
				<li>
					<a href="javascript:;" id="action-stop">
						<?php echo CUtil::i18n('vlayout,runningStop');?>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="container">