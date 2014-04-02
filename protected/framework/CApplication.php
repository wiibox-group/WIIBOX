<?php
/**
 * CApplication class file.
 *
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-12
 */

abstract class CApplication extends CComponents
{
	/**
	 * @var string the application name. Defaults to 'My Application'.
	 */
	public $name='My Application';
	public $components = array();
	
	/**
	 * Processes the request.
	 * This is the place where the actual request processing work is done.
	 * Derived classes should override this method.
	 */
	abstract public function processRequest();

	/**
	 * Constructor.
	 * @param mixed application configuration.
	 * If a string, it is treated as the path of the file that contains the configuration;
	 * If an array, it is the actual configuration information.
	 * Please make sure you specify the {@link getBasePath basePath} property in the configuration,
	 * which should point to the directory containing all application logic, template and data.
	 * If not, the directory will be defaulted to 'protected'.
	 */
	public function __construct($config=null)
	{
		Nbt::setApplication($this);
		//session_start();
		$this->initSystemHandlers();	
		$this->init();
	}


	/**
	 * Runs the application.
	 * This method loads static application components. Derived classes usually overrides this
	 * method to do more application-specific tasks.
	 * Remember to call the parent implementation so that static application components are loaded.
	 */
	public function run()
	{
		//preload components.
		$this->getRequest();
		
		$this->processRequest();
	}
	
	/**
	 * init
	 * 
	 */
	public function init()
	{
		
	}
	
	/**
	 * Initializes the class autoloader and error handlers.
	 */
	protected function initSystemHandlers()
	{
		set_exception_handler(array($this,'handleException'));
		set_error_handler(array($this,'handleError'),error_reporting());			
	}
	
/**
	 * Handles uncaught PHP exceptions.
	 *
	 * This method is implemented as a PHP exception handler. It requires
	 * that constant YII_ENABLE_EXCEPTION_HANDLER be defined true.
	 *
	 * This method will first raise an {@link onException} event.
	 * If the exception is not handled by any event handler, it will call
	 * {@link getErrorHandler errorHandler} to process the exception.
	 *
	 * The application will be terminated by this method.
	 *
	 * @param Exception exception that is not caught
	 */
	public function handleException($exception)
	{
		// disable error capturing to avoid recursive errors
		restore_error_handler();
		restore_exception_handler();
		
		try
		{
			if( $exception instanceof CHttpException )
			{
				$this->displayExceptionPage( $exception->statusCode , $exception );
			}
			else
			{
				$this->displayExceptionPage( 'exception' , $exception  );
			}
		}
		catch( Exception $e )
		{
			$this->displayException( $e );
		}
		exit();
	}
	
	/**
	 * Handles PHP execution errors such as warnings, notices.
	 *
	 * This method is implemented as a PHP error handler. It requires
	 * that constant YII_ENABLE_ERROR_HANDLER be defined true.
	 *
	 * This method will first raise an {@link onError} event.
	 * If the error is not handled by any event handler, it will call
	 * {@link getErrorHandler errorHandler} to process the error.
	 *
	 * The application will be terminated by this method.
	 *
	 * @param integer the level of the error raised
	 * @param string the error message
	 * @param string the filename that the error was raised in
	 * @param integer the line number the error was raised at
	 */
	public function handleError($code,$message,$file,$line)
	{
		if($code & error_reporting())
		{
			restore_error_handler();
			restore_exception_handler();

			try
			{
				//$this->displayError($code,$message,$file,$line);
				$this->displayErrorPage( $code,$message,$file,$line );
			}
			catch(Exception $e)
			{
				$this->displayException($e);
			}
		}
	}
	
	/**
	 * Displays the captured PHP error.
	 * This method displays the error in HTML when there is
	 * no active error handler.
	 * @param integer error code
	 * @param string error message
	 * @param string error file
	 * @param string error line
	 */
	public function displayError($code,$message,$file,$line)
	{
		@include( NBT_VIEW_PATH.'/systems/error.php' );
		/*
		if(NBT_DEBUG)
		{
			echo "<h1>PHP Error [$code]</h1>\n";
			echo "<p>$message ($file:$line)</p>\n";
			echo '<pre>';
			debug_print_backtrace();
			echo '</pre>';
		}
		else
		{
			echo "<h1>PHP Error [$code]</h1>\n";
			echo "<p>$message</p>\n";
		}*/
	}

	/**
	 * Displays the uncaught PHP exception.
	 * This method displays the exception in HTML when there is
	 * no active error handler.
	 * @param Exception the uncaught exception
	 */
	public function displayException($exception)
	{
		@include( NBT_VIEW_PATH.'/systems/exception.php' );
		/*if(NBT_DEBUG)
		{
			echo '<h1>'.get_class($exception)."</h1>\n";
			echo '<p>'.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().')</p>';
			echo '<pre>'.$exception->getTraceAsString().'</pre>';
		}
		else
		{
			echo '<h1>'.get_class($exception)."</h1>\n";
			echo '<p>'.$exception->getMessage().'</p>';
		}*/
	}
	
	/**
	 * display exceptin page
	 * @param int $statuscode
	 * @param string $message
	 */
	public function displayExceptionPage( $statuscode , $exception )
	{
		if( $this instanceof CWebApplication )
		{
			include( NBT_APPLICATION_PATH."/views/systems/{$statuscode}.php" );
		}
		else
		{
			echo $exception->getMessage();
		}
		exit();
	}
	
	/**
	 * display error page.
	 * 
	 * @param unknown_type $code
	 * @param unknown_type $message
	 * @param unknown_type $file
	 * @param unknown_type $line
	 */
	public function displayErrorPage( $code , $message , $file , $line )
	{
		include( NBT_APPLICATION_PATH."/views/systems/error.php" );
		exit();
	}
	
	public function getComponent( $component )
	{
		if( !isset( $this->components[$component] ) )
		{
			//$this->components[$component] = new 
		}
		return $this->components[$component];
	}
	
	/**
	 * get CWebUser object
	 * 
	 */
	public function getUser()
	{
		if( !isset( $this->components['user'] ) )
		{
			$this->components['user'] = new CWebUser();
		}
		return $this->components['user'];
	}
	
	/**
	 * get CRequest object
	 * 
	 */
	public function getRequest()
	{
		if( !isset( $this->components['request'] ) )
		{
			$request = new CRequest();
			$request->init();
			$this->components['request'] = $request;
		}
		return $this->components['request'];
	}	
	
//end class
}
