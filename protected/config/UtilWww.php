<?php
class UtilWww
{
	const PLAY_KEY = "www";
	const VIDEO_ACCESS_KEY = "password";
	
	/**
	 * @author  zhaojingyun
	 *将传入的UID 进行md5加密 以及取出MD5后的数字
	 * @param INT $_str
	 * @return string
	 */
	public static function md5FindNum($_str=null)
	{
		$_str=md5(trim($_str));
		if(empty($_str)){return '';}
		$aryTemp=array('1','2','3','4','5','6','7','8','9','0');
		$result='';
		$aryString = array();
		$aryString = str_split($_str);
		foreach ($aryString as $key=>$val)
		{
			if(in_array($_str[$key],$aryTemp))
			{
				$result.=$_str[$key];        
			}	
		}
			return $result;
	}
	
	public static function checkUid( $_uId )
	{
		$_uId = intval($_uId);
		if($_uId < 0){
			return false;
		}
		return true;
	}
	
	/**
	 * 生成播放链接的加密串
	 *
	 * @param int $_intTuId			教程ID
	 * @param int $_intTucId		章节ID
	 * @param int $_intUid			用户ID
	 * @return string
	 */
	public static function encodePlayKey( $_intTuId = 0 , $_intTucId = 0 , $_intUid = 0 )
	{
		$intTimestamp = time();
		$strKey = "{$_intTuId}|{$_intTucId}|{$_intUid}|{$intTimestamp}";
		return CString::encode( $strKey , self::PLAY_KEY );
	}
	
	/**
	 * 解密播放链接的解密串
	 * <pre>
	 * 		return array(
	 * 						'tuid'=>0,			//教程ID
	 * 						'tucid'=>0,			//章节ID
	 * 						'uid'=>0,			//用户ID
	 * 						'timestamp'=>0		//时间戳
	 * 					);
	 * </pre>
	 *
	 * @param string $_strKey
	 * @return array
	 */
	public static function decodePlayKey( $_strKey = "" )
	{
		$strDecodeKey = CString::decode( $_strKey , self::PLAY_KEY );
		$strDecodeKey = trim( $strDecodeKey );
		$aryDecode = explode( '|' , $strDecodeKey );		
		if( empty( $aryDecode ) || count( $aryDecode ) != 4 )
			throw new CModelException( "无法解析的KEY！" );
		//$aryTimestamp = explode('*',$aryDecode[3]);
		//$aryDecode[3] = $aryTimestamp[1];
		return array( 'tuid'=>$aryDecode[0] , 'tucid'=>$aryDecode[1] , 'uid'=>$aryDecode[2] , 'timestamp'=>$aryDecode[3] );
	}
	
	/**
	 * 给视频文件地址加上accesskey,防盗链防下载
	 *
	 * @param string $_urlFile
	 */
	public static function encodeVideoFile( $_urlFile = "" )
	{
		$ipkey= md5( self::VIDEO_ACCESS_KEY.$_SERVER['REMOTE_ADDR'] );
		return $_urlFile.'?key='.$ipkey;
	}
//end class
}
?>
