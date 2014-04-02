<?php
/**
 * CWebUser class files.
 * Save session and get info from session.
 * 
 * 
 * @author samson.zhou<samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-18
 */

class CWebUser extends CApplicationComponents
{
	
	const PERMIT_KEY = 'permit'; 
	
	/**
	 * Is the user login? Check from session.
	 *  
	 * @var Bool
	 */
	private $isGuest = false;
	
	/**
	 * Get return url from session.
	 * 
	 * @var string
	 */
	private $returnUrl = null;

	/**
	 * 初始化
	 *
	 */
	public function init()
	{
		//自动开启session
		Nbt::app()->getSession()->open();
	}
	
	/**
	 * Is the user login? Check from session.
	 * 
	 * @return bool
	 */
	public function getIsGuest()
	{
		if( $this->getState( 'isGuest' ) === null )
		{
			$this->setState( 'isGuest' , true );
		}
		return $this->getState( 'isGuest' );
	}
	
	/**
	 * save session info
	 * 
	 * @param Array $_aryUserInfo
	 * 
	 */
	public function login( $_aryUserInfo = array() )
	{
		$this->emptyState();
		$this->setState( 'isGuest' , false );
		
		foreach ( (array)$_aryUserInfo as $k=>$v )
		{
			$this->setState( $k , $v );
		}
	}
	
	/**
	 * system Login
	 * 
	 * 
	 */
	public function systemLogin()
	{
		/*$aryUserInfo = array();
		$aryUserInfo['userId'] = ChannelToolUtil::NAME_SYSTEM;
		$aryUserInfo['programList'] = ':ch_datafeed_run:ch_datafeed_runlist:ch_datafeed_upload:';
		$this->login( $aryUserInfo );
		unset($aryUserInfo);*/
	}
	
	/**
	 * user logout, clear session and destroy session.
	 * 
	 */
	public function logout()
	{
		$this->setState( 'isGuest' , true );
		$this->clearState();
	}
		
	/**
	 * clear session and destroy session
	 * 
	 */
	public function clearState()
	{
		$_SESSION = array();
		session_destroy();
	}
	
	/**
	 * empty session.
	 * 
	 */
	public function emptyState()
	{
		$_SESSION = array();
	}
	
	/**
	 * 设置会员ID
	 *
	 * @param int $_intId
	 */
	public function setMemberId( $_intId = null )
	{
		$_SESSION['__mid'] = $_intId;
	}
	
	/**
	 * 获取会员ID
	 *
	 * @return int
	 */
	public function getMemberId()
	{
		return $_SESSION['__mid'];
	}
	
	/**
	 * 设置管理员用户名
	 *
	 * @param int $_intId
	 */
	public function setMemberName( $_strUname = null )
	{
		$_SESSION['__mname'] = $_strUname;
	}
	
	/**
	 * 获取管理员用户名
	 *
	 * @return int
	 */
	public function getMemberName()
	{
		return $_SESSION['__mname'];
	}
	
	/**
	 * 设置教师ID
	 *
	 * @param int $_intId
	 */
	public function setTeacherId( $_intId = null )
	{
		$_SESSION['__tid'] = $_intId;
	}
	
	/**
	 * 获取用户ID
	 *
	 * @return int
	 */
	public function getTeacherId()
	{
		return $_SESSION['__tid'];
	}
	
	/**
	 * 设置教师ID
	 *
	 * @param int $_intId
	 */
	public function setTeacherName( $_strUname = null )
	{
		$_SESSION['__tname'] = $_strUname;
	}
	
	/**
	 * 获取用户ID
	 *
	 * @return int
	 */
	public function getTeacherName()
	{
		return $_SESSION['__tname'];
	}
	
	/**
	 * 设置管理员ID
	 *
	 * @param int $_intId
	 */
	public function setAdminId( $_intId = null )
	{
		$_SESSION['__aid'] = $_intId;
	}
	
	/**
	 * 获取管理员ID
	 *
	 * @return int
	 */
	public function getAdminId()
	{
		return $_SESSION['__aid'];
	}
	
	/**
	 * 设置管理员用户名
	 *
	 * @param int $_intId
	 */
	public function setAdminName( $_strUname = null )
	{
		$_SESSION['__aname'] = $_strUname;
	}
	
	/**
	 * 获取管理员用户名
	 *
	 * @return int
	 */
	public function getAdminName()
	{
		return $_SESSION['__aname'];
	}

	/**
	 * 设置前台会员 ID
	 *
	 * @param int $_intId
	 */
	public function setUserId( $_intId = null )
	{
		$_SESSION['__uid'] = $_intId;
	}
	
	/**
	 * 获得前台会员 ID
	 *
	 * @return int
	 */
	public function getUserId()
	{
		return $_SESSION['__uid'];
	}
	
	/**
	 * get session info by session key.
	 * 
	 * @param string $_key
	 * @return mix
	 */
	public function getState( $_key )
	{
		return isset( $_SESSION[$_key] ) ? $this->decode( $_SESSION[$_key] ) : null;  
	}
	
	/**
	 * get all of session.
	 * 
	 * @return Array
	 */
	public function getStates()
	{
		return $_SESSION;
	}
	
	/**
	 * set session
	 * 
	 * @param string $_key
	 * @param string $_val
	 */
	public function setState( $_key , $_val )
	{
		$_SESSION[$_key] = $this->encode( $_val );
	}
	
	/**
	 * encode session value;
	 * 
	 * @param string $_val
	 * @return string
	 */
	public function encode( $_val )
	{
		return $_val;
	}
	
	/**
	 * decode session value;
	 * 
	 * @param string $_val
	 * @return string
	 */
	public function decode( $_val )
	{
		return $_val;
	}
	
	/**
	 * set msginfo
	 * @param unknown_type $_info
	 */
	public function setTipInfo( $_info )
	{
		$this->setState( 'msginfo' , $_info );
	}
	
	/**
	 * get Msginfo
	 * 
	 * @param unknown_type $_info
	 */
	public function getTipInfo( $_isClear = true )
	{
		$info = $this->getState( 'msginfo' );
		if( $_isClear )
		{
			$this->setState( 'msginfo' , null );
		}
		return $info;
	}
	
	/**
	 * set return url
	 * @param unknown_type $_url
	 */
	public function setReturnUrl( $_url )
	{
		$this->setState( 'returnUrl' , $_url );
	}
	
	
	/**
	 * get return url
	 * 
	 */
	public function getReturnUrl()
	{
		$url = $this->getState( 'returnUrl' );
		$this->setState( 'returnUrl' , null );
		return $url;
	}
	
	/**
	 * 后台权限校验.
	 *  
	 */
	public function authorizeAdminPermission( $_permissionName , $_isReturn = false )
	{
		//check userGroup
		$userGroup = trim($this->getState('purviewlist'));
		
		//if( strstr( ",{$_permissionName},"  ) )权限校验
		
		if( $_isReturn )
			return false;
		else
			throw new CHttpException( 403 , 'Sorry,You have no permission to accsess this page.' );
	}
	
//end class	
}
