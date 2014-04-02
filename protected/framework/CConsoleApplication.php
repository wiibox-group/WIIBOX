<?php
/**
 * CConsoleApplication class file.
 *
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-12
 */

class CConsoleApplication extends CApplication
{
	/**
	 * @return string the ID of the default controller. Defaults to 'site'.
	 */
	public $defaultController = 'datafeed';
	public $defaultAction = 'index';	
	
	/**
	 * Processes the current request.
	 * It first resolves the request into controller and action,
	 * and then creates the controller to perform the action.
	 */
	public function processRequest()
	{
		$aryParams = array();
		$argv = isset( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();
		if( count($argv) > 1 )
		{
			foreach( $argv as $arg )
			{
				$arg = explode('=',$arg);
				if( count($arg) == 2 )
				{
					$_GET[$arg[0]] = $aryParams[$arg[0]] = $arg[1];
				}
			}
		}
		$this->runController( $aryParams );
	}

	
	/**
	 * Initializes the application.
	 * This method overrides the parent implementation by preloading the 'request' component.
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * run controller
	 * 
	 */
	public function runController( $aryParams )
	{
		$controllerName = '';
		$actionName = '';
		$routeName = $this->getRequest()->routeName;
		$route = isset( $aryParams[$routeName] ) ? $aryParams[$routeName] : null;		
		$aryRoute = is_null( $route ) ? array() : explode( '/' , $route );
			
		switch( count( $aryRoute ) )
		{
			case 1:
				$controllerName = $aryRoute[0];
				$actionName = $this->defaultAction;
				break;
			case 2:
				$controllerName = $aryRoute[0];
				$actionName = $aryRoute[1];				
				break;
			case 0:
				$controllerName = $this->defaultController;
				$actionName = $this->defaultAction;
				break;
			default:
				throw new CException( 'Route name error.' );
		}		
		Nbt::app()->user->systemLogin();
		$controller = ucfirst( $controllerName ).'Controller';
		$action = 'action'.ucfirst( $actionName );
		$c = new $controller();
		if( method_exists( $c , $action ) )
		{
			$c->setId( $controllerName );
			$c->setActionId( $actionName );
			$c->beforeAction();
			$c->$action();
			$c->afterAction();
			Nbt::app()->user->logout();
		}
		else
		{
			throw new CHttpException( 404 , "{$controller} have not defined function {$action}" );
		}
	}
	
//end class
}
