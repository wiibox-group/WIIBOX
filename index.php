<?php
error_reporting(E_ALL ^ E_NOTICE);
//check config.php
if( !file_exists( dirname(__FILE__).'/protected/config/define.php' ) )
{
	exit( 'confine/define.php is not existes.' );
}

require_once( dirname(__FILE__).'/protected/config/define.php' );
require_once( dirname(__FILE__).'/protected/config/version.php' );
require_once( dirname(__FILE__).'/protected/config/version_num.php' );

// store session to redis
//ini_set("session.save_handler","redis");
//ini_set("session.save_path","tcp://".REDIS_CONNECT_ADD.":".REDIS_CONNECT_PORT);

// change the following paths if necessary
$nbt=dirname(__FILE__).'/protected/framework/Nbt.php';
$config=dirname(__FILE__).'/protected/config/main.php';
defined('NBT_DEBUG') or define('NBT_DEBUG',true);
defined('TEST_MODE') or define('TEST_MODE',true);
require_once($nbt);
Nbt::createWebApplication($config)->run();
