<?php
/**
 * 对话框部件
 *
 */
class CWidgetDialog extends CWidget
{
	public $id = "";
	public $triggerId = "";
	public $url = "";
	public $title="上传文件";
	public $intWidth = 400;
	public $intHeight = 100;
	
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
		if( empty( $this->id ) )
			throw new CException( "CWidget->id 未定义" );
		if( empty( $this->triggerId ) )
			throw new CException( "CWidget->triggerId 未定义" );
		if( empty( $this->url ) )
			throw new CException( "CWidget->url 未定义" );
			
		$aryData = array();
		$this->render( 'dialog' , $aryData );
	}	
}