<?php
/**
 * This file defined some constant.
 * 
 * @author wengebin<wengebin@hotmail.com> 
 */
//网站根路径
define('WEB_ROOT',dirname(dirname(dirname(__FILE__))));

//主域名设置
define( 'MAIN_DOMAIN' , 'http://sync.wiibox.net' );
define( 'MAIN_DOMAIN_KEY' , '123qwe!@#' );

//是否开启地址重写
define( 'REWRITE_MODE' , false );
//开始运行时间(秒)
define( 'NBT_BEGIN_TIME' , time() );
//开始运行时间(微秒)
define( 'NBT_BEGIN_MICROTIME' , microtime(true) );

//邮件地址
define('MAIL_TO_WGB','wengebin@hotmail.com');

//Redis连接
define( 'REDIS_CONNECT_ADD' , '127.0.0.1' );
define( 'REDIS_CONNECT_PORT' , '6379' );
//Redis存储域，比如 www 站点存储为 www 域开头的 key 中
define( 'REDIS_DISTRICT_NAME' , 'www' );
//是否开启缓存
define( 'CACHE_STATUS' , true );

define( 'SUDO_COMMAND' , 'sudo ' );