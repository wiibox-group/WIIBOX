<?php
/**
 * 控制器特有通用操作
 */
class CUtilMachine
{
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
				// Fried Cat
				'FC_S_V1'		=> 300,
				// Rock Box for xiaoqiang
				'XQ_S_V1'		=> 300,
				// Diginforce
				'DIF_S_V1'		=> 850,
				// Avalon 3
				'AV_S_V1'		=> 500,
				// Zeus 33M Miner, default 950M
				'ZS_S_V1'		=> 0,
			);

	/** default check mode for single mode **/
	private static $_checkMode_S = array(
				'OPENWRT_GS_D_V2'		=> 'tty',
				'RASPBERRY_GS_S_V2'		=> 'lsusb-api',
				'OPENWRT_GS_S_V3'		=> 'lsusb-api',
				'RASPBERRY_A2_S_V1'		=> 'spi-ltc',
				'RASPBERRY_GS_A1_S_V1'	=> 'spi-btc',
				'RASPBERRY_FC_S_V1'		=> 'tty-btc',
				'RASPBERRY_XQ_S_V1'		=> 'tty-btc',
				'OPENWRT_DIF_S_V1'		=> 'lsusb-api',
				'RASPBERRY_DIF_S_V1'	=> 'lsusb-api',
				'OPENWRT_AV_S_V1'		=> 'tty-btc',
				'RASPBERRY_ZS_S_V1'		=> 'tty-ltc',
			);

	/** default check mode for dule mode **/
	private static $_checkMode_D = array(
				'OPENWRT_GS_D_V2'		=> 'lsusb',
			);

	/** default work time interval **/
	private static $_defaultInterval = array(
				'GS_D_V2'		=> 120,
				'GS_S_V2'		=> 120,
				'GS_S_V3'		=> 120,
				'A2_S_V1'		=> 300,
				'GS_A1_S_V1'	=> 300,
				'FC_S_V1'		=> 300,
				'XQ_S_V1'		=> 300,
				'DIF_S_V1'		=> 300,
				'ZS_S_V1'		=> 300,
			);
	
	/**
	 * 获得默认运行频率
	 *
	 * @param string $_strType 设备类型
	 * @return int
	 */
	public static function getDefaultSpeed( $_strType = '' )
	{
		if ( empty( $_strType ) )
			return 0;

		return empty( self::$_defaultSpeed[$_strType] ) ? 850 : self::$_defaultSpeed[$_strType];
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
	 * @return int
	 */
	public static function getSpeedList( $_strType = '' )
	{
		if ( empty( $_strType ) )
			return array();

		// 运行速度
		$intSpeed = self::$_defaultSpeed[ $_strType ];
		if ( empty( $intSpeed ) )
			return array();

		// 最小速度，前后延伸4个速度梯度
		$intMin = $intSpeed - 4 * 25;
		$intMin = $intMin > 0 ? $intMin : 0;

		// 可用速度集合
		$arySpeed = array();
		for ( $i = 0; $i < 10; $i++ )
			$arySpeed[] = $intMin + $i * 25;

		return $arySpeed;
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
			return empty( self::$_checkMode_S[$strSysInfo] ) ? 'lsusb' : self::$_checkMode_S[$strSysInfo];
		else
			return empty( self::$_checkMode_D[$strSysInfo] ) ? 'lsusb' : self::$_checkMode_D[$strSysInfo];
	}
	
//end class
}
