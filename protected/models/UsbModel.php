<?php
/**
 * USB操作类
 *
 * @author wengebin
 * @package framework
 * @date 2014-01-18
 */
class UsbModel extends CModel
{
	// redis object
	private $_redis;

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
	 * USB获得检测结果
	 */
	public function getUsbCheckResult( $_strRunModel = '' , $_strCheckTar = '' )
	{
		if ( empty( $_strRunModel ) )
			return array();

		// is contain GD miner
		$boolHasGD = false;

		if ( ( in_array( $_strRunModel , array( 'B' , 'LB' ) ) && empty( $_strCheckTar ) ) || $_strCheckTar === 'lsusb' )
		{
			$redis = $this->getRedis();
			$aryUsbCache = json_decode( $redis->readByKey( 'usb.check.result' ) , 1 );
			$restartData = json_decode( $redis->readByKey( 'restart.status' ) , 1 );

			$blank_time = $restartData['status'] === 1 ? 5 : 30;
			
			if ( empty( $aryUsbCache ) )
				$aryUsbCache = array( 'usb'=>array() , 'time'=>0 , 'iswrite'=>0 );

			$now = time();
			// if usb state time out
			if ( empty( $aryUsbCache['time'] ) 
					|| $now - $aryUsbCache['time'] > $blank_time 
					|| $now - $aryUsbCache['time'] < 0 
					|| empty( $aryUsbCache['iswrite'] ) )
			{
				$aryUsbCache['iswrite'] = 1;
				$redis->writeByKey( 'usb.check.result' , json_encode( $aryUsbCache , 1 ) );

				@exec( SUDO_COMMAND.'lsusb' , $output );

				$aryUsb = array();
				foreach ( $output as $usb )
				{
					preg_match( '/.*Bus\s(\d+)\sDevice\s(\d+).*CP210x.*/' , $usb , $match_usb );
					if ( !empty( $match_usb[1] ) && !empty( $match_usb[2] ) )
					{
						$boolHasGD = true;
						$strId = intval( $match_usb[1] ).':'.intval( $match_usb[2] );
						$aryUsb[] = $strId;
					}
					else
					{
						preg_match( '/.*Bus\s(\d+)\sDevice\s(\d+).*SGS\sThomson\sMicroelectronics.*/' , $usb , $match_usb );
						if ( !empty( $match_usb[1] ) && !empty( $match_usb[2] ) )
						{
							$strId = intval( $match_usb[1] ).':'.intval( $match_usb[2] );
							$aryUsb[] = $strId;
						}
					}
				}

				// store usb state
				$aryUsbCache['usb'] = $aryUsb;
				$aryUsbCache['time'] = time();
				$aryUsbCache['iswrite'] = 0;
				$aryUsbCache['hasgd'] = $boolHasGD === true ? 1 : 0;
				$redis->writeByKey( 'usb.check.result' , json_encode( $aryUsbCache , 1 ) );
			}
		}
		else if ( empty( $_strCheckTar ) || $_strCheckTar === 'tty' )
		{
			@exec( SUDO_COMMAND.'ls /dev/*ACM*' , $output );

			$aryUsbCache = array();
			$aryUsbCache['usb'] = $output;
			$aryUsbCache['time'] = time();
			$aryUsbCache['hasgd'] = 0;

			return $aryUsbCache;
		}

		return $aryUsbCache;
	}

	/**
	 * check usb is changing
	 */
	public function getUsbChanging( $_strRunModel = '' , $_intSetTimer = 0 , $_strCheckTar = '' )
	{
		if ( empty( $_strRunModel ) )
			return array();

		$aryUsbCache = $this->getUsbCheckResult( $_strRunModel , $_strCheckTar );
		$continue = true;

		//$timer = in_array( $_strRunModel , array( 'B' , 'LB' ) ) ? 6 : 2;
		$timer = 6;
		if ( $_intSetTimer > 0 )
			$timer = $_intSetTimer;

		$time_last = $timer;

		// when has more machine change
		while( $continue )
		{
			sleep( 1 );
			$time_last --;

			$search_time_start = time();
			$newAryUsbCache = $this->getUsbCheckResult( $_strRunModel , $_strCheckTar );
			$search_time_end = time();

			$time_last -= floor( $search_time_end - $search_time_start );

			if ( count( $newAryUsbCache['usb'] ) != count( $aryUsbCache['usb'] ) )	
				$time_last = $timer;

			if ( $time_last < 0 )
				$continue = false;

			$aryUsbCache = $newAryUsbCache;
		}

		return $aryUsbCache;
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

//end class
}
