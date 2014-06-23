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

		// get max accept number
		$intMaxNum = max( $countData['BTC']['A'] , $countData['BTC']['R'] , $countData['LTC']['A'] , $countData['LTC']['R'] );
		if ( $intMaxNum > 0 )
		{
			$countData['last'] = time();
			$countData['noar'] = 0;
		}
		else
			$countData['noar'] += 1;

		// if need reload conf
		$boolIsReloadConf = false;
		if ( !empty( $countData['last'] ) && time() - $countData['last'] > 600 && $countData['noar'] >= 20 )
		{
			$boolIsReloadConf = true;
			$countData['last'] = time();
			$countData['noar'] = 0;
		}

		$arySyncData = array();
		$arySyncData['key'] = md5($mac_addr->mac_addr.'-'.$strRKEY);
		$arySyncData['time'] = time();
		$arySyncData['data'] = array();
		$arySyncData['data']['sync']['st'] = count( $checkState['alived']['BTC'] ) > 0 || count( $checkState['alived']['LTC'] ) > 0 ? ( $checkState['super'] === true ? 2 : 1 ) : -1;
		$arySyncData['data']['sync']['sp'] = array( 'count'=>$intCountMachine , 'btc'=>0 , 'ltc'=>0 );
		$arySyncData['data']['sync']['ar'] = $countData;
		$arySyncData['data']['sync']['ve'] = CUR_VERSION;
		$arySyncData['data']['sync']['md'] = $strRunMode;
		$arySyncData['data']['sync']['ip'] = $ip_addr->ip_addr;
		$arySyncData['data']['sync']['sys'] = $sys->cursys;
		$arySyncData['data']['sync']['info'] = SYS_INFO;
		$arySyncData['data']['sync']['password_machine'] = LoginModel::model() -> getUserPwd();
		
		if ( $boolIsReloadConf === true )
			$arySyncData['data']['sync']['reloadconf'] = 1;
		$arySyncData['data'] = urlencode( base64_encode( json_encode( $arySyncData['data'] ) ) );

		// sync data
		$aryCallBack = UtilApi::callSyncData( $arySyncData );
		if ( $aryCallBack['ISOK'] !== 1 )
		{
			echo '500';
			exit();
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
		if( $syncData['needChangePassword'] === 1 )
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
	
	/*
	* 进行数据同步
	* 
	* @author zhangyi
	* @date 2014-06-14
	*
	*/
	public function actionSpeed()
	{
		//获取基本数据
		$objSpeedController = new SpeedController();
		$waitTime = $objSpeedController -> _waitTime;
		$maxPoint = $objSpeedController -> _maxPoint;
		$nowTime = $objSpeedController -> _nowTime;
		$pointTime = $objSpeedController -> _pointTime;
		
		//首先检查是否存在存在同步错误的数据
		$redis = $this -> getRedis();
		//获取最后一次同步数据
		$lastSyncTimeKey = 'local.speed.last.time';
		$haveWrongDataKey = 'local.speed.wrong.data';
		$lastSyncTime = $redis -> readByKey( $lastSyncTimeKey );
		if( $lastSyncTime == '' )
			$lastSyncTime = 0;
		else
			$lastSyncTime = strtoTime( $lastSyncTime ) * 1000;
		
		$syncWrongData = $redis -> readByKey( $havaWrongDataKey );

		$arySyncData = array();
		$strRKEY = RunModel::model() -> getKeys();
		$os = DIRECTORY_SEPARATOR=='\\' ? "windows" : "linux";
		$mac_addr = new CMac( $os );
		$arySyncData['key'] = md5($mac_addr->mac_addr.'-'.$strRKEY);
		$arySyncData['time'] = time();
		$arySyncData['data']['sync']['maxPoint'] = $maxPoint;
		$arySyncData['data']['sync']['nowTime'] = $nowTime;
		$arySyncData['data']['sync']['pointTime'] = $pointTime;		
		$arySyncData['data']['sync']['waitTime'] = $waitTime;
		
		//获取当前算力速度以及运行模式数据
		$objSpeedModel = SpeedModel::model();
		$intSpeedSum = $objSpeedModel -> getSpeedSum();
		$strRunModel = RunModel::model() -> getRunModel();
		$intSpeedL = $strRunModel === 'L' ? $intSpeedSum : 0;
		$intSpeedB = $strRunModel === 'B' ? $intSpeedSum : 0;

		if(  $syncWrongData != ''  )
		{
			$arySyncWrongData = json_decode( $arySyncWrongData , 1);
			if( $nowTime === $pointTime )
			{
				$arySyncWrongData['L'][''.$pointTime] = array( $pointTime , $intSpeedL );
				$arySyncWrongData['B'][''.$pointTime] = array( $pointTime , $intSpeedB );
			}
			$arySyncData['data']['sync']['data'] = $arySyncWrongData;
		}
		else
		{
			if( $nowTime == $pointTime && ($nowTime - $lastSyncTime) >= $waitTime )
			{
				$aryData = array(
							'L' => array( ''.$pointTime => array( $pointTime , $intSpeedL ) ),
							'B' => array( ''.$pointTime => array( $pointTime , $intSpeedB ) )
						);
				$arySyncData['data']['sync']['data'] = $aryData;
			}else
			{
				return true;
			}
		}
		$arySyncData['data'] = urlencode( base64_encode(json_encode( $arySyncData['data'] )));
		$redis -> writeByKey( $lastSyncTimeKey , date( 'Y-m-d H:i' , $pointTime/1000 ) );
		// sync data
		$aryCallBack = UtilApi::callSyncSpeedData( $arySyncData );
		if( $aryCallBack['ISOK'] === 1 )
		{
			$redis -> writeByKey( $haveWrongDataKey , '' );

			echo '200';
		}
		else
		{
			//判断是否存在错误数据
			if( $syncWrongData != '' )
			{
				$redis -> writeByKey( $haveWrongDataKey , json_encode( $syncWrongData ) );
			}
			else
			{
				$redis -> writeByKey( $haveWrongDataKey , json_encode( $aryData ) );

			}
			echo '500';
		}
		exit();	
	}
//end class
}
