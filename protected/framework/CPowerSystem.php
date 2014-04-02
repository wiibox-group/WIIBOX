<?php
/**
 * 系统操作类
 *
 * @author wengebin
 * @package framework
 * @date 2014-01-17
 */
class CPowerSystem extends CApplicationComponents 
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 *a重新开关电源
	 */
	public static function restartPower( $_intTime = 1000000 )
	{
		@exec( SUDO_COMMAND.'stty -F /dev/ttyATH0 raw speed 9600;'.SUDO_COMMAND.'echo "O(00,05,0)E" > /dev/ttyATH0 &' );
		usleep( $_intTime );
		@exec( SUDO_COMMAND.'stty -F /dev/ttyATH0 raw speed 9600;'.SUDO_COMMAND.'echo "O(00,05,1)E" > /dev/ttyATH0 &' );
	}

//end class
}
