<?php
/**
 * 速度操作类
 *
 * @author wengebin
 * @date 2014-05-08
 */
class SpeedModel extends CModel
{
	
	public $_fileName = 'list.speed.log';
	private $_redis;
	
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
	 * 获得速度数据
	 */
	public static function getSpeedDataByApi()
	{
		$aryApiData = SocketModel::request( 'devs' );

		$aryUsbData = array();
		foreach ( $aryApiData as $data ) 
		{
			if ( !isset( $data['ASC'] ) )
				continue;

			$aryUsb = 'ASC'.$data['ASC'];
			$aryUsbData[$aryUsb] = array( 'A'=>$data['Accepted'] , 
										'R'=>$data['Rejected'] , 
										'S'=>$data['MHS av'] , 
										'RUN'=>$data['Device Elapsed'],
										'LAST'=>$data['Last Share Time']
									);
		}

		return $aryUsbData;
	}
	
	/**
	 * 
	 * 根据文件获取数据
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 * 
	 */
	public function getSpeedDataByFile()
	{
		$aryData = $this -> getRedis() -> readByKey( $this -> _fileName );
		if( !empty( $aryData ) )
			return json_decode( $aryData , 1 );
		return array();
	}
	
	/**
	 * 将数据写入文件
	 * 
	 * @param array $_aryData 需要写入的数据
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 */
	public function storeSpeedData( $_aryData = array() )
	{
		if( empty( $_aryData ) )
			return false;
		return $this -> getRedis() -> writeByKey( $this -> _fileName , json_encode( $_aryData ) );
	}
	
	/**
	 * 注意:此方法仅供测试 
	 * 
	 * 去树莓派上面获取运行数据 
	 * 
	 * 
	 * @author zhangyi
	 * @date 2014-6-4
	 */
	public function getSpeedDataByCurl()
	{
		
		$url = 'http://192.168.1.161/index.php?r=index/getSpeed';
		// 初始化一个 cURL 对象
		$curl = curl_init();
		// 设置你需要抓取的URL
		curl_setopt($curl, CURLOPT_URL, $url);
		// 设置header
		curl_setopt($curl, CURLOPT_HEADER, false);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// 设置cURL 最长执行时间
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		// 关闭URL请求
		curl_close($curl);
		$aryData = json_decode( $res, true);
		
		return $aryData;
		
	}
	
	/**
	 * 注意:此方法仅供测试
	 * 
	 * 去树莓派上获取矿机数量
	 * 
	 * @author zhangyi
	 * @date 2014-6-9
	 */
	public function getCheckDataCurl()
	{
		$url = 'http://192.168.1.161/index.php?r=index/check';

		// 初始化一个 cURL 对象
		$curl = curl_init();
		// 设置你需要抓取的URL
		curl_setopt($curl, CURLOPT_URL, $url);
		// 设置header
		curl_setopt($curl, CURLOPT_HEADER, false);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// 设置cURL 最长执行时间
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		// 运行cURL，请求网页
		$res = curl_exec($curl);
		// 关闭URL请求
		curl_close($curl);
		$aryData = json_decode( $res, true);
		
		return $aryData;
		
	}
	
	/**
	 * 获取文件路径
	 * @return string
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 */
	public function getFilePath()
	{
		$redis = $this -> getRedis();
		return $redis -> getFilePath( $this -> _fileName );
	}
	
	/**
	 * 获取redis
	 * 
	 * @return CRedisFile
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 */
	public function getRedis()
	{
		if( empty( $this -> _redis ) )
			$this -> _redis = new CRedisFile();
		return $this -> _redis;
	}
	
	/**
	 * 获取控制器总算力
	 * 
	 * @return Ambigous <number, unknown>
	 * 
	 * @author zhangyi
	 * @date 2014-6-13
	 */
	public function getSpeedSum()
	{
		//由于此程序未发布到树莓派上,此处获取数据代码仅供测试
		$aryData = $this -> getSpeedDataByCurl();
		$aryData = $aryData['DATA'];
		$strRunModel = $aryData['run'];
		$arySpeedData = $aryData['value'];
		
		//正式获取数据代码
// 		$arySpeedData = $this -> getSpeedDataByApi();
		
		//获取总算力
		$intSpeedSum = 0;
		if( empty( $arySpeedData ) )
			//$intSpeedSum = 0;
			$intSpeedSum = rand(10,100); 
		else
		{
			foreach ( $arySpeedData as $info )
				$intSpeedSum += $info['S'];
		}
		
		return $intSpeedSum;
	}
	
	/**
	 * 获取运行模式
	 * 
	 * @author zhangyi
	 * @date 2014-6-13
	 */
	public function getRunModel()
	{
// 		return RunModel::model() -> getRunModel();
		return 'L';
	}
	
	

//end class
}
