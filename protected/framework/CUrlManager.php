<?php
/**
 * Url 路由控制器
 *
 * @author wengebin
 * @date 2013-10-23
 */
class CUrlManager extends CApplicationComponents
{
	/** 缓存名称 */
	const CACHE_KEY='url.rules';

	/** 两种匹配模式 */
	const GET_FORMAT='get';
	const PATH_FORMAT='path';

	/**
	 * 规则列表，目前从 config/UtilUrl.php 中获得
	 */
	public $rules=array();
	/**
	 * URL 后缀，比如填写 .html ，后缀会自动增加 .html
	 */
	public $urlSuffix='';
	/**
	 * 是否显示 index.php 路径，完全伪静态需要设置为 false
	 */
	public $showScriptName=false;
	/**
	 * 是否讲参数显示在 GET 请求中
	 */
	public $appendParams=true;
	/**
	 * 动态路径下，GET 请求识别 Controller 与 Action 的参数名
	 */
	public $routeVar='r';
	/**
	 * 路由是否严格区分大小写，默认为 true，严格区分！
	 */
	public $caseSensitive=true;
	/**
	 * 参数是否需要匹配特定值，默认为 false，如果设置为 true 会损耗性能
	 */
	public $matchValue=false;
	/**
	 * 是否需要缓存规则集，默认为 true，会进行缓存，但规则改变需要手动清除缓存
	 */
	public $cacheRules=false;
	/**
	 * 是否启用严格 URL 跳转，如果参数不能正常匹配，则返回 404 错误
	 */
	public $useStrictParsing=true;
	/**
	 * URL 生成、解析路由类，如果需要使用其他路由类解析，可进行配置
	 */
	public $urlRuleClass='CUrlRule';
	/**
	 * 默认匹配模式为 GET_FORMAT，可选还有 PATH_FORMAT
	 */
	private $_urlFormat=self::GET_FORMAT;
	/**
	 * 匹配规则对象集
	 */
	private $_rules=array();
	/**
	 * 基本 URL
	 */
	private $_baseUrl;


	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
		$this->processRules();
	}

	/**
	 * 初始化规则解析器
	 */
	protected function processRules()
	{
		// 如果允许缓存解析器，则从缓存中获得解析器缓存集合
		if ( $this->cacheRules === true )
		{
			//$cacheObj = new CRedis();
			//$data = $cacheObj->readByKey( self::CACHE_KEY );
		}
		
		// 如果缓存数据不为空，则直接使用缓存
		if ( !empty( $data ) )
		{
			$this->_rules = unserialize( $data );
			return;
		}

		// 如果默认规则中没有任何设置，则需要从 UtilUrl 获得配置的规则
		if ( empty( $this->rules ) )
			$this->rules = UtilUrl::getConfig();

		// 开始为规则初始化，生成对应的规则匹配对象
		foreach ( $this->rules as $pattern=>$route )
			$this->_rules[] = $this->createUrlRule( $route , $pattern );

		// 如果缓存允许，则将规则缓存起来
		if ( isset( $cacheObj ) && $this->cacheRules === true )
			$cacheObj->writeByKey( self::CACHE_KEY , serialize( $this->_rules ) );
	}

	/**
	 * 运行时规则添加
	 *
	 * @param array $rules 新规则
	 * @param boolean $append 是否添加到规则集后，如果为 false 则添加到最前，优先使用此规则
	 * @return void
	 */
	public function addRules($rules,$append=true)
	{
		if ($append)
		{
			foreach($rules as $pattern=>$route)
				$this->_rules[]=$this->createUrlRule($route,$pattern);
		}
		else
		{
			$rules=array_reverse($rules);
			foreach($rules as $pattern=>$route)
				array_unshift($this->_rules, $this->createUrlRule($route,$pattern));
		}
	}

	/**
	 * 生成匹配对象，默认使用 CUrlRule 解析
	 *
	 * @param mixed $route 需要解析的路由
	 * @param string $pattern 匹配模式
	 * @return CUrlRule
	 */
	protected function createUrlRule($route,$pattern)
	{
		if(is_array($route) && isset($route['class']))
			return $route;
		else
			return new $this->urlRuleClass($route,$pattern);
	}

	/**
	 * 创建一个 URL
	 *
	 * @param string $route 路由部分
	 * @param array $params 其他参数
	 * @param string $ampersand 参数键值对的分割符
	 * @return string
	 */
	public function createUrl($route,$params=array(),$ampersand='&')
	{
		unset($params[$this->routeVar]);
		foreach($params as $i=>$param)
			if($param===null)
				$params[$i]='';

		if(isset($params['#']))
		{
			$anchor='#'.$params['#'];
			unset($params['#']);
		}
		else
			$anchor='';
		$route=trim($route,'/');
		foreach($this->_rules as $i=>$rule)
		{
			if(is_array($rule))
				$this->_rules[$i]=$rule=Nbt::createComponent($rule);
			if(($url=$rule->createUrl($this,$route,$params,$ampersand))!==false)
			{
				if($rule->hasHostInfo)
					return $url==='' ? '/'.$anchor : $url.$anchor;
				else
					return $this->getBaseUrl().'/'.$url.$anchor;
			}
		}
		return $this->createUrlDefault($route,$params,$ampersand).$anchor;
	}

	/**
	 * 没有对应的规则，就使用基本规则进行 URL 生成
	 *
	 * @param string $route 路由部分
	 * @param array $params 其他参数
	 * @param string $ampersand 参数分割符
	 * @return string
	 */
	protected function createUrlDefault($route,$params,$ampersand)
	{
		if($this->getUrlFormat()===self::PATH_FORMAT)
		{
			$url=rtrim($this->getBaseUrl().'/'.$route,'/');
			if($this->appendParams)
			{
				$url=rtrim($url.'/'.$this->createPathInfo($params,'/','/'),'/');
				return $route==='' ? $url : $url.$this->urlSuffix;
			}
			else
			{
				if($route!=='')
					$url.=$this->urlSuffix;
				$query=$this->createPathInfo($params,'=',$ampersand);
				return $query==='' ? $url : $url.'?'.$query;
			}
		}
		else
		{
			$url=$this->getBaseUrl();
			if(!$this->showScriptName)
				$url.='/';
			if($route!=='')
			{
				$url.='?'.$this->routeVar.'='.$route;
				if(($query=$this->createPathInfo($params,'=',$ampersand))!=='')
					$url.=$ampersand.$query;
			}
			elseif(($query=$this->createPathInfo($params,'=',$ampersand))!=='')
				$url.='?'.$query;
			return $url;
		}
	}

	/**
	 * 解析伪静态 URL
	 * 
	 * @param CHttpRequest $request CHttpRequest 对象
	 * @return string
	 */
	public function parseUrl($request)
	{
		if(isset($_GET[$this->routeVar]))
			return $_GET[$this->routeVar];
		elseif(isset($_POST[$this->routeVar]))
			return $_POST[$this->routeVar];
		elseif($this->getUrlFormat()===self::PATH_FORMAT)
		{
			$rawPathInfo=$request->getPathInfo();
			$pathInfo=$this->removeUrlSuffix($rawPathInfo,$this->urlSuffix);
			foreach($this->_rules as $i=>$rule)
			{
				if(is_array($rule))
					$this->_rules[$i]=$rule=Nbt::createComponent($rule);
				if(($r=$rule->parseUrl($this,$request,$pathInfo,$rawPathInfo))!==false)
					return isset($_GET[$this->routeVar]) ? $_GET[$this->routeVar] : $r;
			}

			if($this->useStrictParsing)
				throw new CHttpException( 404 , "Unable to resolve the request '{$pathInfo}'." );
			else
				return $pathInfo;
		}
		else
			return '';
	}

	/**
	 * 解析 URL 详细参数
	 *
	 * @param string $pathInfo URL 详细路径信息
	 */
	public function parsePathInfo($pathInfo)
	{
		if($pathInfo==='')
			return;
		$segs=explode('/',$pathInfo.'/');
		$n=count($segs);
		for($i=0;$i<$n-1;$i+=2)
		{
			$key=$segs[$i];
			if($key==='') continue;
			$value=$segs[$i+1];
			if(($pos=strpos($key,'['))!==false && ($m=preg_match_all('/\[(.*?)\]/',$key,$matches))>0)
			{
				$name=substr($key,0,$pos);
				for($j=$m-1;$j>=0;--$j)
				{
					if($matches[1][$j]==='')
						$value=array($value);
					else
						$value=array($matches[1][$j]=>$value);
				}
				if(isset($_GET[$name]) && is_array($_GET[$name]))
					$value=CMap::mergeArray($_GET[$name],$value);
				$_REQUEST[$name]=$_GET[$name]=$value;
			}
			else
				$_REQUEST[$key]=$_GET[$key]=$value;
		}
	}

	/**
	 * 根据参数创建伪静态 URL 路径
	 *
	 * @param array $params 参数集
	 * @param string $equal 参数分割方式
	 * @param string $ampersand 参数之间的分割符
	 * @param string $key 是否使用数组的方式传递参数，如果需要则填写数组域
	 * @return string
	 */
	public function createPathInfo($params,$equal,$ampersand, $key=null)
	{
		$pairs = array();
		foreach($params as $k => $v)
		{
			if ($key!==null)
				$k = $key.'['.$k.']';

			if (is_array($v))
				$pairs[]=$this->createPathInfo($v,$equal,$ampersand, $k);
			else
				$pairs[]=urlencode($k).$equal.urlencode($v);
		}
		return implode($ampersand,$pairs);
	}

	/**
	 * 移除后缀
	 *
	 * @param string $pathInfo 需要处理的 URL 地址
	 * @param string $urlSuffix 需要移除的后缀
	 * @return string
	 */
	public function removeUrlSuffix($pathInfo,$urlSuffix)
	{
		if($urlSuffix!=='' && substr($pathInfo,-strlen($urlSuffix))===$urlSuffix)
			return substr($pathInfo,0,-strlen($urlSuffix));
		else
			return $pathInfo;
	}

	/**
	 * 获得基本URL
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		if($this->_baseUrl!==null)
			return $this->_baseUrl;
		else
		{
			if($this->showScriptName)
				$this->_baseUrl=Nbt::app()->getRequest()->getScriptUrl();
			else
				$this->_baseUrl=Nbt::app()->getRequest()->getBaseUrl();
			return $this->_baseUrl;
		}
	}

	/**
	 * 设置基本 URL
	 *
	 * @param string $value 新基本 URL 地址
	 */
	public function setBaseUrl($value)
	{
		$this->_baseUrl=$value;
	}

	/**
	 * 获得当前的 URL 匹配模式
	 *
	 * @return string
	 */
	public function getUrlFormat()
	{
		return $this->_urlFormat;
	}

	/**
	 * 设置 URL 匹配模式
	 *
	 * @param string $value URL 匹配模式，有 get 与 path 两种
	 */
	public function setUrlFormat($value)
	{
		if($value===self::PATH_FORMAT || $value===self::GET_FORMAT)
			$this->_urlFormat=$value;
		else
			throw new CException( 'CUrlManager.UrlFormat must be either "path" or "get".' );
	}
}


/**
 * URL 基本匹配器
 *
 * @author wengebin
 * @date 2013-10-25
 */
abstract class CBaseUrlRule extends CComponents
{
	/**
	 * @var boolean 是否解析 URL 地址头
	 */
	public $hasHostInfo=false;
	/**
	 * 创建 URL 抽象方法
	 *
	 * @param CUrlManager $manager URL 匹配器
	 * @param string $route 路由部分
	 * @param array $params 其他参数
	 * @param string $ampersand 参数之间的分割符
	 * @return mixed
	 */
	abstract public function createUrl($manager,$route,$params,$ampersand);
	/**
	 * 解析 URL 抽象方法
	 *
	 * @param CUrlManager $manager URL 匹配器
	 * @param CHttpRequest $request CHttpRequest 对象
	 * @param string $pathInfo 被解析的 URL 路径
	 * @param string $rawPathInfo 路径信息，包括潜在的 URL 后缀
	 * @return mixed
	 */
	abstract public function parseUrl($manager,$request,$pathInfo,$rawPathInfo);
}


/**
 * CUrlRule 解析器类
 *
 * @author wengebin
 * @date 2013-10-25
 */
class CUrlRule extends CBaseUrlRule
{
	/**
	 * URL 后缀
	 */
	public $urlSuffix;
	/**
	 * 是否严格区分大小写
	 */
	public $caseSensitive;
	/**
	 * URL 中的默认参数
	 */
	public $defaultParams=array();
	/**
	 * 参数是否需要匹配特定值
	 */
	public $matchValue;
	/**
	 * 匹配多个动词，动词间用","分开
	 */
	public $verb;
	/**
	 * 规则是否仅仅用于解析，默认为 false，解析 与 生成 均可使用此规则
	 */
	public $parsingOnly=false;
	/**
	 * 路由名称，controller/action 组成
	 */
	public $route;
	/**
	 * 路由映射表
	 */
	public $references=array();
	/**
	 * 路由匹配模式
	 */
	public $routePattern;
	/**
	 * 匹配的表达式
	 */
	public $pattern;
	/**
	 * 构造 URL 的模板
	 */
	public $template;
	/**
	 * 参数列表
	 */
	public $params=array();
	/**
	 * 额外参数的添加位置，默认为参数集合尾端
	 */
	public $append;
	/**
	 * 是否包含 URL 头信息
	 */
	public $hasHostInfo;

	/**
	 * 构造器
	 *
	 * @param string $route 路由部分
	 * @param string $pattern 路由匹配规则
	 */
	public function __construct($route,$pattern)
	{
		if(is_array($route))
		{
			foreach(array('urlSuffix', 'caseSensitive', 'defaultParams', 'matchValue', 'verb', 'parsingOnly') as $name)
			{
				if(isset($route[$name]))
					$this->$name=$route[$name];
			}
			if(isset($route['pattern']))
				$pattern=$route['pattern'];
			$route=$route[0];
		}
		$this->route=trim($route,'/');

		$tr2['/']=$tr['/']='\\/';

		if(strpos($route,'<')!==false && preg_match_all('/<(\w+)>/',$route,$matches2))
		{
			foreach($matches2[1] as $name)
				$this->references[$name]="<$name>";
		}

		$this->hasHostInfo=!strncasecmp($pattern,'http://',7) || !strncasecmp($pattern,'https://',8);

		if($this->verb!==null)
			$this->verb=preg_split('/[\s,]+/',strtoupper($this->verb),-1,PREG_SPLIT_NO_EMPTY);

		if(preg_match_all('/<(\w+):?(.*?)?>/' , $pattern , $matches))
		{
			$tokens=array_combine($matches[1],$matches[2]);
			foreach($tokens as $name=>$value)
			{
				if($value==='')
					$value='[^\/]+';
				$tr["<$name>"]="(?P<$name>$value)";
				if(isset($this->references[$name]))
					$tr2["<$name>"]=$tr["<$name>"];
				else
					$this->params[$name]=$value;
			}
		}
		$p=rtrim($pattern,'*');
		$this->append=$p!==$pattern;
		$p=trim($p,'/');
		$this->template=preg_replace('/<(\w+):?.*?>/','<$1>',$p);
		$this->pattern='/^'.strtr($this->template,$tr).'\/';
		if($this->append)
			$this->pattern.='/u';
		else
			$this->pattern.='$/u';

		if($this->references!==array())
			$this->routePattern='/^'.strtr($this->route,$tr2).'$/u';

		if(YII_DEBUG && @preg_match($this->pattern,'test')===false)
			throw new CException( "The URL pattern '{$pattern}' for route '{$route}' is not a valid regular expression." );
	}

	/**
	 * 创建一个伪静态 URL 
	 *
	 * @param CUrlManager $manager URL 管理器对象
	 * @param string $route 路由部分
	 * @param array $params 其他参数
	 * @param string $ampersand 参数之间的分割符
	 * @return mixed
	 */
	public function createUrl($manager,$route,$params,$ampersand)
	{
		if($this->parsingOnly)
			return false;

		if($manager->caseSensitive && $this->caseSensitive===null || $this->caseSensitive)
			$case='';
		else
			$case='i';

		$tr=array();
		if($route!==$this->route)
		{
			if($this->routePattern!==null && preg_match($this->routePattern.$case,$route,$matches))
			{
				foreach($this->references as $key=>$name)
					$tr[$name]=$matches[$key];
			}
			else
				return false;
		}

		foreach($this->defaultParams as $key=>$value)
		{
			if(isset($params[$key]))
			{
				if($params[$key]==$value)
					unset($params[$key]);
				else
					return false;
			}
		}

		foreach($this->params as $key=>$value)
			if(!isset($params[$key]))
				return false;

		if($manager->matchValue && $this->matchValue===null || $this->matchValue)
		{
			foreach($this->params as $key=>$value)
			{
				if(!preg_match('/\A'.$value.'\z/u'.$case,$params[$key]))
					return false;
			}
		}

		foreach($this->params as $key=>$value)
		{
			$tr["<$key>"]=urlencode($params[$key]);
			unset($params[$key]);
		}

		$suffix=$this->urlSuffix===null ? $manager->urlSuffix : $this->urlSuffix;

		$url=strtr($this->template,$tr);

		if($this->hasHostInfo)
		{
			$hostInfo=Nbt::app()->getRequest()->getHostInfo();
			if(stripos($url,$hostInfo)===0)
				$url=substr($url,strlen($hostInfo));
		}

		if(empty($params))
			return $url!=='' ? $url.$suffix : $url;

		if($this->append)
			$url.='/'.$manager->createPathInfo($params,'/','/').$suffix;
		else
		{
			if($url!=='')
				$url.=$suffix;
			$url.='?'.$manager->createPathInfo($params,'=',$ampersand);
		}

		return $url;
	}

	/**
	 * 解析一个伪静态 URL
	 *
	 * @param CUrlManager $manager 解析器对象
	 * @param CHttpRequest $request CHttpRequest 对象
	 * @param string $pathInfo URL 路径信息
	 * @param string $rawPathInfo 路径信息，包括潜在的 URL 后缀
	 * @return mixed
	 */
	public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
	{
		if($this->verb!==null && !in_array($request->getRequestType(), $this->verb, true))
			return false;

		if($manager->caseSensitive && $this->caseSensitive===null || $this->caseSensitive)
			$case='';
		else
			$case='i';

		if($this->urlSuffix!==null)
			$pathInfo=$manager->removeUrlSuffix($rawPathInfo,$this->urlSuffix);

		// URL suffix required, but not found in the requested URL
		if($manager->useStrictParsing && $pathInfo===$rawPathInfo)
		{
			$urlSuffix=$this->urlSuffix===null ? $manager->urlSuffix : $this->urlSuffix;
			if($urlSuffix!='' && $urlSuffix!=='/')
				return false;
		}

		if($this->hasHostInfo)
			$pathInfo=strtolower($request->getHostInfo()).rtrim('/'.$pathInfo,'/');

		$pathInfo.='/';

		if(preg_match($this->pattern.$case,$pathInfo,$matches))
		{
			foreach($this->defaultParams as $name=>$value)
			{
				if(!isset($_GET[$name]))
					$_REQUEST[$name]=$_GET[$name]=$value;
			}
			$tr=array();
			foreach($matches as $key=>$value)
			{
				if(isset($this->references[$key]))
					$tr[$this->references[$key]]=$value;
				elseif(isset($this->params[$key]))
					$_REQUEST[$key]=$_GET[$key]=$value;
			}
			if($pathInfo!==$matches[0]) // there're additional GET params
				$manager->parsePathInfo(ltrim(substr($pathInfo,strlen($matches[0])),'/'));
			if($this->routePattern!==null)
				return strtr($this->route,$tr);
			else
				return $this->route;
		}
		else
			return false;
	}
}
