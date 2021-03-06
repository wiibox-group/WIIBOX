<?php
/**
 * 控制器特有通用操作
 */
class CUtilMachine
{
	/** Speed list step */
	const SPEED_STEP = 25;

	/** Machine default run speed **/
	private static $_defaultSpeed = array(
				// GS 5chips
				'GS_D_V2'		=> 850,
				// GS 40chips
				'GS_S_V2'		=> 825,
				// GS 40chips for 9331
				'GS_S_V3'		=> 850,
				// A2
				'A2_S_V1'		=> 1280,
				// GS A1
				'GS_A1_S_V1'	=> 850,
				// JIE A1
				'JIE_A1_S_V1'	=> 1000,
				// JIE A1
				'JIE_A1_S_V2'	=> 800,
				// Fried Cat
				'FC_S_V1'		=> 300,
				// Rock Box for xiaoqiang
				'XQ_S_V1'		=> 320,
				// Diginforce
				'DIF_S_V1'		=> 850,
				// Avalon 3
				'AV_S_V1'		=> 500,
				// Zeus 33M Miner, default 950M
				'ZS_S_V1'		=> 0,
				// LK 33M
				'LK_S_V1'		=> 325,
				// SF 3301
				'SF3301_D_V1'	=> array(450,850),
			);

	/** default check mode for single mode **/
	private static $_checkMode_S = array(
				'OPENWRT_GS_D_V2'		=> 'tty',
				'RASPBERRY_GS_S_V2'		=> 'lsusb-api',
				'OPENWRT_GS_S_V3'		=> 'tty',
				'RASPBERRY_A2_S_V1'		=> 'spi-ltc',
				'RASPBERRY_GS_A1_S_V1'	=> 'spi-btc',
				'RASPBERRY_JIE_A1_S_V1'	=> 'spi-btc',
				'RASPBERRY_JIE_A1_S_V2'	=> 'spi-btc',
				'RASPBERRY_FC_S_V1'		=> 'tty-btc',
				'RASPBERRY_XQ_S_V1'		=> 'lsusb-btc',
				'OPENWRT_XQ_S_V1'		=> 'lsusb-btc',
				'RASPBERRY_DIF_S_V1'	=> 'lsusb-api',
				'OPENWRT_DIF_S_V1'		=> 'tty',
				'OPENWRT_AV_S_V1'		=> 'tty-btc',
				'RASPBERRY_ZS_S_V1'		=> 'tty-ltc',
				'RASPBERRY_LK_S_V1'		=> 'tty-ltc',
			);

	/** default check mode for dule mode **/
	private static $_checkMode_D = array(
				'OPENWRT_GS_D_V2'		=> 'lsusb',
				'RASPBERRY_SF3301_D_V1'	=> 'sf-ltc',
			);

	/** default work time interval **/
	private static $_defaultInterval = array(
				'GS_D_V2'		=> 300,
				'GS_S_V2'		=> 300,
				'GS_S_V3'		=> 300,
				'A2_S_V1'		=> 300,
				'GS_A1_S_V1'	=> 300,
				'JIE_A1_S_V1'	=> 300,
				'JIE_A1_S_V2'	=> 300,
				'FC_S_V1'		=> 300,
				'XQ_S_V1'		=> 300,
				'DIF_S_V1'		=> 300,
				'ZS_S_V1'		=> 300,
				'LK_S_V1'		=> 300,
				'LK_S_V1'		=> 300,
				'SF3301_D_V1'	=> 600,
			);
	
	/**
	 * 获得默认运行频率
	 *
	 * @param string $_strType 设备类型
	 * @param int $_intAlgo 指定算法
	 * @return int
	 */
	public static function getDefaultSpeed( $_strType = '' , $_intAlgo = null )
	{
		if ( empty( $_strType ) )
			return 0;

		// 频率
		$intFreq = !is_null( $_intAlgo ) ? self::$_defaultSpeed[$_strType][$_intAlgo] : self::$_defaultSpeed[$_strType];

		return empty( $intFreq ) ? 850 : $intFreq;
	}

	/**
	 * 获得算力板死亡间隔
	 *
	 * @param string $_strType 设备类型
	 * @return int
	 */
	public static function getDefaultInterval( $_strType = '' )
	{
		if ( empty( $_strType ) )
			return 120;

		return empty( self::$_defaultInterval[$_strType] ) ? 300 : self::$_defaultInterval[$_strType];
	}

	/**
	 * 获得当前可用运行频率
	 *
	 * @param string $_strType 设备类型
	 * @param int $_intAlgo 指定算法
	 * @return int
	 */
	public static function getSpeedList( $_strType = '' , $_intAlgo = null )
	{
		if ( empty( $_strType ) )
			return array();

		// 运行速度
		$intFreq = !is_null( $_intAlgo ) ? self::$_defaultSpeed[$_strType][$_intAlgo] : self::$_defaultSpeed[$_strType];
		if ( empty( $intFreq ) )
			return array();

		// 最小速度，前后延伸4个速度梯度
		$intMin = floor( $intFreq / self::SPEED_STEP ) * self::SPEED_STEP - 4 * self::SPEED_STEP;
		$intMin = $intMin > 0 ? $intMin : 0;

		// 可用速度集合
		$arySpeed = array();
		for ( $i = 0; $i < 10; $i++ )
		{
			$intGetSpeed = $intMin + $i * self::SPEED_STEP;
			$arySpeed[$intGetSpeed] = $intGetSpeed;
		}

		$arySpeed[$intFreq] = $intFreq;
		ksort( $arySpeed );

		return array_values( $arySpeed );
	}

	/**
	 * 获得设备检查模式
	 *
	 * @param string $_strSystem 系统类型
	 * @return string
	 */
	public static function getCheckMode( $_strSystem = '' )
	{
		// system info head
		$strSysInfo = $_strSystem.'_'.SYS_INFO;

		$strRunMode = RunModel::model()->getRunMode();
		if ( $strRunMode == 'L' || $strRunMode == 'B' )
			return empty( self::$_checkMode_S[$strSysInfo] ) ? 'tty' : self::$_checkMode_S[$strSysInfo];
		else
			return empty( self::$_checkMode_D[$strSysInfo] ) ? 'tty' : self::$_checkMode_D[$strSysInfo];
	}
	
//end class
}
