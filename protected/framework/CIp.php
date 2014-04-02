<?php
/**
 * 获取本地 IP 地址
 */
class CIp
{
	// 返回 IP 地址
	public $return_array = array();
	public $ip_addr;
	
	/**
	 * init class
	 */
	function CIp($os_type)
	{
		switch ( strtolower($os_type) )
		{
			case "linux":
				$this->forLinux();
				break;
			case "solaris":
				break;
			case "unix":
				break;
			case "aix":
				break;
			default:
				$this->forWindows();
				break;
		}

		$temp_array = array();
		foreach ( $this->return_array as $value )
		{
			if ( preg_match_all( "/192\.168\.\d{1,3}\.\d{1,3}/i", $value, $temp_array ) )
			{
				$tmpIp = $temp_array[0];
				if ( is_array( $tmpIp ) ) $tmpIp = array_shift( $tmpIp );
				$this->ip_addr = $tmpIp;
				break;
			}
		}

		unset($temp_array);
		return $this->ip_addr;
	}

	/**
	 * Get ip address for windows
	 */
	function forWindows()
	{
		@exec("ipconfig /all", $this->return_array);
		if ( $this->return_array )
			return $this->return_array;
		else
		{
			$ipconfig = $_SERVER["WINDIR"]."/system32/ipconfig.exe";
			if ( is_file($ipconfig) )
				@exec($ipconfig." /all", $this->return_array);
			else
				@exec($_SERVER["WINDIR"]."/system/ipconfig.exe /all", $this->return_array);
			
			return $this->return_array;
		}
	}

	/**
	 * Get ip address for linux
	 */
	function forLinux()
	{
		@exec("ifconfig -a", $this->return_array);
		return $this->return_array;
	}

// end class
}
