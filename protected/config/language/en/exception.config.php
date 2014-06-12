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
			'scrypt_setting_haveNullData' => 'Null Data Is Not Allowed In SCRYPT Setting',
			'miningMachine_found_faild'=> 'Mining Machine Not Found!',
			'sys_error' => 'System Error',
			'bound_cancel_faild' => 'Failed To Cancel Bound,  Please Try Again',
			'bound_cancel_success' => 'Bound Canceled Successfully！',
			'version_upgrad_withoutUpgrad' => 'No Upgrade Need For Current Version!',
			'version_upgrad_upgradFaild' => 'Invalid Data, Upgrade Failed !',
			'havaWrong' => 'An Error Has Occurred!',
			'view_notFound' => 'Component View Not Found:',
		
			'exec_set_notSet' => 'Haven’t Set $_FILE KEY',
			'exec_file_noSetloadSize' => 'Haven’t Set The Uploading Maximum',
			'exec_file_noSetUploadType' => 'Haven’t Set The Uploading File Type That Allowed',
			'exec_file_noSetUploadFixedAds' => 'Haven’t Set The Fixed Saving Address Of  File Uploaded',
			'exec_file_noSetUploadAds' => 'Haven’t Set The File Saving Location',
			'exec_file_sizeCannotExceed' => 'File Size Must Keep Less Than',
			'exec_upload_FileNotFound' => 'File Not Found',
			'exec_file_fileIllegal' => 'Illegal Upload of File',
			'exec_sys_none' => 'none',
			'exec_file_sizeExceedMaxSize' => 'File Uploaded Exceed The Max Size',
			'exec_file_tooSmallFile' => 'File Size Is Too Small！',
			'exec_file_of' => 'of',
			'exec_file_allowedTypes' => 'The File Type Is Not Allowed to Upload, Allowable Type Includes:',
			'exec_file_unTomkdir' => 'Unable To Create Directory',
			'exec_upload_Failed' => 'Uploading File Failed',
			'exec_file_nameNotNull' => 'File Name Cannot Be Empty！',
			'exec_file_banWrite' => 'File Cannot Be Written！',
			'exec_file_banClose' => 'Invalid File, Cannot Be Closed！',
			
			'exec_session_adsAddFaild' => 'Failed To Set The Session Saving Path, The Path',
			'exec_session_errorAds' => 'Is Not A Right Path',
);