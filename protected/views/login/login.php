<div class="page-login">
	<div class="form-holder">
		<form id="signinForm" class="form-signin" role="form" method="post" action="<?php echo $this -> createUrl('login/login')?>">
	        <h2 class="form-signin-heading">{{title}}</h2>
	        <input name="uname" type="text" class="form-control" placeholder="{{input1}}" value="<?php echo $aryData['uname']?>" required>
	        <input name="pwd" type="password" class="form-control" placeholder="{{input2}}" required>
	        <label class="checkbox">
	          	<input name="remember" type="checkbox" value="yes" checked="checked">{{remember}}
	        </label>
	        <button class="btn btn-lg btn-primary btn-block" type="submit">{{btn}}</button>
	    </form>
    </div>
    <img src="static/assets/img/doge.gif" class="doge">
    <!-- tooltip -->
	<div class="doge-tip">
	  <div class="doge-tip-arrow"></div>
	  <div class="doge-tip-inner"><i class="glyphicon glyphicon-hand-up"></i> Click~</div>
	</div>
	<div id="canvas-container"></div>
</div>