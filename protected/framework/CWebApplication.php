<?php
/**
 * CWebApplication class file.
 * 
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-12
 */

class CWebApplication extends CApplication
{
	/** 默认控制器 */
	public $defaultController = 'index';
	/** 默认动作 */
	public $defaultAction = 'index';
	/** 基本URL */
	private $baseUrl;
	/** 路由控制器对象 */
	private $urlManager;
	/** 控制器集合 */
	public $controllerMap=array();
	/** 控制器对应路径 */
	private $_controllerPath;
	
	/**
	 * 获得路由控制器
	 */
	public function getUrlManager()
	{
		if ( !isset( $this->urlManager ) ) $this->urlManager = new CUrlManager();
		return $this->urlManager;
	}

	/**
	 * Processes the current request.
	 * It first resolves the request into controller and action,
	 * and then creates the controller to perform the action.
	 */
	public function processRequest()
	{
		$urlManager = $this->getUrlManager();
		// 开始解析，将匹配模式设置为 path
		$urlManager->setUrlFormat( CUrlManager::PATH_FORMAT );
		// 解析 URL
		$route=$urlManager->parseUrl($this->getRequest());
		// 根据路由运行控制器
		$this->runController( $route );
	}

	
	/**
	 * Initializes the application.
	 * This method overrides the parent implementation by preloading the 'request' component.
	 */
	public function init()
	{
		parent::init();
		$this->setBaseUrl( Nbt::app()->request->baseUrl );
	}
	
	/**
	 * run controller
	 */
	public function runController( $_strRoute )
	{
		// 根据路由创建控制器对象
		if( ( $ca = $this->createController( $_strRoute ) ) !== null )
		{
			// 从结果获得参数
			list( $controller , $actionID , $controllerName ) = $ca;

			// 防止因为 actionID 无效而导致的错误
			if ( empty( $actionID ) )
				$actionID = 'index';

			// 设置对应参数
			$action = 'action'.ucfirst( $actionID );
			$controller->setId( $controllerName );
			$controller->setActionId( $actionID );			
			if( !method_exists( $controller , $action ) )
				throw new CHttpException( 404 , get_class($controller)." have not defined function {$action}" );

			// 预处理
			$controller->beforeAction();
			// 执行 Controller
			$controller->$action();
			// 执行后处理
			$controller->afterAction();
		}
		else
		{
			throw new CHttpException( 404 , "have not defined route {$_strRoute}" );
		}
	}

	/**
	 * 根据路由创建控制器
	 */
	public function createController($route,$owner=null)
	{
		// 当前控制器触发应用
		if($owner===null)
			$owner = $this;

		// 获得默认控制器
		if(($route=trim($route,'/'))==='')
			$route = $owner->defaultController;

		// 是否严格区分大小写
		$caseSensitive=$this->getUrlManager()->caseSensitive;

		$route.='/';
		// 处理路由
		while(($pos = strpos($route,'/')) !== false)
		{
			$id = substr($route , 0 , $pos);
			if(!preg_match('/^\w+$/',$id))
				return null;
			
			if(!$caseSensitive)
				$id=strtolower($id);

			$route = (string)substr($route,$pos+1);
			if(!isset($basePath))  // first segment
			{
				if(isset($owner->controllerMap[$id]))
				{
					return array(
						Nbt::createComponent($owner->controllerMap[$id],$id,$owner===$this?null:$owner),
						$this->parseActionParams($route),
						$id,
					);
				}

				$basePath=$owner->getControllerPath();
				$controllerID='';
			}
			else
			{
				$controllerID.='/';
			}

			$className=ucfirst($id).'Controller';
			$classFile=$basePath.DIRECTORY_SEPARATOR.$className.'.php';

			if(is_file($classFile))
			{
				if(!class_exists($className,false))
					require($classFile);
				if(class_exists($className,false) && is_subclass_of($className,'CController'))
				{
					$id[0]=strtolower($id[0]);
					return array(
						new $className($controllerID.$id,$owner===$this?null:$owner),
						$this->parseActionParams($route),
						$id,
					);
				}
				return null;
			}
			$controllerID.=$id;
			$basePath.=DIRECTORY_SEPARATOR.$id;
		}
	}

	/**
	 * 获得 controllers 目标位置
	 *
	 * @return string
	 */
	public function getControllerPath()
	{
		if($this->_controllerPath!==null)
			return $this->_controllerPath;
		else
			return $this->_controllerPath=dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'controllers';
	}

	/**
	 * 解析请求参数
	 *
	 * @param string $pathInfo 路径信息
	 * @return string
	 */
	protected function parseActionParams($pathInfo)
	{
		if(($pos=strpos($pathInfo,'/'))!==false)
		{
			$manager=$this->getUrlManager();
			$manager->parsePathInfo((string)substr($pathInfo,$pos+1));
			$actionID=substr($pathInfo,0,$pos);
			return $manager->caseSensitive ? $actionID : strtolower($actionID);
		}
		else
			return $pathInfo;
	}
	
	/**
	 * 创建一个 URL 地址
	 * 
	 * @return string
	 */
	public function createUrl( $_route = null , $_aryParams = array() , $_ampersand='&' )
	{
		$_route = ($_route === null) ? "{$this->defaultController}/{$this->defaultAction}" : $_route;
		
		$url = "";
		//是否进行地址重写，生成静态地址
		//$url = REWRITE_MODE === true ? UtilUrl::createStaticUrl( $_route , $_aryParams ) : '';
		if ( REWRITE_MODE === true )
			$url = $this->getUrlManager()->createUrl( $_route , $_aryParams );

		//没有静态地址，则生成动态地址
		if( empty( $url ) )
			$url = $this->createDynamicUrl( $_route , $_aryParams , $_ampersand );

		return $url;
	}
	
	/**
	 * 创建动态url地址
	 *
	 * @param string $_route	路由
	 * @param array $_aryParams	参数=>值
	 * @param string $_ampersand	url参数之间的边接符
	 * @return string
	 */
	public function createDynamicUrl( $_route = null , $_aryParams = array() , $_ampersand='&' )
	{
		$_route = ($_route === null) ? "{$this->defaultController}/{$this->defaultAction}" : $_route;
		$routeName = $this->getRequest()->routeName;
		$url = $this->getBaseUrl()."/index.php?{$routeName}={$_route}";		
		foreach( (array)$_aryParams as $k=>$v )
		{
			$url .= "{$_ampersand}{$k}={$v}";
		}
		return $url; 
	}
	
	/**
	 * create absolute url contains www
	 * 
	 * @return string
	 */
	public function createAbsoluteUrl( $_route = "" , $_aryParams = array() ,$_schema='',$_ampersand='&' )
	{
		return Nbt::app()->getRequest()->getHostInfo($_schema).$this->createUrl( $_route , $_aryParams , $_ampersand );
	}
	
	/**
	 * 设置网站相对路径
	 *
	 * @param string $_baseUrl
	 */
	public function setBaseUrl( $_baseUrl )
	{
		$this->baseUrl = $_baseUrl;
	}
	
	/**
	 * 获取网站相对路径
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}
	
	/**
	 * 获取session对像
	 *
	 * @return CHttpSession
	 */
	public function getSession()
	{
		if( !isset( $this->components['session'] ) )
		{
			$session = new CHttpSession();
			$session->init();
			$this->components['session'] = $session;
		}
		return $this->components['session'];
	}
	
//end class
}
