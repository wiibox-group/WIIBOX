<?php
return array(
	//routeName=>array('permission1','permission2','permission3','...'),//Allow permission
	'index/index'=>array(),//Allow all users
    
	'product/index'=>array('ch_product_list','ch_product_export'),
    'product/add'=>array('ch_product_add'),
    'product/editinfophp'=>array('ch_product_edit'),
    'product/deletebyid'=>array('ch_product_del'),
	'product/addtoblack'=>array('ch_product_blacklist_add','ch_product_blacklist_del'),
    'product/selectstyle'=>array(),
    'product/showotherprice'=>array(),
    'product/lockprice'=>array('ch_product_lockprice_edit'),

    'usercustom/*'=>array('ch_product_tablestyle'),
    'productLock/index'=>array('ch_product_lockprice_list'),
    'productLock/delete'=>array('ch_product_lockprice_del'),
    
	'blacklist/index'=>array('ch_product_blacklist_list'),    
    'blacklist/delete'=>array('ch_product_blacklist_del'),
    
    'channel/update'=>array('ch_channel_edit'),
    'channel/index'=>array('ch_channel_list'),
    'channel/create'=>array('ch_channel_add'),
    //run and update function is not finish yet
    'datafeed/run'=>array('ch_datafeed_run'),
	'datafeed/index'=>array('ch_datafeed_run','ch_datafeed_runlist','ch_datafeed_upload'),
	'datafeed/help'=>array('ch_datafeed_run','ch_datafeed_runlist','ch_datafeed_upload'),
    'datafeed/runList'=>array('ch_datafeed_runlist'),
    'datafeed/upload'=>array('ch_datafeed_upload'),
    
    'datafeedHistory/index'=>array('ch_datafeed_history'),
    'datafeedHistory/downloadlist'=>array('ch_datafeed_download'),
    'datafeedHistory/download'=>array('ch_datafeed_download'),
    
     'importpro/index'=>array(),
     'importpro/getCats'=>array(),
     'importpro/getPro'=>array(),
     'importpro/addPro'=>array('ch_product_add'),
    
      
    'log/index'=>array('ch_system_log'),
    'log/export'=>array('ch_system_log'),
    
    'login/index'=>array(),
    'login/logout'=>array(),
    
    'systemErrorCode/index'=>array('ch_system_errorcode'),
);
