<?php
/**
 * 控制器特有通用操作
 */
class CUtilRestart
{
	/**
	 * 重启A2
	 */
	public static function restartByA2( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_a2 -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --A1Pll1 {$intRunSpeed} --A1Pll2 {$intRunSpeed} --A1Pll3 {$intRunSpeed} --A1Pll4 {$intRunSpeed} --A1Pll5 {$intRunSpeed} --A1Pll6 {$intRunSpeed} --diff 16 --api-listen --api-network --cs 8 --stmcu 0 --hwreset --no-submit-stale --lowmem --api-allow W:127.0.0.1 >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启A1
	 */
	public static function restartByA1( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_a1 --no-submit-stale --hotplug=0 --cs=8 --hwreset --stmcu=1 --diff=8 --A1Pll={$intRunSpeed} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --api-listen --api-allow W:127.0.0.1 --real-quiet >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启烤猫
	 */
	public static function restartByFc( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		if ( !empty( $_aryConfig['usb'] ) )
		{
			$strUsbParam = '';
			foreach ( $_aryConfig['usb'] as $usb )
				$strUsbParam .= ' -S'.$usb;
		}

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_fc {$strUsbParam} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --hashratio-freq={$intRunSpeed} --api-listen --api-allow W:127.0.0.1 --real-quiet >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启小强矿机
	 */
	public static function restartByXq( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_xq -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --api-listen --api-allow W:127.0.0.1 >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启Avalon 3
	 */
	public static function restartByAvalon( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		if ( !empty( $_aryConfig['usb'] ) )
		{
			$strUsbParam = '';
			foreach ( $_aryConfig['usb'] as $usb )
				$strUsbParam .= ' -S '.$usb;
		}

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_av {$strUsbParam} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --avalon2-freq={$intRunSpeed} --avalon2-fan=40 --avalon2-voltage=7000 --api-listen --api-allow W:127.0.0.1 >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启小二黑
	 */
	public static function restartByGS40Chips( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_ltc --gridseed-options=baud=115200,freq={$intRunSpeed},modules=1,chips=40,usefifo=0 --hotplug=0 -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --api-listen --api-allow W:127.0.0.1 >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启大红袍
	 */
	public static function restartByDIF128Chips( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/minerd_red --dif={$_strUsb} -G {$_strUsb} --freq={$intRunSpeed} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启宙斯
	 */
	public static function restartByZs( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;

		if ( !empty( $_aryConfig['usb'] ) )
		{
			$strUsbParam = '';
			foreach ( $_aryConfig['usb'] as $usb )
				$strUsbParam .= ' -S '.$usb;
		}

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_zs {$strUsbParam} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --fixdiff 1024 --chips 36 --clock 330 --api-listen --api-allow W:127.0.0.1 >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启菊花单挖
	 */
	public static function restartByGS5Chips( $_aryConfig = array() , $_strUsb )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/minerd --dif={$_strUsb} -G {$_strUsb} --freq={$intRunSpeed} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

// end class
}
