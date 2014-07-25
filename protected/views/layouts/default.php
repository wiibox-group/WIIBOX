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
	<!--[if lt IE 9]>
	<script src="<?php echo $this->baseUrl;?>/staic/libs/html5shiv/html5shiv.min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/respond/respond.min.js"></script>
	<![endif]-->
</head>

<body>
	<?php include NBT_VIEW_PATH.'/layouts/_header.php';?>
	<div class="container">
		<?php $this->widget('EWidgetSessionTipMsg');?>
	</div>
	<?php echo $content;?>
	<?php include NBT_VIEW_PATH.'/layouts/_footer.php';?>
	<?php include NBT_VIEW_PATH.'/systems/debug.php';?>
	<input type="hidden" id="urlConfig" data-restart="<?php echo $this->createUrl( 'index/restart' ); ?>" data-check="<?php echo $this->createUrl( 'index/check' ); ?>" >

	<script src="<?php echo $this->baseUrl;?>/static/js/language/<?php echo CUtil::getLanguage();?>/base.config.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/js/libs.min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/js/script.min.js"></script>

</body>

</html>
