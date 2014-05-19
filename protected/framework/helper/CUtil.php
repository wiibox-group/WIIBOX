<?php
/**
 * 通用的功能
 *
 */
class CUtil
{
	/**性别-男*/
	const SEX_MAN = 1;
	/**性别-女*/
	const SEX_WOMEN = 2;
	
	/**
	 * 性别
	 *
	 */
	public static function getSex( $_intV = 9999 )
	{
		$aryData = array( self::SEX_MAN=>CUtil::i18n('framework,cutil_gender_boy') 
						, self::SEX_WOMEN=>CUtil::i18n('framework,cutil_gender_girl') );
		if( is_null( $_intV ) )
			return $aryData;
		else
			return isset( $aryData[$_intV] ) ? $aryData[$_intV] : '-';
	}
	
	/**
	 * 时间戳过期检查
	 *
	 * @params int $_intTimestamp	客户端传递的时间戳
	 * @params int $_intOverdueTime	过期时间间隔
	 */
	public static function checkTimpStampOverdue( $_intTimestamp = 0 , $_intOverdueTime = 0 )
	{
		if ( empty($_intTimestamp) )
			return false;

		if ( empty($_intOverdueTime) )
			return false;

		if ( time() - $_intTimestamp > $_intOverdueTime )
			return false;

		return true;
	}

	/**
	 * 判断字符串中是否有中文
	 *
	 * @params string $_str 字符串
	 * @return bool
	 */
	public static function isContainChinese( $_str )
	{
		if( preg_match( '/([\x80-\xFE][\x40-\x7E\x80-\xFE])+/' , $_str ) )
			return true;
		else
			return false;
	}

	/**
	 * 判断文件是否是RAR文件
	 *
	 * @params string $_strFileName 文件名
	 * @return bool
	 */
	public static function isRar( $_strFileName )
	{
		if ( empty( $_strFileName ) )
			return false;

		// 分割文件名，并获得后缀名
		$splits = explode('.', $_strFileName);
		$suffix = $splits[ count($splits) - 1 ];

		// 判断是否是 rar 文件
		if( strtoupper($suffix) === 'RAR' )
			return true;
		else
			return false;
	}
	
	/**
	 * 根据随机数判断是否需要执行某一操作。
	 *
	 * @return false;
	 */
	public static function isExecuteByRandom()
	{
		mt_srand((double)microtime() * 1000000);
		$intNumber = mt_rand(1, 9999);
		if( $intNumber%100 == 99 )
			return true;
		return false;
	}
	
	/**
	 * 获得用户的真实IP地址
	 *
	 * @access  public
	 * @return  string
	 */
	public static function realIp()
	{
	    if (isset($_SERVER))
	    {
	        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        {
	            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	
	            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
	            foreach ($arr AS $ip)
	            {
	                $ip = trim($ip);
	
	                if ($ip != 'unknown')
	                {
	                    $realip = $ip;
	
	                    break;
	                }
	            }
	        }
	        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
	        {
	            $realip = $_SERVER['HTTP_CLIENT_IP'];
	        }
	        else
	        {
	            if (isset($_SERVER['REMOTE_ADDR']))
	            {
	                $realip = $_SERVER['REMOTE_ADDR'];
	            }
	            else
	            {
	                $realip = '0.0.0.0';
	            }
	        }
	    }
	    else
	    {
	        if (getenv('HTTP_X_FORWARDED_FOR'))
	        {
	            $realip = getenv('HTTP_X_FORWARDED_FOR');
	        }
	        elseif (getenv('HTTP_CLIENT_IP'))
	        {
	            $realip = getenv('HTTP_CLIENT_IP');
	        }
	        else
	        {
	            $realip = getenv('REMOTE_ADDR');
	        }
	    }
	
	    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
	    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
	
	    return $realip;
	}

	/**
	 * 计算时间距离现在多久
	 *
	 * @param int $_timestamp 时间戳
	 * @return string
	 */
	public function tranTime( $_timestamp ){
		$rtime = date("Y".CUtil::i18n('framework,cutil_time_year')."m"
				.CUtil::i18n('framework,cutil_time_month')."d"
				.CUtil::i18n('framework,cutil_time_day')." H:i:s", $_timestamp);
		$rtime = str_replace( date('Y'.CUtil::i18n('framework,cutil_time_year')) , '' , $rtime );
		$htime = date("H:i:s", $_timestamp);

		$now = time();
		// 时间戳距离现在的时间
		$time = $now - $_timestamp;
		// 昨天凌晨时间
		$timey = strtotime(date('Y-m-d')) - 24*3600;
		// 前天凌晨时间
		$timeby = $timey - 24*3600;

		if ($time < 60)
		{
			$str = CUtil::i18n('framework,cutil_time_just');
		}
		elseif ($time < 60 * 60)
		{
			$min = floor($time/60);
			$str = $min.CUtil::i18n('framework,cutil_time_minuteAgo');
		}
		elseif ($time < 60 * 60 * 24)
		{
			$h = floor($time/(60*60));
			$str = $h.CUtil::i18n('framework,cutil_time_hourAgo');
		}
		elseif ($timey < $_timestamp)
		{
			$str = CUtil::i18n('framework,cutil_time_yesterday').$htime;
		}
		elseif ($timeby < $_timestamp)
		{
			$str = CUtil::i18n('framework,cutil_time_beforeYesterday').$htime;
		}
		else
		{
			$str = $rtime;
		}

		return $str;
	}

	/**
	 * 配置是否为空
	 *
	 * @param array $_aryParams 配置
	 * @return bool
	 */
	public static function isParamsEmpty( $_aryParams = array() )
	{
		if ( empty( $_aryParams['ad'] ) 
				|| empty( $_aryParams['ac'] )
				|| empty( $_aryParams['pw'] ) )
			return false;

		return true;
	}
	
	/**
	 * 根据key获取指定的值
	 * 
	 * @param String $_strkey 文件key值,格式('文件前缀,文件中key值')
	 * @return String 该key所对应的value值
	 * 
	 * @author zhangyi
	 * @date 2014-5-15
	 */
	public static function i18n($_strkey)
	{
		return Nbt::app()->language->i18n($_strkey);
	}
	
	/**
	 * 获取语言
	 * 
	 * @author zhangyi
	 * @date 2014-5-15
	 */
	public static function getLanguage()
	{
		return Nbt::app() -> language -> getLanguage();
	}
	

//end class
}
