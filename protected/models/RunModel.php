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
	public function getRunModel()
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
			
			return empty( $aryModel ) ? 'L' : $aryModel['model'];
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

		return $intCountFile > 0 ? 1 : 0;
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

// end class
}
