<?php
/**
 * Synchronize Controller
 * 
 * @author wengebin
 * @date 2014-01-06
 */
class SyncController extends BaseController
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
	 * Start sync
	 */
	public function actionStart()
	{
		// generate key
		$os = DIRECTORY_SEPARATOR=='\\' ? "windows" : "linux";
		$mac_addr = new CMac( $os );
		$ip_addr = new CIp( $os );
		
		// get system
		$sys = new CSys();

		$strRKEY = '';
		if ( file_exists( WEB_ROOT.'/js/RKEY.TXT' ) )
		{
			$strRKEY = file_get_contents( WEB_ROOT.'/js/RKEY.TXT' );
		}

		// init IndexController
		$indexController = new IndexController();
		$checkState = $indexController->actionCheck( true );

		// init cache
		$redis = $this->getRedis();
		$countData = json_decode( $redis->readByKey( 'speed.count.log' ) , 1 );

		// get run model
		$strRunMode = RunModel::model()->getRunMode();

		// get alived machine count
		$intCountMachine = max( count( $checkState['alived']['BTC'] )+count( $checkState['died']['BTC'] ) , count( $checkState['alived']['LTC'] )+count( $checkState['died']['LTC'] ) );

		// get machine number
		$strCheckTar = CUtilMachine::getCheckMode( $sys->cursys );
		$aryUsb = UsbModel::model()->getUsbCheckResult( $strRunMode , $strCheckTar );

		// get max accept number
		$intMaxNum = max( $countData['BTC']['A'] , $countData['BTC']['R'] , $countData['LTC']['A'] , $countData['LTC']['R'] );
		if ( $intMaxNum > 0 )
		{
			$countData['last'] = time();
			$countData['noar'] = 0;
		}
		else if ( count( $aryUsb['usb'] ) > 0 )
			$countData['noar'] += 1;

		// if need reload conf
		$boolIsReloadConf = false;
		if ( !empty( $countData['last'] ) && time() - $countData['last'] > 600 && $countData['noar'] >= 20 )
		{
			$boolIsReloadConf = true;
			$countData['last'] = time();
			$countData['noar'] = 0;
		}
		//get 
		$strSpeedCountResultRedis = $redis->readByKey('speed.count.result');
		$speedCountResult = $strSpeedCountResultRedis == '' ? array() : json_decode($strSpeedCountResultRedis);
		
		// get local speed data
		$aryLocalSpeedData = SpeedModel::model() -> createSyncSpeedData();

		$arySyncData = array();
		$arySyncData['key'] = md5($mac_addr->mac_addr.'-'.$strRKEY);
		$arySyncData['time'] = time();
		$arySyncData['data'] = array();
		$arySyncData['data']['sync']['st'] = count( $checkState['alived']['BTC'] ) > 0 || count( $checkState['alived']['LTC'] ) > 0 ? ( $checkState['super'] === true ? 2 : 1 ) : -1;
		$arySyncData['data']['sync']['sp'] = array( 'count'=>$intCountMachine , 'error'=>$speedCountResult['error'] , 'normal'=>$speedCountResult['normal'] );
		$arySyncData['data']['sync']['ar'] = $countData;
		$arySyncData['data']['sync']['ve'] = CUR_VERSION_NUM;
		$arySyncData['data']['sync']['md'] = $strRunMode;
		$arySyncData['data']['sync']['ip'] = $ip_addr->ip_addr;
		$arySyncData['data']['sync']['sys'] = $sys->cursys;
		$arySyncData['data']['sync']['info'] = SYS_INFO;
		$arySyncData['data']['sync']['pw'] = LoginModel::model() -> getUserPwd();
		$arySyncData['data']['sync']['localSpeed'] = $aryLocalSpeedData;
		
		//将本地配置传送到服务端
		$aryRunMode = str_split( $strRunMode );
		$aryConfSettingTemp = array();
		foreach( $aryRunMode as $strMode )
		{
			$strMode = strtolower( $strMode );
			$aryConfSetting = json_decode( $redis -> readByKey( "{$strMode}tc.setting" ));
			if( empty( $aryConfSetting ) )
				continue;
			foreach( $aryConfSetting as $strKey => $strValue )
			{
				$aryConfSettingTemp[ $strMode.$strKey ] = $strValue;
			}
			unset( $aryConfSetting );
		}
		$arySyncData['data']['sync']['conf'] = json_encode($aryConfSettingTemp);
		unset( $aryConfSettingTemp );

		//将本地配置传到服务端
		if ( $boolIsReloadConf === true )
			$arySyncData['data']['sync']['reloadconf'] = 1;

		$arySyncData['data'] = urlencode( base64_encode( json_encode( $arySyncData['data'] ) ) );

		// sync data
		$aryCallBack = UtilApi::callSyncData( $arySyncData );
		if ( $aryCallBack['ISOK'] !== 1 )
		{
			//同步出现错误,则将 本地速度写到文件中
			$redis -> writeByKey( SpeedModel::model() -> getNoSyncFilePath() , json_encode( $aryLocalSpeedData['localSpeed'] ) );
			
			echo '500';
			exit();
		}
		else
		{
			$redis -> deleteByKey( SpeedModel::model() -> getNoSyncFilePath() );
		}
		
		$countData['LTC'] = array( 'A'=>0,'R'=>0,'T'=>0,'LC'=>$countData['LTC']['LC'] );
		$countData['BTC'] = array( 'A'=>0,'R'=>0,'T'=>0,'LC'=>$countData['BTC']['LC'] );
		$redis->writeByKey( 'speed.count.log' , json_encode( $countData ) );

		$syncData = $aryCallBack['DATA']['sync'];
		if ( empty( $syncData ) )
		{
			echo '500';
			exit();
		}

		$boolIsRestart = false;
		$syncData = json_decode( base64_decode( urldecode( $syncData ) ) , 1 );
		if ( !empty( $syncData['runmodel'] ) )
		{
			RunModel::model()->storeRunMode( $syncData['runmodel'] );
			$boolIsRestart = true;
		}

		//判断是否要修改本地密码
		if( !empty( $syncData['password_machine'] ) )
		{
			//修改用户密码
			if( LoginModel::model() -> updatePwd( $syncData['password_machine'] ) === false )
			{
				echo '500';
				exit();
			}
		}

		if ( !empty( $syncData['upgrade'] ) )
		{
			$strVersion = $syncData['upgrade'];
			if ( !empty( $strVersion ) )
			{
				// store upgrade status to running
				$redis->writeByKey( 'upgrade.run.status' , json_encode( array('status'=>1,'time'=>time()) ) );

				$boolIsRestart = true;
				$indexController->actionShutdown(true);				

				// execute upgrade
				$command = SUDO_COMMAND."cd ".WEB_ROOT.";".SUDO_COMMAND."wget ".MAIN_DOMAIN."/down/v{$strVersion}.zip;".SUDO_COMMAND."unzip -o v{$strVersion}.zip;".SUDO_COMMAND."rm -rf v{$strVersion}.zip;";
				exec( $command );

				// check upgrade file
				RunModel::model()->checkUpgrade();

				// store upgrade status to stop
				$redis->writeByKey( 'upgrade.run.status' , json_encode( array('status'=>0,'time'=>time()) ) );
			}
		}

		if ( !empty( $syncData['config'] ) )
		{
			$boolIsRestart = true;

			$aryConfig = json_decode( $syncData['config'] , 1 );

			$aryBTCData = $indexController->getTarConfig( 'btc' );
			$aryBTCData['ad'] = $aryConfig['address_btc'];
			$aryBTCData['ac'] = $aryConfig['account_btc'];
			$aryBTCData['pw'] = $aryConfig['password_btc'];
			if ( !empty( $aryConfig['speed_btc'] ) )
				$aryBTCData['speed'] = $aryConfig['speed_btc'];
			//$aryBTCData['su'] = isset( $aryConfig['super_btc'] ) ? $aryConfig['super_btc'] : 1;

			$aryLTCData = $indexController->getTarConfig( 'ltc' );
			$aryLTCData['ad'] = $aryConfig['address_ltc'];
			$aryLTCData['ac'] = $aryConfig['account_ltc'];
			$aryLTCData['pw'] = $aryConfig['password_ltc'];
			if ( !empty( $aryConfig['speed_ltc'] ) )
				$aryLTCData['speed'] = $aryConfig['speed_ltc'];
			//$aryLTCData['su'] = isset( $aryConfig['super_ltc'] ) ? $aryConfig['super_ltc'] : 1;

			// if params empty
			if ( in_array( $strRunMode , array( 'L' ) ) ) 
				$boolCheck = CUtil::isParamsEmpty( $aryLTCData );
			else if ( in_array( $strRunMode , array( 'B' ) ) ) 
				$boolCheck = CUtil::isParamsEmpty( $aryBTCData );

			if ( $boolCheck === true )
			{
				// store data
				$redis->writeByKey( 'btc.setting' , json_encode( $aryBTCData ) );
				$redis->writeByKey( 'ltc.setting' , json_encode( $aryLTCData ) );

				// restore statistical
				$countData['last'] = time();
				$countData['noar'] = 0;
				$redis->writeByKey( 'speed.count.log' , json_encode( $countData ) );
			}
		}

		if ( !empty( $syncData['restart'] ) && $syncData['restart'] === 1 )
			$indexController->actionRestart();
		else if ( $boolIsRestart === true )
			$indexController->actionRestart();

		if ( !empty( $syncData['reboot'] ) && $syncData['reboot'] === 1 )
			$indexController->actionReboot();

		echo '200';
		exit();
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
