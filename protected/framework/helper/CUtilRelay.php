<?php
/**
 * 继电器控制类
 *
 * @author wengebin
 * @date 2014-07-16
 */
class CUtilRelay
{
	/**
	 * 获得继电器重启端口
	 *
	 * @return string
	 */
	public static function getRelayPort()
	{
		// 寻找继电器端口
		$command = "dmesg|awk -F ' ' '/pl2302.*(ttyUSB[0-9]+)/{split(\$NF,a,\"/\");print a[1]}'";
		@exec( $command , $output );

		// 返回找到的端口
		if ( !empty( $output ) )
			return end( $output );
		else
			return '';
	}

	/**
	 * 重新开关电源
	 *
	 * @param string $_strPort 继电器端口
	 * @param int $_intTime 继电器命令执行间隔，以微秒为单位
	 * @return void
	 */
	public static function restartPower( $_strPort = 'ttyATH0' , $_intTime = 1000000 )
	{
		@exec( SUDO_COMMAND."chmod 777 /dev/{$_strPort}" );
		@exec( SUDO_COMMAND."stty -F /dev/{$_strPort} raw speed 9600;".SUDO_COMMAND."echo \"O(00,05,0)E\" > /dev/{$_strPort} &" );
		usleep( $_intTime );
		@exec( SUDO_COMMAND."stty -F /dev/{$_strPort} raw speed 9600;".SUDO_COMMAND."echo \"O(00,05,1)E\" > /dev/{$_strPort} &" );
		usleep( $_intTime );
	}
	
//end class
}
