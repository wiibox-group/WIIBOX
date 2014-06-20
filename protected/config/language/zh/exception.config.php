<?php /**
 * user  用于处理跟用户相关类异常
 * email 用于处理跟邮箱一类异常
 * sql   用于数据库一类异常
 * data  用于数据一类异常
 * sys   系统异常
 * frame 框架内特有异常
 * model model层里面特有异常
 */
return array(
			'scrypt_setting_haveNullData' => 'SCRYPT配置不能有空数据！',
			'sha_setting_haveNullData' => 'SHA配置不能有空数据！',
			'miningMachine_found_faild'=> '我不是你要找的矿机！',
			'sys_error' => '系统错误',
			'bound_cancel_faild' => '取消绑定失败,请重试',
			'bound_cancel_success' => '取消绑定成功，请重新扫描绑定！',
			'version_upgrad_withoutUpgrad' => '当前版本无需升级！',
			'version_upgrad_upgradFaild' => '升级失败，参数不正确！',
			'havaWrong' => '出错了！',
			'view_notFound' => '找不到部件视图:',
		
			'exec_set_notSet' => '没有设置$_FILES的KEY',
			'exec_file_noSetloadSize' => '没有设置上传文件大小的最大值',
			'exec_file_noSetUploadType' => '没有设置允许上传的文件类型',
			'exec_file_noSetUploadFixedAds' => '没有设置文件的保存位置的固定部份',
			'exec_file_noSetUploadAds' => '没有设置文件的保存位置',
			'exec_file_sizeCannotExceed' => '上传的文件大小不能超过',
			'exec_upload_FileNotFound' => '没有找到上传的文件数据',
			'exec_file_fileIllegal' => '不合法的上传文件',
			'exec_sys_none' => '没有',
			'exec_file_sizeExceedMaxSize' => '文件大小超过了系统所允许上传的大小！',
			'exec_file_tooSmallFile' => '文件太小了！',
			'exec_file_of' => 'of',
			'exec_file_allowedTypes' => '不被允许上传的文件类型！允许上传的文件类型有:',
			'exec_file_unTomkdir' => '无法创建目录',
			'exec_upload_Failed' => '上传文件失败',
			'exec_file_nameNotNull' => '文件名不能为空！',
			'exec_file_banWrite' => '文件不可写！',
			'exec_file_banClose' => '文件不可用，无法关闭！',
			
			'exec_session_adsAddFaild' => '设置SESSION保存路径失败，该路径',
			'exec_session_errorAds' => '不是一个正确的路径',
);
