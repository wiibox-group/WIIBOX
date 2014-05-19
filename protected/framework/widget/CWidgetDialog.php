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
	public $title = '';
	public $intWidth = 400;
	public $intHeight = 100;
	
	/**
	 * 初始化
	 *
	 */
	public function init()
	{
		parent::init();
		$this -> title = CUtil::i18n('framework,cwidgetDialog_init_title');
	}
	
	/**
	 * 运行
	 *
	 */
	public function run()
	{
		if( empty( $this->id ) )
			throw new CException( "CWidget->id ".CUtil::i18n('framework,cwidgetDialog_run_undefined'));
		if( empty( $this->triggerId ) )
			throw new CException( "CWidget->triggerId ".CUtil::i18n('framework,cwidgetDialog_run_undefined'));
		if( empty( $this->url ) )
			throw new CException( "CWidget->url ".CUtil::i18n('framework,cwidgetDialog_run_undefined'));
			
		$aryData = array();
		$this->render( 'dialog' , $aryData );
	}	
}