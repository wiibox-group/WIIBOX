<div class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo $this->createUrl( 'index/index' ); ?>">WIIBOX设置</a>
    </div>
    
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li<?php echo $this->id == 'index' ? ' class="active"' : '' ?>><a href="<?php echo $this->createUrl( 'index/index' ); ?>">设置中心</a></li>
        <li<?php echo $this->id == 'monitor' ? ' class="active"' : '' ?>><a href="<?php echo $this->createUrl( 'monitor/index' ); ?>">本地监控</a></li>
		<li><a target="_blank" href="http://www.wiibox.net">远程监控</a></li>
		<li><a href="/check/index.html"">自检</a></li>
      </ul>
	  <ul id="action-header" class="nav navbar-nav navbar-right">
	    <li><a href="javascript:;" id="action-restart">立即重启</a></li>
	    <?php/*<li><a href="javascript:;" id="action-run">正常运行</a></li>*/?>
	    <li><a href="javascript:;" id="action-super">正在运行</a></li>
	    <li><a href="javascript:;" id="action-stop">停止运行</a></li>
      </ul>
    </div>
  </div>
</div>
<div class="container">
