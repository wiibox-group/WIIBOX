<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="Keywords" content="<?php echo $this->getSeoKeyword();?>" />
<meta name="Description" content="<?php echo $this->getSeoDesc();?>" />
<title><?php echo $this->getSeoTitle();?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl;?>/css/bootstrap.min.css"/>
<?php/*<link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl;?>/css/bootstrap-theme.min.css"/>*/?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl;?>/css/index.css"/>
<script language="javascript" type="text/javascript">
	var NBT_DEBUG = <?php echo NBT_DEBUG ? 1 : 0;?>;
	var WEB_PATH = "<?php echo $this->baseUrl;?>";
	var set_url_restart = "<?php echo $this->createUrl( 'index/restart' ); ?>";
	var set_url_restarttarget = "<?php echo $this->createUrl( 'index/restartTarget' ); ?>";
	var set_url_shutdown = "<?php echo $this->createUrl( 'index/shutdown' ); ?>";
	var set_url_supermodel = "<?php echo $this->createUrl( 'index/mode' ); ?>";
	var set_url_usbstate = "<?php echo $this->createUrl( 'index/usbstate' ); ?>";
	var set_url_usbset = "<?php echo $this->createUrl( 'index/usbset' ); ?>";
	var set_url_check = "<?php echo $this->createUrl( 'index/check' ); ?>";
</script>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/bootstrap.min.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/language/<?php echo CUtil::getLanguage();?>/base.config.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/base.js"></script>
<?php if( NBT_DEBUG ):?>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/dump.js"></script>
<?php endif;?>
<!--[if lt IE 9]>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/html5shiv.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/respond.min.js"></script>
<![endif]-->
</head>

<body>
<?php include NBT_VIEW_PATH.'/layouts/_header.php';?>
<?php $this->widget('EWidgetSessionTipMsg'); ?>
<?php echo $content;?>	    	
<?php include NBT_VIEW_PATH.'/layouts/_footer.php';?>
<?php include NBT_VIEW_PATH.'/systems/debug.php';?>
</body>
</html>
