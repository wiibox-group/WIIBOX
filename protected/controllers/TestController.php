<?php
/**
 * Test Controller
 * @author yang
 * @date 2013-8-4
 */
class TestController extends BaseController
{
	/** redis object **/
	private $_redis;
	
	/** curent every usb setting **/
	private $_usbSet = array();

	/** system message **/
	private $_sys = '';

	/** run mode **/
	private $_runMode = '';
	
	/**
	 * init
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 *  Index method
	 */
	public function actionIndex()
	{
		//get 
		$redis = $this->getRedis();

		$arySpeedData = SpeedModel::getSpeedDataByApi();

		// get history accept
		$historyLog = $redis->readByKey( 'speed.history.log' );
		$aryHistory = json_decode( $historyLog , 1 );

		echo "<pre>";
		print_r($arySpeedData);
		print_r($aryHistory);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function actionClearLog()
	{
		$reids = $this->getRedis();
		// get run mode
		$strRunMode = $this->getRunMode();
		// get check mode
		$strCheckTar = $this->getCheckMode();
		// get work time interval
		$intWorkTimeInterval = $this->getDefaultInterval();

		// get usb cache
		$aryUsbCache = UsbModel::model()->getUsbCheckResult( $strRunMode , $strCheckTar );
		$aryUsb = $aryUsbCache['usb'];

		//get 
		$redis = $this->getRedis();
		$speedLog = $redis->readByKey( 'speed.log' );
		$countLog = $redis->readByKey( 'speed.count.log' );
	 
		

		$speedData = json_decode( $speedLog , 1 );
		$countData = json_decode( $countLog , 1 );

		$now = time();

		if ( empty( $speedLog ) || empty( $speedData ) )
			$speedData = array('BTC'=>array(),'LTC'=>array());
		if ( empty( $countLog ) || empty( $countData ) )
			$countData = array(
					'BTC'=>array('A'=>0,'R'=>0,'T'=>$now,'LC'=>$now),
					'LTC'=>array('A'=>0,'R'=>0,'T'=>$now,'LC'=>$now)
					);

		

		$boolIsNeedRestart = false;
		$newData = array('BTC'=>array(),'LTC'=>array());

		switch ( $strCheckTar )
		{
			// btc mode use spi agreement
			case 'spi-btc':
			// btc mode use lsusb agreement
			case 'lsusb-btc':
			// btc mode use tty agreement
			case 'tty-btc':

				// parse data
				foreach ( $arySpeedData as $key=>$data )
				{
					// more than 5 minutes restart
					if ( $now - $data['LAST'] > $intWorkTimeInterval )
						$boolIsNeedRestart = true;

					// if speed too low
					if ( $doubleHighSpeed > 0 && $data['RUN'] > 30 && floatval( $data['S'] ) - $doubleHighSpeed * 0.8 < 0 )
						$boolIsNeedRestart = true;

					$intHistoryA = empty( $aryHistory[$key] ) ? 0 : $aryHistory[$key]['A'];
					if ( intval( $data['A'] ) < $intHistoryA ) $intHistoryA = 0;
					$countData['BTC']['A'] += intval( $data['A'] ) - $intHistoryA;

					$intHistoryR = empty( $aryHistory[$key] ) ? 0 : $aryHistory[$key]['R'];
					if ( intval( $data['R'] ) < $intHistoryR ) $intHistoryR = 0;
					$countData['BTC']['R'] += intval( $data['R'] ) - $intHistoryR;

					$aryHistory[$key]['A'] = intval($data['A']);
					$aryHistory[$key]['R'] = intval($data['R']);
				}

				$countData['BTC']['LC'] = $now;

				// write history log
				$redis->writeByKey( 'speed.history.log' , json_encode( $aryHistory ) );

				// end btc mode
				break;

			// ltc mode use tty agreement
			case 'tty-ltc':
			// ltc mode use usb but data from api
			case 'lsusb-api':
			// ltc mode use spi agreement
			case 'spi-ltc':

				// get speed data
				$arySpeedData = SpeedModel::getSpeedDataByApi();
				
				// get history accept
				$historyLog = $redis->readByKey( 'speed.history.log' );
				$aryHistory = json_decode( $historyLog , 1 );
				
				// high speed
				$doubleHighSpeed = 0;
				$speedCountResult['error'] = 0;

				foreach ( $arySpeedData as $key=>$data )
				{
					$doubleHighSpeed = max( $doubleHighSpeed , floatval( $data['S'] ) );
					if(isset($aryHistory[$key]['A'])&&$aryHistory[$key]['A']>=$arySpeedData[$key]['A'])
					{
						$speedCountResult['error']++;
					}
					echo '<h3>['.$key.']historyAccepted:'.$aryHistory[$key]['A'].':CurrentAcceptd:'.$arySpeedData[$key]['A'].'</h3>';
				}
				
				$speedCountResult['normal'] = count($arySpeedData) - $speedCountResult['error'];
				$redis->writeByKey('speed.count.result' , json_encode($speedCountResult));

				// parse data
				foreach ( $arySpeedData as $key=>$data )
				{
					// more than 5 minutes restart
					if ( $now - $data['LAST'] > $intWorkTimeInterval )
						$boolIsNeedRestart = true;

					// if speed too low
					if ( $doubleHighSpeed > 0 && $data['RUN'] > 30 && floatval( $data['S'] ) - $doubleHighSpeed * 0.8 < 0 )
						$boolIsNeedRestart = true;

					$intHistoryA = empty( $aryHistory[$key] ) ? 0 : $aryHistory[$key]['A'];
					if ( intval( $data['A'] ) < $intHistoryA ) $intHistoryA = 0;
					$countData['LTC']['A'] += intval( $data['A'] ) - $intHistoryA;

					$intHistoryR = empty( $aryHistory[$key] ) ? 0 : $aryHistory[$key]['R'];
					if ( intval( $data['R'] ) < $intHistoryR ) $intHistoryR = 0;
					$countData['LTC']['R'] += intval( $data['R'] ) - $intHistoryR;

					$aryHistory[$key]['A'] = intval($data['A']);
					$aryHistory[$key]['R'] = intval($data['R']);
				}

				$countData['LTC']['LC'] = $now;

				// write history log
				$redis->writeByKey( 'speed.history.log' , json_encode( $aryHistory ) );
				echo "<pre>";
				print_r($speedCountResult);
				
				echo $strCheckTar.':history:';
				print_r($aryHistory);
				echo "</pre>";

				// end spi mode
				break;

			// other mode and agreement
			default:
				
				if ( in_array( $strRunMode , array( 'B' , 'LB' ) ) )
					$newData['BTC'] = $speedData['BTC'];

				if ( in_array( $strRunMode , array( 'L' , 'LB' ) ) )
				{
					foreach ( $speedData['LTC'] as $k=>$d )
					{
						if ( in_array( $k , $aryUsb ) )
							$newData['LTC'][$k] = $d;
					}
				}

				if ( in_array( $strRunMode , array( 'L' , 'LB' ) ) )
				{
					foreach ( $aryUsb as $usb )
					{
						if ( !array_key_exists( $usb , $newData['LTC'] ) )
							$newData['LTC'][$usb] = array( 'A'=>0 , 'R'=>0 , 'T'=>$now);
					}
				}

				$log_dir = '/tmp';
				$btc_log_dir = $log_dir.'/btc';
				$ltc_log_dir = $log_dir.'/ltc';

				if ( file_exists( $btc_log_dir ) )
					$btc_dir_source = opendir( $btc_log_dir );

				$btc_need_check_time = false;
				while ( isset( $btc_dir_source ) && ( $file  = readdir( $btc_dir_source ) ) !== false )
				{
					// get child directory
					$sub_dir = $btc_log_dir.DIRECTORY_SEPARATOR.$file;
					if ( $file == '.' || $file == '..' )
						continue;
					else
					{
						$val = file_get_contents( $sub_dir );
						$valData = explode( '|', $val );
						
						if ( $valData[2] == 'A' )
						{
							$newData['BTC']['A'] ++;
							$countData['BTC']['A'] ++;
						}
						else if ( $valData['2'] == 'R' )
						{
							$newData['BTC']['R'] ++;
							$countData['BTC']['R'] ++;
						}

						$newData['BTC']['T'] = $now;
						$countData['BTC']['T'] = $now;

						unlink( $sub_dir );
						$btc_need_check_time = true;
					}
				}
				
				if ( $btc_need_check_time === true || empty( $countData['BTC']['LC'] ) )
					$countData['BTC']['LC'] = $now;

				// is need restart
				if ( in_array( $strRunMode , array( 'B' , 'LB' ) ) 
						&& ( $btc_need_check_time 
							|| $now - $countData['BTC']['LC'] > $intWorkTimeInterval 
							|| $now - $countData['BTC']['LC'] < 0 
						) 
				)
				{
					if ( $now - $newData['BTC']['T'] > $intWorkTimeInterval || $now - $newData['BTC']['T'] < 0 )
						$boolIsNeedRestart = true;
				}

				if ( file_exists( $ltc_log_dir ) )
					$ltc_dir_source = opendir( $ltc_log_dir );

				$ltc_need_check_time = false;
				while ( isset( $ltc_dir_source ) && ( $file  = readdir( $ltc_dir_source ) ) !== false )
				{
					// get child directory
					$sub_dir = $ltc_log_dir.DIRECTORY_SEPARATOR.$file;
					if ( $file == '.' || $file == '..' )
						continue;
					else
					{
						$val = file_get_contents( $sub_dir );
						$valData = explode( '|', $val );

						// machine id
						$id = $valData[0];

						if ( !array_key_exists( $id , $newData['LTC'] ) )
						{
							unlink( $sub_dir );
							continue;
						}
					
						if ( $valData[2] == 'A' )
						{
							$newData['LTC'][$id]['A'] ++;
							$countData['LTC']['A'] ++;
						}
						else if ( $valData['2'] == 'R' )
						{
							$newData['LTC'][$id]['R'] ++;
							$countData['LTC']['R'] ++;
						}

						$newData['LTC'][$id]['T'] = $now;
						$countData['LTC']['T'] = $now;

						unlink( $sub_dir );
						$ltc_need_check_time = true;
					}
				}
				
				
				if ( $ltc_need_check_time === true || empty( $countData['LTC']['LC'] ) )
					$countData['LTC']['LC'] = $now;
					
				if ( in_array( $strRunMode , array( 'L' , 'LB' ) ) 
						&& ( $ltc_need_check_time 
							|| $now - $countData['LTC']['LC'] > $intWorkTimeInterval 
							|| $now - $countData['LTC']['LC'] < 0 ) 
				)
				{
					foreach ( $newData['LTC'] as $m )
					{
						if ( $now - $m['T'] > $intWorkTimeInterval || $now - $m['T'] < 0 )
						{
							$boolIsNeedRestart = true;
							break;
						}
					}
					
					if ( $boolIsNeedRestart === false 
							&& ( $now - $countData['LTC']['LC'] > $intWorkTimeInterval 
								|| $now - $countData['LTC']['LC'] < 0 ) 
					)
						$boolIsNeedRestart = true;
				}

				if ( empty( $speedData['lastlog'] ) )
					$boolIsNeedRestart = false;

				// end tty/lsusb mode
				break;
		}

	}






//other 

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
	 * Get run mode
	 */
	public function getRunMode()
	{
		if ( empty( $this->_runMode ) )
		{
			$strRunMode = RunModel::model()->getRunMode();
			$this->_runMode = $strRunMode;
		}

		return $this->_runMode;
	}

	/**
	 * Get default speed
	 */
	public function getDefaultSpeed()
	{
		return CUtilMachine::getDefaultSpeed(SYS_INFO);
	}

	/**
	 * Get default work time interval
	 */
	public function getDefaultInterval()
	{
		return CUtilMachine::getDefaultInterval(SYS_INFO);
	}

	/**
	 * Get check mode
	 */
	public function getCheckMode()
	{
		// get current system
		$strSys = $this->getSystem();
		return CUtilMachine::getCheckMode( $strSys );
	}

	/**
	 * Get system
	 */
	public function getSystem()
	{
		if ( empty( $this->_sys ) )
		{
			// get system
			$sys = new CSys();
			$this->_sys = $sys->cursys;
		}

		return $this->_sys;
	}

	/**
	 * Reboot
	 */
	public function actionReboot()
	{
		$command = SUDO_COMMAND.'reboot';
		@exec( $command );

		return true;
	}


//end class
}
	