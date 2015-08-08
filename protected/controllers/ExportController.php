<?php
/**
 * Export Controller
 * 
 * @author wengebin
 * @date 2015-08-08
 */
class ExportController extends BaseController
{
	/**
	 * init
	 */
	public function init()
	{
		parent::init();		
	}

	/**
	 * Export logs
	 */
	public function actionLog()
	{
		// 导出名
		$strExpName = isset( $_GET['exp'] ) ? $_GET['exp'] : 'nonm';

		$command = SUDO_COMMAND."/bin/bash ".WEB_ROOT."/shell/packagelogs.sh {$strExpName}";
		exec( $command , $log );

		// 导出文件路径
		$file = "/www/wiibox-log-{$strExpName}.zip";

		if ( file_exists( $file ) )
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			readfile($file);
		}
		else
		{
			echo "Error. Zip file not exists.";
		}
		exit;
	}
}
