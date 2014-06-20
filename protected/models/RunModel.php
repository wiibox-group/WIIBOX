<?php
/**
 * 运行模型
 * 
 * @author wengebin
 * @date 2014-01-16
 */
class RunModel extends CModel
{
	// redis object
	private $_redis;

	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 返回惟一实例
	 *
	 * @return Model
	 */
	public static function model( $className = __CLASS__ )
	{
		return parent::model( __CLASS__ );
	}

	/**
	 * get current model
	 */
	public function getRunMode()
	{
		if ( isset( $this->_model ) )
			return $this->_model;
		else
		{
			$redis = $this->getRedis();
			$modelVal = $redis->readByKey( 'run.model' );
			
			// get model object
			$aryModel = array();
			if ( !empty( $modelVal ) )
				$aryModel = json_decode( $modelVal , 1 );
			
			$strModel = empty( $aryModel ) ? 'L' : $aryModel['model'];
			return $strModel;
		}
	}

	/**
	 * store run model
	 */
	public function storeRunModel( $_strModel = 'L' )
	{
		$redis = $this->getRedis();
		$modelVal = $redis->readByKey( 'run.model' );

		// get model object
		$aryModel = array();
		if ( !empty( $modelVal ) )
			$aryModel = json_decode( $modelVal , 1 );

		$aryModel['model'] = $_strModel;
		$redis->writeByKey( 'run.model' , json_encode( $aryModel ) );

		return true;
	}

	/**
	 * Check upgrade
	 */
	public function checkUpgrade()
	{
		$strDir = WEB_ROOT.'/up';
		// 打开目录
		$dir = @opendir( $strDir );

		// 统计文件数
		$intCountFile = 0;

		// 遍历文件
		if ( !empty( $dir ) )
		{
			while ( ( $file  = readdir( $dir ) ) !== false )
			{
				// 获得子目录
				$sub_dir = $strDir.DIRECTORY_SEPARATOR.$file;
				if ( $file == '.' || $file == '..' )
					continue;
				else if ( is_dir( $sub_dir ) )
					$intCountFile += 1;
				else
				{
					$intCountFile += 1;
					exec( SUDO_COMMAND.'unzip -o '.$sub_dir.' -d /' );
					@unlink( $sub_dir );
					@unlink( '/'.$file );
				}
			}
		}

		// OPKG 升级包
		$strDir = WEB_ROOT.'/opkg';
		// 打开目录
		$dir = @opendir( $strDir );

		// 统计文件数
		$intOpkgCountFile = 0;

		// 遍历文件
		if ( !empty( $dir ) )
		{
			while ( ( $file  = readdir( $dir ) ) !== false )
			{
				// 获得子目录
				$sub_dir = $strDir.DIRECTORY_SEPARATOR.$file;
				if ( $file == '.' || $file == '..' )
					continue;
				else if ( is_dir( $sub_dir ) )
					$intOpkgCountFile += 1;
				else
				{
					$intOpkgCountFile += 1;
					exec( SUDO_COMMAND.'opkg install '.$sub_dir );
					@unlink( $sub_dir );
				}
			}
		}

		return $intCountFile > 0 || $intOpkgCountFile > 0 ? 1 : 0;
	}

	/**
	 * get all usb cache
	 */
	public function getAllUsbCache()
	{
		$redis = $this->getRedis();
		$usbVal = $redis->readByKey( 'usb.all' );
		
		return empty( $usbVal ) ? array() : json_decode( $usbVal , 1 );
	}

	/**
	 * store all usb cache
	 */
	public function storeAllUsbCache( $_aryUsb = array() )
	{
		$redis = $this->getRedis();
		return $redis->writeByKey( 'usb.all' , json_encode( $_aryUsb ) );
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
	
	/**
	* 获取控制器Key
	*
	* @author zhangyi
	* @date 2014-06-13
	*
	*/
	public function getKeys()
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
		return $strRKEY;

	}

// end class
}
