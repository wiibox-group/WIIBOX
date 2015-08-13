<?php
/**
 * Check Controller
 * 
 * @author wengebin
 * @date 2014-03-06
 */
class CheckController extends BaseController
{
	private $_redis;
	/**
	 * init
	 */
	public function init()
	{
		parent::init();		
	}
	
	/**
	 * Index method
	 */
	public function actionIndex()
	{
		exit();
	}

	/**
	 * Find wiibox, start flash light
	 */
	public function actionFind()
	{
		// execute flash light shell
		if ( SYS_INFO === 'SF3301_D_V1' )
			SocketModel::request( '{"command":"led","parameter":300}' );
		else
		{
			$command = SUDO_COMMAND."/bin/bash ".WEB_ROOT."/soft/gpio_0.sh >/dev/null 2>&1 &";
			exec( $command );
		}

		echo '200';
		exit();
	}

	/**
	 * Stop flash light
	 */
	public function actionStopFind()
	{
		if ( SYS_INFO === 'SF3301_D_V1' )
			SocketModel::request( '{"command":"led","parameter":0}' );
		else
		{
			// find gpio thread
			$command = SUDO_COMMAND.'ps'.( SUDO_COMMAND === '' ? '' : ' -x' ).'|grep gpio_0';
			exec( $command , $output );

			$pids = array();
			foreach ( $output as $r )
			{
				preg_match( '/\s*(\d+)\s*.*/' , $r , $match );
				if ( !empty( $match[1] ) ) $pids[] = $match[1];
			}

			exec( SUDO_COMMAND.'kill -9 '.implode( ' ' , $pids ) );
			echo '200';exit;
		}
	}

	/**
	 * about command lsusb
	 */
	public function actionLsusb()
	{
		// lsusb command
		$command = SUDO_COMMAND.'lsusb';
		exec( $command , $output );

		$aryReturn = array( 'COMMAND'=>0 , 'MILL'=>0 );

		// check result
		if ( !empty( $output ) && count( $output ) > 0 )
		{
			// run command success
			$aryReturn['COMMAND'] = 1;
			// find mill
			$sys = new CSys();
			$strCheckTar = CUtilMachine::getCheckMode( $sys->cursys );
			$strRunMode = RunModel::model()->getRunMode();
			$aryUsb = UsbModel::model()->getUsbCheckResult( $strRunMode , $strCheckTar );
			$aryReturn['MILL'] = count( $aryUsb['usb'] );
		}

		echo json_encode( $aryReturn );
		exit;
	}

	/**
	 * about timer
	 */
	public function actionTimer()
	{
		// lsusb command
		$command = SUDO_COMMAND.'ps'.( SUDO_COMMAND === '' ? '' : ' -x' ).'|grep timer';
		exec( $command , $output );

		$aryReturn = array( 'COMMAND'=>0 , 'FILE'=>0 );

		// check result
		if ( !empty( $output ) && count( $output ) > 0 )
		{
			// find timer thread
			foreach ( $output as $data )
			{
				preg_match( '/.*timer\.sh.*/' , $data , $match_timer );
				if ( !empty( $match_timer[0] ) )
				{
					$aryReturn['COMMAND'] = 1;
				}
			}
		}

		// get timer.sh
		if ( file_exists( '/root/timer.sh' ) )
		{
			// get timer content
			$strContent = file_get_contents( '/root/timer.sh' );
			// match sync
			preg_match( '/.*sync\/start.*/' , $strContent , $match_sync );
			// match mac
			preg_match( '/.*port\/generatemac.*/' , $strContent , $match_mac );
			// match key
			preg_match( '/.*port\/generatekey.*/' , $strContent , $match_key );
			// match checkrun
			preg_match( '/.*index\/checkrun.*/' , $strContent , $match_checkrun );

			if ( !empty( $match_sync[0] ) && !empty( $match_mac[0] ) && !empty( $match_key[0] ) && !empty( $match_checkrun[0] ) )
			{
				$aryReturn['FILE'] = 1;
			}
		}

		echo json_encode( $aryReturn );
		exit;
	}

	/**
	 * about time
	 */
	public function actionDate()
	{
		$aryReturn = array( 'TIME'=>0 , 'ZONE'=>0 );

		// date command
		$command = SUDO_COMMAND.'date -R';
		exec( $command , $output );

		// get current time
		$cur = time();
		$aryReturn['TIME'] = $cur;

		// check
		if ( !empty( $output ) && count( $output ) > 0 ) 
		{
			// match timezone
			preg_match( '/\+0800/' , $output[0] , $match_zone );

			if ( !empty( $match_zone[0] ) ) 
			{
				$aryReturn['ZONE'] = 1;
			}
		}

		echo json_encode( $aryReturn );
		exit;
	}

	/**
	 * about version
	 */
	public function actionVersion()
	{
		$aryReturn = array( 'VERSION'=>CUR_VERSION_NUM );
		echo json_encode( $aryReturn );
		exit;
	}

	/**
	 * about network
	 */
	public function actionNetwork()
	{
		$aryReturn = array( 'NET'=>0 , 'NET_DELAY'=>0 , 'WIIBOX'=>0 , 'WIIBOX_DELAY'=>0 );

		// ping network
		$command_network = SUDO_COMMAND.'ping -c 1 -w 5 61.135.167.36';
		$command_wiibox = SUDO_COMMAND.'ping -c 1 -w 5 www.wiibox.net';

		exec( $command_network , $output_network );
		exec( $command_wiibox , $output_wiibox );

		foreach ( $output_network as $data )
		{
			preg_match( '/.*time=(.*?)\sms.*/' , $data , $match_network );
			if ( !empty( $match_network[0] ) && !empty( $match_network[1] ) )
			{
				$aryReturn['NET'] = 1;
				$aryReturn['NET_DELAY'] = $match_network[1];
			}
		}

		foreach ( $output_wiibox as $data )
		{
			preg_match( '/.*time=(.*?)\sms.*/' , $data , $match_wiibox );
			if ( !empty( $match_wiibox[0] ) && !empty( $match_wiibox[1] ) )
			{
				$aryReturn['WIIBOX'] = 1;
				$aryReturn['WIIBOX_DELAY'] = $match_wiibox[1];
			}
		}

		echo json_encode( $aryReturn );
		exit;
	}

	/**
	 * about ip
	 */
	public function actionIp()
	{
		// get ip
		$os = DIRECTORY_SEPARATOR=='\\' ? "windows" : "linux";
		$mac_addr = new CMac( $os );
		$ip_addr = new CIp( $os );

		$aryReturn = array( 'IP'=>0 , 'MAC'=>0 );

		$aryReturn['IP'] = $ip_addr->ip_addr;
		$aryReturn['MAC'] = $mac_addr->mac_addr;

		echo json_encode( $aryReturn );
		exit;
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
