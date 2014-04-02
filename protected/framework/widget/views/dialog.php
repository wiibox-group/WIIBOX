<style>
	.dialog{width:400px;border:solid 1px #eee;;padding:2px;display:none;background:#eee;position:absolute;top:0px;left:0px;}
	.dialog .dialogTool{height:25px;line-height:25px;background-color:#CEEBBD;position:relative;}
	.dialog .dialogTool .dialogTitle{position:absolute;left:5px;top:1px;color:#296a03;font-weight:bold;}
	.dialog .dialogTool .dialogClose{position:absolute;width:20px;height:20px;right:3px;top:1px;color:#296a03;font-weight:bold;}
	.dialog .dialogContent{padding:15px 5px;text-center:center;}
</style>
<div id="<?php echo $this->id;?>" class="dialog" style="width:<?php echo $this->intWidth;?>px;">
	<div class="dialogTool">
		<div class="dialogTitle"><?php echo $this->title;?></div>
		<a href="javascript:void(0);" class="dialogClose" onclick="$(this).parent().parent().hide();">x</a>
	</div>
	<div class="dialogContent">
		<iframe src="<?php echo $this->url;?>" frameborder="0" style="width:<?php echo $this->intWidth-20;?>px;height:<?php echo $this->intHeight?>px;"></iframe>
	</div>
</div>
<script language="javascript">
	$(function(){
		//打开对话框
		$("#<?php echo $this->triggerId;?>").click(function(){
			var jsonOffset = $(this).offset();
			var jsonCss = {};
				jsonCss.top = parseInt(jsonOffset.top)+30;
				jsonCss.left = jsonOffset.left;
			
			$("#<?php echo $this->id;?>").css( jsonCss ).toggle();
		});
	});
</script>