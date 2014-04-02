<?php
/**
 * base controller.
 * 
 * 
 * 
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-06-29
 */
class CController
{
	protected $id=null;
	protected $actionId = null;
	protected $layout=null;
	protected $baseUrl = "";
	
	/**
	 * __construct
	 * 
	 */
	public function __construct()
	{
		$this->baseUrl = Nbt::app()->request->getBaseUrl();
		$this->init();
	}
	
	
	/**
	 * init
	 * 
	 */
	public function init()
	{
		//@todo
	}
	
	/**
	 * display pages
	 * @see _render()
	 * 
	 * @param string		$_viewFile_
	 * @param array|string	$_data_
	 * @param bool			$_return_
	 * @return string
	 */
	public function render($_viewFile_,$_data_=null,$_return_=false)
	{
		$_viewFile_=NBT_VIEW_PATH.DIRECTORY_SEPARATOR.$this->getId().DIRECTORY_SEPARATOR.$_viewFile_;
		if($this->layout!==null)
		{
			$content=$this->renderView($_viewFile_,$_data_,true);
			$layout=NBT_VIEW_PATH.DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$this->layout;
			$this->renderView($layout,array('content'=>$content));
		}
		else
		{
			$this->renderView($_viewFile_,$_data_);
		}
	}
	
	
	/**
	 * display view.
	 * 
	 * @param string		$_viewFile_
	 * @param array|string	$_data_
	 * @param bool			$_return_
	 * @return string
	 */
	public function renderView($_viewFile_,$_data_=null,$_return_=false,$_isFromLayout=false)
	{
		// we use special variable names here to avoid conflict when extracting data
		if(is_array($_data_))
			extract($_data_,EXTR_PREFIX_SAME,'data');
		else
			$data=$_data_;
			
		$_viewFile_="{$_viewFile_}.php";		
		if($_return_)
		{
			ob_start();
			ob_implicit_flush(false);
			require($_viewFile_);
			return ob_get_clean();
		}
		else
			require($_viewFile_);
	}
	
	/**
	 * Output encoded ajax Data,json format.
	 * @param bool $_isok
	 * @param array $_data
	 * @param array $_error
	 * @param string $_msg
	 */
	public function encodeAjaxData($_isok=FALSE,$_data=array(),$_msg='')
	{
		header('Content-Type: text/html; charset=utf-8');

		$aryData=array();
		$aryData['ISOK']=$_isok?1:0;
		$aryData['DATA']=$_data;
		$aryData['MSG']=$_msg;
		return CJavascript::jsonEncode($aryData);
	}
	
	public function beforeAction()
	{
		
	}
	
	public function afterAction()
	{
		
	}
	
	/**
	 * set layout
	 * 
	 * @param string $_layout
	 */
	public function setLayout($_layout)
	{
		$this->layout=$_layout;
	}
	
	/**
	 * set controller name
	 * @param string $_id controller name.
	 */
	public function setId( $_id )
	{
		$this->id = $_id;
	}
	
	/**
	 * return controller name.
	 * 
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * set action name.
	 * 
	 * @param string $_actionId
	 */
	public function setActionId( $_actionId )
	{
		$this->actionId = $_actionId;
	}
	
	/**
	 * get action name.
	 * 
	 */
	public function getActionId()
	{
		return $this->actionId;
	}
	
	/**
	 * create url
	 * 
	 * @param	string	$_route
	 * @param	array	$_aryParams
	 */
	public function createUrl( $_route , $_aryParams = array() )
	{
		return Nbt::app()->createUrl( $_route , $_aryParams );
	}
	
	/**
	 * create absolute url
	 * 
	 * @param	string	$_route
	 * @param	array	$_aryParams
	 */
	public function createAbsouteUrl( $_route , $_aryParams = array() )
	{
		return Nbt::app()->createAbsoluteUrl( $_route , $_aryParams );
	}
	
	public function redirect( $_mixUrl )
	{
		$gourl = '';
		if( is_array( $_mixUrl ) )
		{
			$route = 'index/index';
			$aryParams = array();
			foreach( $_mixUrl as $k=>$v )
			{
				if( $k === 0 )
				{
					$route = $_mixUrl[0];
				}
				else
				{
					$aryParams[$k]=$v;
				}
			}
			$gourl = $this->createUrl( $route , $aryParams );
		}
		elseif ( is_string( $_mixUrl ) )
		{
			$gourl = $_mixUrl;
		}
		if( empty( $gourl ) )
			throw new CException( NBT_DEBUG ? 'redirect var error!' : 'error:10001' );
		header( "Location:{$gourl}" );
		exit();
	}
	
	/**
	 * refresh current page.
	 * 
	 */
	public function refresh( $_terminate = true , $_anchor = "" )
	{
		$this->redirect( Nbt::app()->getRequest()->getUrl().$_anchor,$_terminate );
	}
	
	public function setReturnUrl( $_url = null )
	{
		$_url = is_null( $_url ) ? Nbt::app()->request->getUrlReferrer() : $_url;	
		Nbt::app()->user->setReturnUrl( $_url );
	}
	
	public function getReturnUrl( $_urlDefault = null )
	{
		$returnUrl = Nbt::app()->user->getReturnUrl();
		if( $returnUrl === null )
			$returnUrl = $_urlDefault;
		return $returnUrl;
	}
	
	/**
	 * 部件
	 *
	 * @param string $_strWidgetName	部件名称
	 * @param array $_aryParams	部件参数
	 */
	public function widget( $_strWidgetName = '' , $_aryParams = array() )
	{
		$widget = new $_strWidgetName();
		foreach ( $_aryParams as $key=>$value )
			$widget->$key = $value;
		$widget->init();
		$widget->run();
		return $widget;
	}
	
//end class	
}
