<?php
class TestController extends BaseController 
{
	
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	public static function writeFile( $_address ,$value )
	{		
		$file = fopen( $_address , "a+");
		fwrite($file, $value);
		// 先把文件剪切12.    为0字节大小，13.     然后写入
		fclose($file);
	}
}
