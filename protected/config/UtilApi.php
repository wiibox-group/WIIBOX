<?php
/**
 * 教师模块API静态方法
 * 
 * @author wengebin
 * @date 2013-09-17
 */
class UtilApi
{
	/**
	 * 检查性版本
	 *
	 * @param string $_strVersion 当前版本号
	 * @return array
	 * 			<pre>
	 * 					return array( 'ISOK'=>bool,'DATA'=>array(),'ERROR'=>'错误号' );
	 * 			</pre>
	 */
	public static function callCheckNewVersion( $_strVersion = '' )
	{
		$aryData = array();

		// cur version
		$aryData['version'] = $_strVersion;

		return CApi::callApi( MAIN_DOMAIN."/checkversion" , $aryData , MAIN_DOMAIN_KEY , true );
	}

	/**
	 * 同步数据
	 *
	 * @param array $_aryData 需要同步传递的数据
	 * @return array
	 * 			<pre>
	 * 					return array( 'ISOK'=>bool,'DATA'=>array(),'ERROR'=>'错误号' );
	 * 			</pre>
	 */
	public static function callSyncData( $_aryData = '' )
	{
		return CApi::callApi( MAIN_DOMAIN."/sync" , $_aryData , MAIN_DOMAIN_KEY , true );
	}
	
	/**
	 * 同步本地速度
	 * 
	 * @param string $_aryData
	 * @return array 返回值
	 * 
	 * @author zhangyi
	 * @date 2014-6-13
	 */
	public static function callSyncSpeedData( $_aryData = '' )
	{
		return CApi::callApi( MAIN_DOMAIN.'/syncSpeed' , $_aryData , MAIN_DOMAIN_KEY, true );
	}

	/**
	 * 解除绑定
	 *
	 * @param array $_strKey 需要解除绑定的设备KEY
	 * @return array
	 * 			<pre>
	 * 					return array( 'ISOK'=>bool,'DATA'=>array(),'ERROR'=>'错误号' );
	 * 			</pre>
	 */
	public static function callCancelbind( $_strKey = '' )
	{
		return CApi::callApi( MAIN_DOMAIN."/cancelbindNet" , array( 'key'=>$_strKey ) , MAIN_DOMAIN_KEY , true );
	}
	
	//end class
}
