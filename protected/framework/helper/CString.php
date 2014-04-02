<?php
/**
 * CString class file.
 * 
 * 
 * 
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-09-16
 */
class CString
{
	/**
	 * 密码加密
	 *
	 * 
	 */
	public static function encodeAdminPassword( $_strPwd = '' )
	{
		return md5( "qinxue-admin-".$_strPwd );
	}
	
	/**
	 * 密码加密
	 *
	 * 
	 */
	public static function encodeTeacherPassword( $_strPwd = '' )
	{
		return md5( "teacher-admin-".$_strPwd );
	}
	
	/**
	 * 密码加密
	 *
	 * 
	 */
	public static function encodeMemberPassword( $_strPwd = '' )
	{
		return md5( "member-admin-".$_strPwd );
	}

	/**
	 * 密码加密
	 *
	 * 
	 */
	public static function encodeUserPassword( $_strPwd = '' )
	{
		return md5( "www-user-".$_strPwd );
	}

	/**
	 * 密保答案加密
	 */
	public static function encodeUserPasswordAnswer( $_strPwd = '' )
	{
		return md5( "password-answer-".$_strPwd );
	}
	
	/**
	* Passport 加密函数
	*
	* @param	 string	 等待加密的原字串
	* @param	 string	 私有密匙(用于解密和加密)
	*
	* @return	string	 原字串经过私有密匙加密后的结果
	*/
	public static function encode( $_strVal = "" , $_strKey = "" )
	{
		// 使用随机数发生器产生 0~32000 的值并 MD5()
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		
		// 变量初始化
		$ctr = 0;
		$tmp = '';
		
		// for 循环，$i 为从 0 开始，到小于 $_strVal 字串长度的整数
		for($i = 0; $i < strlen($_strVal); $i++)
		{
			// 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			// $tmp 字串在末尾增加两位，其第一位内容为 $encrypt_key 的第 $ctr 位，
			// 第二位内容为 $_strVal 的第 $i 位与 $encrypt_key 的 $ctr 位取异或。然后 $ctr = $ctr + 1
			$tmp .= $encrypt_key[$ctr].($_strVal[$i] ^ $encrypt_key[$ctr++]);
		}
		
		// 返回结果，结果为 passport_key() 函数返回值的 base65 编码结果
		return base64_encode(self::doOp($tmp, $_strKey));
	
	}
	
	/**
	* Passport 解密函数
	*
	* @param	 string	 加密后的字串
	* @param	 string	 私有密匙(用于解密和加密)
	*
	* @return	string	 字串经过私有密匙解密后的结果
	*/
	public static function decode( $_strVal = "" , $_strKey = "" )
	{
	
		// $_strVal 的结果为加密后的字串经过 base64 解码，然后与私有密匙一起，
		// 经过 passport_key() 函数处理后的返回值
		$_strVal = self::doOp(base64_decode($_strVal), $_strKey);
		
		// 变量初始化
		$tmp = '';
		
		// for 循环，$i 为从 0 开始，到小于 $_strVal 字串长度的整数
		for ($i = 0; $i < strlen($_strVal); $i++)
		{
			// $tmp 字串在末尾增加一位，其内容为 $_strVal 的第 $i 位，
			// 与 $_strVal 的第 $i + 1 位取异或。然后 $i = $i + 1
			$tmp .= $_strVal[$i] ^ $_strVal[++$i];
		}
		
		// 返回 $tmp 的值作为结果
		return $tmp;
	
	}
	
	/**
	* Passport 密匙处理函数
	*
	* @param	 string	 待加密或待解密的字串
	* @param	 string	 私有密匙(用于解密和加密)
	*
	* @return	string	 处理后的密匙
	*/
	public static function doOp( $_strVal = "" , $_strKey = "" )
	{	
		// 将 $encrypt_key 赋为 $encrypt_key 经 md5() 后的值
		$strKey = md5( $_strKey );
		
		// 变量初始化
		$ctr = 0;
		$tmp = '';
		
		// for 循环，$i 为从 0 开始，到小于 $_strVal 字串长度的整数
		for($i = 0; $i < strlen($_strVal); $i++) {
			// 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
			$ctr = $ctr == strlen($strKey) ? 0 : $ctr;
			// $tmp 字串在末尾增加一位，其内容为 $_strVal 的第 $i 位，
			// 与 $encrypt_key 的第 $ctr + 1 位取异或。然后 $ctr = $ctr + 1
			$tmp .= $_strVal[$i] ^ $strKey[$ctr++];
		}
	
		// 返回 $tmp 的值作为结果
		return $tmp;
	}	
	
//end class
}
