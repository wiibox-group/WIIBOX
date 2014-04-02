<?php
/**
 * swfupload上传部件
 *
 */
class CWidgetSwfUpload extends CWidget
{
	public $handleCallbackJsDir = "";
	public $filesize = "100 MB";
	public $filetypes = "*.*";
	public $filetypesDescription = "所有文件";
	public $fileUploadLimit = 1;
	public $fileQueueLimit = 1;
	public $postData = array();
	/**
	 * 初始化
	 *
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 运行
	 *
	 */
	public function run()
	{
		$aryData = array();
		$this->render( 'swfupload' , $aryData );
	}	
	
//end class	
}