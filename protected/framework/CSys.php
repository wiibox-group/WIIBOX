<?php
/**
 * 获取系统信息
 */
class CSys
{
	// 系统标记
	public $cursys;
	
	/**
	 * init class
	 */
	function CSys()
	{
		// 系统分辨命令
		$strCommand = SUDO_COMMAND.'uname -a';

		// 匹配系统
		@exec( $strCommand , $output );
		if ( !empty( $output ) )
		{
			$strSystem = $output[0];

			// 是否为OpenWrt
			preg_match( '/.*OpenWrt.*mips.*/' , $strSystem , $match );
			if ( !empty( $match[0] ) )
			{
				$this->cursys = 'OPENWRT';
				return;
			}

			// 是否为 raspberry
			preg_match( '/.*wiibox.*armv6l.*/' , $strSystem , $match );
			if ( !empty( $match[0] ) )
			{
				$this->cursys = 'RASPBERRY';
				return;
			}
		}
	}

// end class
}
