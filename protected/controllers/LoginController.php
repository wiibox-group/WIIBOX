<?php
/**
 * Login Controller
 * 
 * @author biallo
 * @date 2013-5-23
 */
class LoginController extends BaseController
{	
	
	private $_redis;
	private $_fileName = 'user.pwd';
	private $_cookeName = 'wiibox_user';
	private $_sessionName = 'userInfo';

	/**
	 * init
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * Index method
	 */
	public function actionLogin()
	{
		$objLoginModel = LoginModel::model();
		try {
			//检查是否是post请求
			$boolCheckLogin = false;
			if( Nbt::app()->request->isPostRequest )
			{
				// 绑定数据
				$aryUserInfo['uname'] = isset( $_POST['uname'] ) ? htmlspecialchars( trim( $_POST['uname'] ) ) : '';
				$aryUserInfo['pwd'] = isset( $_POST['pwd'] ) ? CString::encodeMachinePassword( trim( $_POST['pwd'] ) ) : '';
				$strIsRemember = isset( $_POST['remember'] ) ? htmlspecialchars( trim( $_POST['remember'] ) ) : 'no';
				
				$boolCheckLogin = LoginModel::model() -> checkLogin( $aryUserInfo );
				
				if(($boolCheckLogin = LoginModel::model() -> checkLogin( $aryUserInfo )) === false)
					throw new CModelException( CUtil::i18n( 'controllers,login_index_pwdWrong' ) );
					
				//根据是否登入成功,再判断是否将用户名和密码写入cookie
				if( $boolCheckLogin === true && $strIsRemember === 'yes' )
				{
					$aryUserInfo['pwd'] = CString::encode($aryUserInfo['pwd'],UKEY);
					//设置cookie,时间为一年
					setcookie( $this -> _cookeName , base64_encode( json_encode( $aryUserInfo ) ) , time()+(365*24*3600));
				}
			}
			
			//如果没有登入成功,则判断cookie中是否存在用户名和密码
			else if( !empty( $_COOKIE[$this -> _cookeName]) && $boolCheckLogin === false  )
			{
				$aryUserInfo = json_decode( base64_decode( $_COOKIE[$this -> _cookeName] ) , 1 );
				$aryUserInfo['pwd'] = CString::decode( $aryUserInfo['pwd'] , UKEY );
				if( ( $boolCheckLogin = $objLoginModel -> checkLogin( $aryUserInfo ) ) === false )
					throw new CModelException( CUtil::i18n( 'controllers,login_index_pwdWrong' ) );
			}
			//contains/判断是否登入成功
			if( $boolCheckLogin === true )
			{
				Nbt::app()->session->set( 'userInfo' , $aryUserInfo );
				UtilMsg::saveTipToSession( '登入成功' );
				$this -> redirect( array( 'index/index' ) );
			}
		} catch (CModelException$e) {
			UtilMsg::saveErrorTipToSession( $e -> getMessage() );
		}
		
		$this->layout = 'login';
		$this->seoTitle = CUtil::i18n('controllers,login_index_seoTitle');
		
		//根据key判断文件是否存在,如果不存在则)创建一个默认账户
		$objLoginModel -> createDefaultUserInfo();
		
		$this->render( 'login' , array( 'aryData' => $aryUserInfo ));		
	}
	
	/**
	 * get redis connection
	 */
	public function getRedis()
	{
		if ( empty( $this->_redis ) )
			$this->_redis = new CRedisFile();
		
		return $this->_redis;
	}
	
	/**
	 * 检查是否已经登入
	 *
	 * @author zhangyi
	 * @date 2014-5-30
	 */
	public function checkIsLogin()
	{
		$aryUserInfo = Nbt::app() -> session -> get( $this -> _sessionName );
		
		if( empty( $aryUserInfo ) )
		{
			$this -> redirect(array('login/login'));
		}
		return true;
	}
	
	
	/**
	 * logout,and redirect login page.
	 */
	public function actionLogout()
	{
		Nbt::app() -> session -> clear();
		setcookie( $this -> _cookeName , '' , time()-1);
		$this->redirect( array( 'login/login' ) );
	}
	

//end class
}
