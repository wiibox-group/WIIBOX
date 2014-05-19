<?php
/**
 * Monitor Controller
 * 
 * @author wengebin
 * @date 2013-12-24
 */
class MonitorController extends BaseController
{
	/**
	 * init
	 */
	public function init()
	{
		parent::init();		
	}
	
	/**
	 * Index method
	 */
	public function actionIndex()
	{
		$this->replaceSeoTitle( CUtil::i18n( 'controllers,monitor_index_seoTitle' ) );

		$aryData = array();
		$this->render( 'index' , $aryData );
	}

//end class
}
