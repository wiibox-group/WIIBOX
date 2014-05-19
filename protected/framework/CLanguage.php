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
    /**
     * 初始化
     */
    public function __construct()
	{
        $aryUrl = explode('.', $_SERVER['HTTP_HOST']);
        if((in_array($aryUrl[0], $this -> _aryLanguage)) === true)
            $this->_language = $aryUrl[0];
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
    public function getAryByFile($_file = '') {
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
}
