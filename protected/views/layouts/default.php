<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta name="Keywords" content="<?php echo $this->getSeoKeyword();?>" />
	<meta name="Description" content="<?php echo $this->getSeoDesc();?>" />
	<title><?php echo $this->getSeoTitle();?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl;?>/static/assets/style/style.min.css"/>
</head>

<body>
	<input type="hidden" id="urlConfig" data-nbt="<?php echo NBT_DEBUG ? 1 : 0;?>" data-path="<?php echo $this->baseUrl;?>" data-restart="<?php echo $this->createUrl( 'index/restart' ); ?>" data-restarttarget="<?php echo $this->createUrl( 'index/restartTarget' ); ?>" data-shutdown="<?php echo $this->createUrl( 'index/shutdown' ); ?>" data-supermodel="<?php echo $this->createUrl( 'index/mode' ); ?>" data-usbstate="<?php echo $this->createUrl( 'index/usbstate' ); ?>" data-usbset="<?php echo $this->createUrl( 'index/usbset' ); ?>" data-check="<?php echo $this->createUrl( 'index/check' ); ?>" >
	<script src="<?php echo $this->baseUrl;?>/static/libs/jquery/jquery-1.8.3.min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/handlebars/handlebars-v1.3.0.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/highcharts/highcharts.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/nprogress/nprogress.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/js/language/<?php echo CUtil::getLanguage();?>/base.config.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/js/base.js"></script>
	<?php if( NBT_DEBUG ):?>
	<script src="<?php echo $this->baseUrl;?>/static/libs/dump/dump.js"></script>
	<?php endif;?>
	<!--[if lt IE 9]>
	<script src="<?php echo $this->baseUrl;?>/staic/libs/html5shiv/html5shiv.min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/respond/respond.min.js"></script>
	<![endif]-->
	<?php include NBT_VIEW_PATH.'/layouts/_header.php';?>
	<div class="container">
		<?php $this->widget('EWidgetSessionTipMsg');?>
	</div>
	<?php echo $content;?>
	<?php include NBT_VIEW_PATH.'/layouts/_footer.php';?>
	<?php include NBT_VIEW_PATH.'/systems/debug.php';?>
</body>

</html>
