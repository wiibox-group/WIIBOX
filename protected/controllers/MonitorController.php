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
		$this->replaceSeoTitle( 'WIIBOX 监控中心' );

		$aryData = array();
		$this->render( 'index' , $aryData );
	}

//end class
}
