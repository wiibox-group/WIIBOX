<?php
/**
 * Login Controller
 * 
 * @author biallo
 * @date 2013-5-23
 */
class LoginController extends BaseController
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
	public function actionLogin()
	{
		$this->layout = 'login';
		$this->render( 'login');
	}

//end class
}
