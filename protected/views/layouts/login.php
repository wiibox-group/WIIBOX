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

<body class="body-login">
	<input type="hidden" id="i18n" value="<?php echo CUtil::getLanguage();?>">
	<div class="container tip-msg">
		<?php $this->widget('EWidgetSessionTipMsg'); ?>
	</div>
	<?php echo $content;?>

	<script src="<?php echo $this->baseUrl;?>/static/js/login.min.js"></script>

</body>

</html>
