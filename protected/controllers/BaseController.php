<?php
/**
 * Base Controller
 * 
 * @author wengebin
 */
class BaseController extends CController
{
	/**
	 * 设置布局模板
	 *
	 * @var string
	 */
	public $layout = 'default';
	
	/**
	 * 是否需要登录
	 */
	public $isRequiredLogin = false;
	
	/**
	 * 加载seo标题
	 *
	 * @var string
	 */
	public $seoTitle = "";
	
	/**
	 * 加载seo关键字
	 *
	 * @var string
	 */
	public $seoKeyword = "";
	
	/**
	 * 加载seo描述信息
	 *
	 * @var string
	 */
	public $seoDesc = "";
	
	/**
	 * bread crumbs
	 * 
	 * @var string
	 */
	private $_aryBreadCrumbs = array();
	
	/**
	 * init
	 * 
	 */
	public function init()
	{
		parent::init();
		//check is login
		if( $this->isRequiredLogin && Nbt::app()->user->userId <= 0 )
		{
			//ajax请求，输出ajax格式信息
			if( Nbt::app()->request->isAjaxRequest )
			{
				echo $this->encodeAjaxData( false , array() , '您没有登录' );
				exit();
			}
			else
			{			
				$url = Nbt::app()->request->getUrl();
				$this->redirect( array( 'login' , 'gourl'=>urlencode($url) ) );
			}
		}
	}
	
	
	public function beforeAction()
	{
		return true;
	}
	
	/**
	 * set bread crumbs
	 * 
	 * @params array|string $_ary
	 * 
	 */
	public function setBreadCrumbs( $_ary = array() )
	{
		if( !is_array( $_ary ) )
		{
			$_ary = array( $_ary );
		}
		$this->_aryBreadCrumbs = array_merge( $this->_aryBreadCrumbs , $_ary );
	}
	
	/**
	 * get bread crumbs
	 * 
	 */
	public function getBreadCrumbs()
	{
		return $this->_aryBreadCrumbs;
	}	
	
	/**
	 * 成功提示页面
	 *
	 * @param string	$_strMsg	提示消息
	 * @param array		$_aryLink	引导链接
	 */
	public function showSuccessMessage( $_strMsg = "" , $_aryLink = array() )
	{
		$view = NBT_VIEW_PATH.DIRECTORY_SEPARATOR."systems".DIRECTORY_SEPARATOR."msg_success";
		
		$this->renderView( $view , array( 'strMsg'=>$_strMsg,'aryLink'=>$_aryLink ) );
		exit();
	}
	
	/**
	 * 错误提示页面
	 *
	 * @param string	$_strMsg	提示消息
	 * @param array		$_aryLink	引导链接
	 */
	public function showErrorMessage( $_strMsg = "", $_aryLink = array() )
	{
		$view = NBT_VIEW_PATH.DIRECTORY_SEPARATOR."systems".DIRECTORY_SEPARATOR."msg_error";
		
		$this->renderView( $view , array( 'strMsg'=>$_strMsg,'aryLink'=>$_aryLink ) );
		exit();
	}
	
	/**
	 * 加载seo信息
	 *
	 * @param string $_strPageType			页面类型
	 * @param string $_strPagePk			页面主标识符
	 * @param string $_strPageSubPk			页面子标识符
	 */
	public function loadSeo( $_strPageType = "" , $_strPagePk = "" , $_strPageSubPk = "" )
	{
		$arySeo = CmsSeoModel::model()->getSeoInfo( $_strPageType , $_strPagePk , $_strPageSubPk );
		if( empty( $arySeo ) && $_strPagePk != '' || $_strPageSubPk != '' )
		{
			$arySeo = CmsSeoModel::model()->getSeoInfo( $_strPageType , '' , '' );
			if ( empty( $arySeo ) ) return ;
		}
		
		$this->seoTitle = $arySeo['seo_title'];
		$this->seoKeyword = $arySeo['seo_keyword'];
		$this->seoDesc = $arySeo['seo_desc'];		
	}
	
	/**
	 * 获取seo标题
	 *
	 * @return string
	 */
	public function getSeoTitle()
	{
		return $this->seoTitle;
	}

	/**
	 * 替换seo标题
	 *
	 * @param string $_strSeoTitle 新标题
	 * @return void
	 */
	public function replaceSeoTitle( $_strSeoTitle = '' )
	{
		$this->seoTitle = $_strSeoTitle;
	}

	/**
	 * 替换seo标题
	 *
	 * @param string $_strSeoTitle 新标题
	 * @param int $_intWhere SEO新增标题位置，1 - 最前，-1 - 最后
	 * @return void
	 */
	public function addSeoTitle( $_strSeoTitle = '' , $_intWhere = 1 )
	{
		$this->seoTitle = 
			$_intWhere === 1 
			? $_strSeoTitle.' - '.$this->seoTitle 
				: $_intWhere === -1 
				? $this->seoTitle.' - '.$_strSeoTitle
				: $this->seoTitle;
	}
	
	/**
	 * 获取seo关键字
	 *
	 * @return string
	 */
	public function getSeoKeyword()
	{
		return $this->seoKeyword;
	}

	/**
	 * 替换seo关键词
	 *
	 * @param string $_strSeoKeyword 新关键词
	 * @return void
	 */
	public function replaceSeoKeyword( $_strSeoKeyword = '' )
	{
		$this->seoKeyword = $_strSeoKeyword;
	}
	
	/**
	 * 获取seo描述信息
	 *
	 * @return string
	 */
	public function getSeoDesc()
	{
		return $this->seoDesc;
	}

	/**
	 * 替换seo描述
	 *
	 * @param string $_strSeoDesc 新描述
	 * @return void
	 */
	public function replaceSeoDesc( $_strSeoDesc = '' )
	{
		$this->seoDesc = $_strSeoDesc;
	}
	
	/**
	 * Output encoded ajax Data,json format.
	 * @param bool $_isok
	 * @param array $_data
	 * @param string $_msg
	 * @param bool $_isNeedLogin 是否需要登录
	 * @param array	$_aryLink 引导链接
	 */
	public function encodeAjaxData( $_isok=FALSE , $_aryData=array() , $_strMsg='' , $_isNeedLogin = true , $_aryLink = array() )
	{
		if( !headers_sent() )
			header('Content-Type: text/html; charset=utf-8');

		$aryData = array();
		$aryData['ISOK'] = $_isok ? 1 : 0;
		$aryData['DATA'] = $_aryData;
		$aryData['MSG'] = $_strMsg;
		$aryData['LINK'] = $_aryLink;
		return CJavascript::jsonEncode($aryData);
	}
	
//end class	
}

