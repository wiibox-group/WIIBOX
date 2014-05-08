<?php
/**
 * 速度操作类
 *
 * @author wengebin
 * @date 2014-05-08
 */
class SpeedModel extends CModel
{
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

//end class
}
