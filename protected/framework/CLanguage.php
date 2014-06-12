<?php

/**
 * 
 * i18n操作类
 * 
 * @author  zhangyi
 * @package framework
 * @date    2014-3-27
 * 
 */
class CLanguage {
	
	/** 获取地区语言 格式 zh en  */
    private $_language = 'zh';

    /** 此数组为二维数组 用于缓存每次请求所需要的语言配置文件 */
    public $_aryData;
    
	/** 支持的语言类型 */
    public $_aryLanguage = array('zh','en');

    /** cookie名称 */
    private $_cookieName = 'wiiboxLanguage';
    
    /**
     * 初始化
     */
    public function __construct()
	{
        $strCookieLanguage = $_COOKIE[ $this -> _cookieName ];

		//判断cookie中是否存在语言,如果存在则直接从cookie中进行读取
		if( empty( $strCookieLanguage ) )
		{
			$this -> getRequestBrowserLanguage();
			setcookie( $this -> _cookieName , $this -> _language , time()+3600*24*365 );
		}else
		{
			$this -> _language = $strCookieLanguage;
		}
		
		//判断当前语言是否在指定支持语言范围内
		if( !in_array( $this -> _language , $this -> _aryLanguage ) )
			$this -> _language = 'en';
    }

    /**
     * i18n根据key获取值
     * 
     * @author zhangyi
     * @date 2014-03-28
     * @param String $_strKey  key值 格式('文件前缀,数组key')
     * @return String 该key值
     */
    public function i18n( $_strKey = '' ) {
       
        $arykeys = explode(',', $_strKey);
        
        //判断总的数组中是否存在该key数据
        if (empty($this->_aryData[$arykeys[0]])) {
            //根据key获取数据
            $this->_aryData[$arykeys[0]] = $this->getAryByKey($arykeys[0]);
            $aryData = $this->_aryData[$arykeys[0]];
        } else {
            $aryData = $this->_aryData[$arykeys[0]];
        }
        return $aryData[$arykeys[1]];
    }

    /**
     * 获得语言缓存/加载语言文件
     * 
     * @author zhangyi
     * @date 2014-3-28
     * @param String $_strKey  配置文件前缀
     * @return type
     */
    public function getAryByKey( $_strKey = '' ) {
        $language = $this->_language;
        $strFilePath = WEB_ROOT
                . "/protected/config/language/{$language}/{$_strKey}.config.php";
        
        return $this -> getAryByFile($strFilePath);;
    }

    /**
     * 根据文件路径获取指定config文件中的array
     * 
     * @param type $_file 文件路径
     * @author zhangyi
     * @date 2014-3-28
     * @return array
     */
    public function getAryByFile($_file = '')
    {
        if (file_exists($_file))
            return require($_file);
        else{
            switch ($this->_language)
            {
                case 'zh':
                    throw new CModelException("文件地址不存在");
                    break;
                case 'en':
                    throw new CModelException("file not exists");
                    break;
                default :
                    throw new CModelException("file not exists");
            }
        }
    }

    /**
     * language的get方法
     * @return String 当前语言设置
     * @date 2014-4-4
     * @author zhangyi
     */
    public function getLanguage()
    {
        return $this -> _language;
    }
    
    /**
     * 获取当前访问网站浏览器语言,暂只支持(zh , en )解析
     *
     * @return 当前浏览器语言
     * @author zhangyi
     * @date 2014-06-08
     *
     */
    public function getRequestBrowserLanguage()
    {
    	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
    	if (preg_match("/zh-c/i", $lang))
    		$this -> _language = 'zh';
    	else if (preg_match("/zh/i", $lang))
    		$this -> _language = 'zh';
    	else if (preg_match("/en/i", $lang))
    		$this -> _language = 'en';
    	else
    		$this -> _language = 'en';
    	return $this -> _language;
    }
    
}
