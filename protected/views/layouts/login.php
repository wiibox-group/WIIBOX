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
	<input type="hidden" id="i18n" value="<?php echo CUtil::getLanguage();?>">
	<?php $this->widget('EWidgetSessionTipMsg'); ?>
	<?php echo $content;?>
	<script src="<?php echo $this->baseUrl;?>/static/libs/jquery/jquery-1.8.3.min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/handlebars/handlebars-v1.3.0.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/libs/jquery-html5Validate/jquery-html5Validate-min.js"></script>
	<script src="<?php echo $this->baseUrl;?>/static/js/login.js"></script>
</body>

</html>
