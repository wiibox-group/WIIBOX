<?php
/**
 * Nbt Class Files.
 * 
 * 
 * 
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-12
 */

date_default_timezone_set('Asia/Shanghai');

/**
 * Defines the debug mode.
 */
defined('NBT_DEBUG') or define('NBT_DEBUG',false);

/**
 * Defines the nbt framework installation path.
 */
defined('NBT_PATH') or define('NBT_PATH',dirname(__FILE__));
defined('NBT_APPLICATION_PATH') or define('NBT_APPLICATION_PATH',dirname(dirname(__FILE__)));
defined('NBT_APPLICATION_CONFIG_PATH') or define('NBT_APPLICATION_CONFIG_PATH',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config');
defined('NBT_APPLICATION_RUNTIME_PATH') or define('NBT_APPLICATION_RUNTIME_PATH',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'runtime');
defined('NBT_VIEW_PATH') or define('NBT_VIEW_PATH',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'views');

class Nbt
{
	
	private static $_app;
	public static $aryCollectSql;
	
	/**
	 * Creates a Web application instance.
	 * @param mixed application configuration.
	 * If a string, it is treated as the path of the file that contains the configuration;
	 * If an array, it is the actual configuration information.
	 * Please make sure you specify the {@link CApplication::basePath basePath} property in the configuration,
	 * which should point to the directory containing all application logic, template and data.
	 * If not, the directory will be defaulted to 'protected'.
	 */
	public static function createWebApplication($config=null)
	{
		return self::createApplication('CWebApplication',$config);
	}

	/**
	 * Creates a console application instance.
	 * @param mixed application configuration.
	 * If a string, it is treated as the path of the file that contains the configuration;
	 * If an array, it is the actual configuration information.
	 * Please make sure you specify the {@link CApplication::basePath basePath} property in the configuration,
	 * which should point to the directory containing all application logic, template and data.
	 * If not, the directory will be defaulted to 'protected'.
	 */
	public static function createConsoleApplication($config=null)
	{
		return self::createApplication('CConsoleApplication',$config);
	}

	/**
	 * Creates an application of the specified class.
	 * @param string the application class name
	 * @param mixed application configuration. This parameter will be passed as the parameter
	 * to the constructor of the application class.
	 * @return mixed the application instance
	 * @since 1.0.10
	 */
	public static function createApplication($class,$config=null)
	{
		return new $class($config);
	}

	/**
	 * @return CApplication the application singleton, null if the singleton has not been created yet.
	 */
	public static function app()
	{
		return self::$_app;
	}

	/**
	 * Stores the application instance in the class static member.
	 * This method helps implement a singleton pattern for CApplication.
	 * Repeated invocation of this method or the CApplication constructor
	 * will cause the throw of an exception.
	 * To retrieve the application instance, use {@link app()}.
	 * @param CApplication the application instance. If this is null, the existing
	 * application singleton will be removed.
	 * @throws CException if multiple application instances are registered.
	 */
	public static function setApplication($app)
	{
		if(self::$_app===null || $app===null)
			self::$_app=$app;
		else
			throw new CException( 'Nbt application can only be created once' );
	}
	
/**
	 * Class autoload loader.
	 * This method is provided to be invoked within an __autoload() magic method.
	 * @param string class name
	 * @return boolean whether the class has been loaded successfully
	 */
	public static function autoload($className)
	{
		include_once($className.'.php');		
		return class_exists($className,false) || interface_exists($className,false);
	}
	
	/**
	 * set include path
	 */
	public static function setIncludePath()
	{
		
		$aryIncludePath = explode( PATH_SEPARATOR , get_include_path() );
		$aryIncludePath[] = NBT_APPLICATION_PATH.DIRECTORY_SEPARATOR.'controllers';
		$aryIncludePath[] = NBT_APPLICATION_PATH.DIRECTORY_SEPARATOR.'models';
		$aryIncludePath[] = NBT_PATH.DIRECTORY_SEPARATOR.'db';
		$aryIncludePath[] = NBT_PATH.DIRECTORY_SEPARATOR.'helper';
		$aryIncludePath[] = NBT_PATH.DIRECTORY_SEPARATOR.'widget';		
		$aryIncludePath[] = NBT_APPLICATION_PATH.DIRECTORY_SEPARATOR.'config';
		$aryIncludePath[] = NBT_APPLICATION_PATH.DIRECTORY_SEPARATOR.'libs';
		$aryIncludePath[] = NBT_APPLICATION_PATH.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'widgets';
		set_include_path( implode( PATH_SEPARATOR , $aryIncludePath ) );
	}
	
	/**
	 * import import
	 * @param	path	$_path	Set Include Path
	 */
	public static function import( $_path )
	{
		$aryIncludePath = explode( PATH_SEPARATOR , get_include_path() );
		$_path = str_replace( '.' , DIRECTORY_SEPARATOR , $_path );
		$aryIncludePath[] = NBT_APPLICATION_PATH.DIRECTORY_SEPARATOR.$_path;
		set_include_path( implode( PATH_SEPARATOR , $aryIncludePath ) );
	}
		
//end class	
}

Nbt::setIncludePath();
spl_autoload_register(array('Nbt','autoload'));
