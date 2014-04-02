<?php
class UtilUrl
{
	// 配置
	private static $_aryConfigRouteReWrite = array(
						''=>'index/index',
						// s=1|0
						'supermode'=>'index/mode',
						'restart'=>'index/restart',
						'shutdown'=>'index/shutdown',
						'check'=>'index/check',
						'checkrun'=>'index/checkrun',
						'usbstate'=>'index/usbstate',
						// usb=/dev/ttyUSB0 | to=btc|ltc|0
						'usbset'=>'index/usbset',
						// usb=/dev/ttyUSB0
						'restartTar'=>'index/restartTarget',
						'monitor'=>'monitor/index',
						// upgrade
						'upgrade'=>'upgrade/index',
						'checkversion'=>'upgrade/checkversion',
						'hasnew'=>'upgrade/hasnew',
						'upgradeversion'=>'upgrade/upgradeversion',
						// port check
						'portcheck'=>'port/check',
						'printsome'=>'port/print',
						'generatekey'=>'port/generatekey',
						'cancelbind'=>'port/cancelbind'
					);

	/**
	 * 获得配置
	 */
	public static function getConfig()
	{
		return self::$_aryConfigRouteReWrite;
	}

//end class
}

