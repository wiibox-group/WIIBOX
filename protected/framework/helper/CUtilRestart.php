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
	 * 重启JIE A1
	 */
	public static function restartByJieA1( $_aryConfig = array() )
	{
		if ( empty( $_aryConfig ) )
			return false;
	
		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_jie --no-submit-stale --hotplug=0 --cs=8 --hwreset --stmcu=1 --diff=8 --A1Pll1 {$intRunSpeed} --A1Pll2 {$intRunSpeed} --A1Pll3 {$intRunSpeed} --A1Pll4 {$intRunSpeed} --A1Pll5 {$intRunSpeed} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --api-listen --api-allow W:127.0.0.1 --real-quiet >/dev/null 2>&1 &";
	
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

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_xq -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --api-listen --api-allow W:127.0.0.1 --icarus-options=115200:1:1 --rmu-auto {$intRunSpeed} --rmu-fan 0 >/dev/null 2>&1 &";

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
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_av {$strUsbParam} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --avalon2-freq={$intRunSpeed} --avalon2-fan=90 --avalon2-voltage=7000 --api-listen --api-allow W:127.0.0.1 >/dev/null 2>&1 &";

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
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_ltc --gridseed-options=baud=115200,freq={$intRunSpeed},modules=1,chips=40,usefifo=0 --hotplug=0 -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --api-listen --api-allow W:127.0.0.1 --text-only >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启大红袍
	 */
	public static function restartByDIF128Chips( $_aryConfig = array() , $_strUsb = '' )
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
	public static function restartByGS5Chips( $_aryConfig = array() , $_strUsb = '' )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$intRunSpeed = intval( $_aryConfig['speed'] );
		$command = SUDO_COMMAND.WEB_ROOT."/soft/minerd --dif={$_strUsb} -G {$_strUsb} --freq={$intRunSpeed} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启LK 33M
	 */
	public static function restartByLK33M( $_aryConfig = array() )
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
		$command = SUDO_COMMAND.WEB_ROOT."/soft/cgminer_lk33 {$strUsbParam} -o {$_aryConfig['ad']} -u {$_aryConfig['ac']} -p {$_aryConfig['pw']} --fixdiff 1024 --chips 36 --clock {$intRunSpeed} --api-listen --api-allow W:127.0.0.1 --quiet >/dev/null 2>&1 &";

		@exec( $command );
		return true;
	}

	/**
	 * 重启SF 3301
	 */
	public static function restartBySf3301( $_aryConfig = array() , $_aryUsb = '' , $_strType = '' )
	{
		if ( empty( $_aryConfig ) )
			return false;

		$intRunSpeed = intval( $_aryConfig['speed'] );
		if ( $_strType == 'LTC' )
		{
			// SF100 3 board or 5 board
			if ( count( $_aryConfig['ac'] ) === 3 || count( $_aryConfig['ac'] ) === 5 )
			{
				foreach ( $_aryUsb as $index=>$usb )
				{
					$usb = ($index+1).":".$usb;
					$acc = $_aryConfig['ac'][$index];
					$tmpStrLogName = substr( $usb , strpos($usb,'ttyUSB') );
					$command = SUDO_COMMAND.WEB_ROOT."/soft/minerd_sf -o {$_aryConfig['ad']} -u {$acc} -p x -f {$intRunSpeed} -B -d {$usb} >/www/logs/l-{$tmpStrLogName}.log 2>&1 &";
					@exec( $command );
				}
			}
			else
			{
				foreach ( $_aryUsb as $index=>&$usb )
					$usb = ($index+1).":".$usb;

				$strUsb = implode( ',' , $_aryUsb );
				$command = SUDO_COMMAND.WEB_ROOT."/soft/minerd_sf -o {$_aryConfig['ad']} -u {$_aryConfig['ac'][0]} -p x -f {$intRunSpeed} -B -d {$strUsb} >/www/logs/l-all.log 2>&1 &";
				@exec( $command );
			}
		}
		else if ( $_strType == 'BTC' )
		{
			// SF100 3 board or 5 board
			if ( count( $_aryConfig['ac'] ) === 3 || count( $_aryConfig['ac'] ) === 5 )
			{
				foreach ( $_aryUsb as $index=>$usb )
				{
					$usb = ($index+1).":".$usb;
					$acc = $_aryConfig['ac'][$index];
					$tmpStrLogName = substr( $usb , strpos($usb,'ttyUSB') );
					$command = SUDO_COMMAND.WEB_ROOT."/soft/minerd_sf -o {$_aryConfig['ad']} -u {$acc} -p x -a sha256d -f {$intRunSpeed} -B -d {$usb} >/www/logs/b-{$tmpStrLogName}.log 2>&1 &";
					@exec( $command );
				}
			}
			else
			{
				foreach ( $_aryUsb as $index=>&$usb )
					$usb = ($index+1).":".$usb;

				$strUsb = implode( ',' , $_aryUsb );
				$command = SUDO_COMMAND.WEB_ROOT."/soft/minerd_sf -o {$_aryConfig['ad']} -u {$_aryConfig['ac'][0]} -p x -a sha256d -f {$intRunSpeed} -B -d {$strUsb} >/www/logs/b-all.log 2>&1 &";
				@exec( $command );
			}
		}

		return true;
	}

// end class
}
