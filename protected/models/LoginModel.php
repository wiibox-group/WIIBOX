<?php
/**
 * 登入模型
 * 
 * @author zhangyi
 * @date 2014-5-29
 */
class LoginModel extends CModel
{
	private $_redis;
	private $_fileName = 'user.pwd';
	
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 返回惟一实例
	 *
	 * @return Model
	 */
	public static function model( $className = __CLASS__ )
	{
		return parent::model( __CLASS__ );
	}
	
	/**
	 * 用户信息
	 * @param string $strVerifyType 登入形式,例如输入密码登入 或者cookie登入
	 * @param array $aryUserInfo 传进来的用户数据
	 * 
	 * @author zhangyi
	 * @date 2014-5-29
	 */
	public function checkLogin( $aryData = array() )
	{
		if( empty( $aryData ) )
			throw new CModelException( CUtil::i18n( 'exception,sys_error' ) );
		
		$aryUserInfo = $this -> getUserInfo();
		
		//验证用户名和密码
		return ( $aryUserInfo['uname'] === $aryData['uname'] && $aryUserInfo['pwd'] === $aryData['pwd'] );
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
	 * 获取用户名
	 * 
	 * @author zhangyi
	 * @date 2014-5-29
	 */
	public function getFileName()
	{
		return $this -> _fileName;
	}
	
	/**
	 * 获取控制板上 用户密码
	 * 
	 * @return String 用户密码
	 * 
	 * @author zhangyi
	 * @date 2014-5-29
	 */
	public function getUserPwd()
	{
		//获取之前判断是否存在用户信息数据
		$this -> createDefaultUserInfo();
		
		$aryUserInfo = $this -> getUserInfo();
		return $aryUserInfo['pwd'];
	}
	
	/**
	 * 创建默认用户
	 * 
	 * @author zhangyi
	 * @date 2014-5-30
	 */
	public function createDefaultUserInfo()
	{
		//判断是否存在用户信息文件来确定是否创建用户文件 
		$redis = $this -> getRedis();
		$aryUserInfo = $redis -> readByKey( $this -> _fileName );
		if( empty($aryUserInfo) )
		{
			return $redis -> writeByKey( $this -> _fileName , json_encode(
						array(
							'uname' => UUSERNAME ,
							'pwd' => CString::encodeMachinePassword( UPASSWORD ) 
						) ) );
		}
		else 
			return false;
	}
	
	/**
	 * 获取控制板上用户名和密码
	 * 
	 * @throws CModelException
	 * 
	 * @author zhangyi
	 * @date 2014-5-29
	 */
	private function getUserInfo()
	{
		//获取控制器上用户名和密码
		$redis = $this -> getRedis();
		$aryUserInfo = $redis -> readByKey( $this -> _fileName );
		
		if( empty( $aryUserInfo ) )
			$aryUserInfo = '{}';
		return json_decode( $aryUserInfo , 1 );
	}
	
	/**
	 * 修改用户密码
	 * @param string $_strPwd 用户密码
	 * 
	 * @author zhangyi
	 * @date 2014-5-30
	 */
	public function updatePwd( $_strPwd = '' )
	{
		$redis = $this -> getRedis();
		
		//首先检查是否存在默认密码 
		$this -> createDefaultUserInfo();
		
		//修改密码
		$userInfo = $this -> getUserInfo();
		$userInfo['pwd'] = $_strPwd;
		return $redis -> writeByKey( $this -> _fileName , json_encode( $userInfo ) );
	}
}
