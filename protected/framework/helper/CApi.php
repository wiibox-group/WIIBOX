<?php
/**
 * API调用,API签名,API签名认证
 *
 */
class CApi
{
	/**
	 * 调用接口处理数据
	 *
	 * @param string $_strRoute	路由
	 * @param array $_aryData 传递的参数
	 * @param string $_strSignKey 加密串
	 * @param boolean $_boolIsStaticUrl 是否是静态地址，如果是静态地址，则参数以 ？ 开始
	 * @return array
	 * 			<pre>
	 * 					return array( 'ISOK'=>bool,'DATA'=>array(),'ERROR'=>'错误号' );
	 * 			</pre>
	 */
	public static function callApi( $_strRoute = "" , $_aryData = array() , $_strSignKey = "" , $_boolIsStaticUrl = false )
	{
		$aryReturn = array( 'ISOK'=>0 , 'DATA'=>array() , 'ERROR'=>'' );
		//补时间戳数据
		$_aryData['time'] = time();

		//对参数进行签名
		$sign = self::sign( $_aryData , $_strSignKey );
		$url = $_strRoute;

		$aryParams = array();
		foreach ( $_aryData as $k=>$v )
			$aryParams[] = "{$k}=".urlencode($v);

		$aryParams[] = "&sign={$sign}";
		$url = $url.( $_boolIsStaticUrl === true ? '?' : '&' ).implode( "&" , $aryParams );

		// 初始化一个 cURL 对象
		$curl = curl_init();
		// 设置你需要抓取的URL
		curl_setopt($curl, CURLOPT_URL, $url);
		// 设置header
		curl_setopt($curl, CURLOPT_HEADER, false);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// 运行cURL，请求网页
		$res = curl_exec($curl);
		// 关闭URL请求
		curl_close($curl);
		$resJson = json_decode( $res, true);

		if( $resJson === false || is_null($resJson) )
		{
			$aryReturn['ISOK'] = 0;
			$aryReturn['ERROR'] = $resJson;
		}
		else
		{
			$aryReturn = $resJson;
		}
		return $aryReturn;
	}
	
	
	/**
	 * 验证签名
	 *	
	 * @param array $_aryData
	 * @param string $_strSign
	 * @return bool
	 */
	public static function verifySign( $_aryData = "" , $_strSign = "" , $_strSignKey = "" )
	{
		$sign = self::sign( $_aryData , $_strSignKey );
		return $sign === $_strSign;
	}
	
	/**
	 * 签名
	 *
	 * @param array $_aryData	需要签名的参数
	 * @return 返回签名结果
	 */
	public static function sign( $_aryData , $_strSignKey = "" )
	{
		ksort( $_aryData );
		reset( $_aryData );		
		$sign = "";
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		while (list ($key, $val) = each ($_aryData)) {
	        $sign .= $key."=".$val."&";
	    }
	    $sign = substr($sign , 0 , count($sign)-2 );//去掉最后一个&字符
	    //拼接后的字符串再与安全校验码直接连接起来
	    $sign .= $_strSignKey;
	    //将字符串签名，获得签名结果
	    $sign = md5($sign);
	    return $sign;
	}
	
//end class
}
