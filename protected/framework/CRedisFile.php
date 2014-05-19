<?php
/**
 * Redis 存储类
 *
 * @author wengebin
 * @package framework
 * @date 2014-01-05
 */
class CRedisFile extends CApplicationComponents 
{
	private $_store_url = "/tmp/";
	/**
	 * Redis key 前缀
	 * @var string
	 */
	private $_keyPrefix = "file.string.";

	/**
	 * Redis key 后缀
	 * @var string
	 */
	private $_keySuffix = "";

	/**
	 * Redis 连接
	 * @var CRedis
	 */
	private static $_connection;

	/**
	 * Redis 值生存时间
	 * @var int
	 */
	private $_timeout = 0;
	
	/**
	 * Redis 存储是否需要设置域
	 * 默认 1 - 自动设置前缀， 0 - 无需前缀
	 *
	 * @var int
	 */
	private $_needDistrict = 1;

	/**
	 * 初始化
	 */
	public function init( $_prefix = '' , $_suffix = '' )
	{
		$this->_store_url = WEB_ROOT."/cache/";
		parent::init();
		// 设置前缀
		$this->setPrefix( $_prefix );
		// 设置后缀
		$this->setSuffix( $_suffix );
	}

	public function readFile( $_strFileName = '' )
	{
		if ( empty( $_strFileName ) )
			 throw new CModelException(CUtil::i18n('exception,exec_file_nameNotNull'));

		if ( file_exists( $this->_store_url.$_strFileName ) )
			return file_get_contents( $this->_store_url.$_strFileName );
		else
			return '';
	}

	public function getFile( $_strFileName = '' )
	{
		if ( empty( $_strFileName ) )
			 throw new CModelException( CUtil::i18n('exception,exec_file_nameNotNull') );

		$file = fopen( $this->_store_url.$_strFileName , 'w' );
		return $file;
	}

	public function writeFile( $_fileTar = null , $_strVal = '' )
	{
		if ( empty( $_fileTar ) )
			throw new CModelException( CUtil::i18n('exception,exec_file_banWrite') );

		return fwrite( $_fileTar , $_strVal );
	}

	public function closeFile( $_fileTar = null )
	{
		if ( empty( $_fileTar ) )
			throw new CModelException( CUtil::i18n('exception,exec_file_banClose') );

		fclose( $_fileTar );
	}

	public function deleteFile( $_strFileName = '' )
	{
		if ( empty( $_strFileName ) )
			 throw new CModelException( CUtil::i18n('exception,exec_file_nameNotNull') );

		if ( file_exists( $this->_store_url.$_strFileName ) )
			return unlink( $this->_store_url.$_strFileName );
		else
			return false;
	}

	/**
	 * 根据 key 读取一个 value
	 *
	 * @param string $_id	指定的Redis key
	 * @return bool
	 */
	public function readByKey( $_key = '' )
	{
		return $this->readFile( $this->calculateKey( $_key ) );
	}

	/**
	 * 根据 key 存储一个给定的 value
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_value	给定的岁应 key 的 value 值
	 * @return bool
	 */
	public function writeByKey( $_key = '' , $_value = '' )
	{
		$key = $this->calculateKey( $_key );

		$file = $this->getFile( $key );
		$writeResult = $this->writeFile( $file , $_value );
		$this->closeFile( $file );

		return $writeResult > 0 ? true : false;
	}

	/**
	 * 根据 key 删除
	 *
	 * @param string $_key	给定的需要删除的 redis key
	 * @return bool
	 */
	public function deleteByKey( $_key = '' )
	{
		return $this->deleteFile($this->calculateKey( $_key ));
	}

	/**
	 * 根据匹配字符串获得对应的 key 集合
	 *
	 * @param string $_matchStr 匹配的字符串
	 * @return string
	 */
	public function getKeys( $_matchStr = '*' )
	{
		return array();
	}

	/**
	 * 手动设置 Redis 连接
	 *
	 * @param Redis $_connection	新连接
	 */
	public function setConnection( $_connection )
	{
		return true;
	}

	/**
	 * 根据给定的 key 计算出新 key
	 *
	 * @param string $_key	给定的 Redis key，通过与指定字符串合并计算得到一个新 key
	 * @return string
	 */
	protected function calculateKey( $_key = '' )
	{
		return ( $this->_needDistrict === 1 ? REDIS_DISTRICT_NAME.'.' : '' ).$this->_keyPrefix.$_key.$this->_keySuffix.'.d';
	}

	/**
	 * 为 key 设置过期时间
	 *
	 * @param string $_key KEY
	 * @return bool
	 */
	public function setTimeoutByKey( $_key = '' )
	{
		return true;
	}

	/**
	 * 设置过期时间
	 *
	 * @param int $_timeout	过期时长
	 * @return bool
	 */
	public function setTimeout( $_timeout = 0 )
	{
		return true;
	}

	/**
	 * 获得过期时间
	 *
	 * @return int
	 */
	public function getTimeout()
	{
		return $this->_timeout;
	}

	/**
	 * 设置 key 前缀
	 *
	 * @param string $_strPrefix key的前缀字符串
	 * @return bool
	 */
	public function setPrefix( $_strPrefix = '' )
	{
		$this->_keyPrefix = $_strPrefix;
		return true;
	}

	/**
	 * 设置 key 后缀
	 *
	 * @param string $_strSuffix key的后缀字符串
	 * @return bool
	 */
	public function setSuffix( $_strSuffix = '' )
	{
		$this->_keySuffix = $_strSuffix;
		return true;
	}

	/**
	 * 设置是否需要域前缀
	 *
	 * @param int $_intDistrict key的后缀字符串
	 * @return bool
	 */
	public function setDistrict( $_intDistrict = '' )
	{
		$this->_needDistrict = $_intDistrict;
		return true;
	}

	/**
	 * 保存数据
	 *
	 * @return bool
	 */
	public function saveData( $_intDistrict = '' )
	{
		return true;
	}

//end class
}
