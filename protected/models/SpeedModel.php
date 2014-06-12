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
	
	

//end class
}