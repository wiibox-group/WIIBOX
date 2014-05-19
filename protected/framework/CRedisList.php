<?php
/**
 * Redis list 存储类
 *
 * @author wengebin
 * @package framework
 * @date 2013-09-28
 *
 * List 为有序队列，默认 push 将数据放置到链表尾端，pop 会取出链表头第一行数据
 * 如果需要强制将数据放入链表头优先处理，这样调用：
 *	$redis->push( 'list1' , 'val1' , CRedisList::REDIS_LIST_FRONT );
 * 如果需要强制获得链表尾端数据，这样调用：
 *	$redis->pop( 'list1' , CRedisList::REDIS_LIST_END );
 * 如果需要强制获得表头数据，这样调用：
 *	$redis->get( 'list1' , CRedisList::REDIS_LIST_FRONT );
 * 如果需要强制替换表头数据，这样调用：
 *	$redis->set( 'list1' , 'val1' , CRedisList::REDIS_LIST_FRONT );
 *
 * 使用：
 * $redis = new CRedisList();
 * $redis->push( 'list1' , 'val1' );
 * echo $redis->pop( 'list1' );
 */
class CRedisList extends CRedis 
{
	/**
	 * Redis List中数据的位置，链表头或链表尾
	 */
	const REDIS_LIST_FRONT = 'front';
	const REDIS_LIST_END = 'end';

	/**
	 * 初始化
	 */
	public function init( $_prefix = 'redis.list.' , $_suffix = '' )
	{
		parent::init( $_prefix , $_suffix );
	}

	/**
	 * 根据 key 读取一个 value
	 * List 中没有根据 key 获得值的方法
	 *
	 * @param string $_key	给定的 redis key
	 * @return bool
	 */
	public function readByKey( $_key = '' )
	{
		return false;
	}

	/**
	 * 根据 key 存储一个给定的 value
	 * List 中没有根据 key 获得值的方法
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_value	给定的岁应 key 的 value 值
	 * @return bool
	 */
	public function writeByKey( $_key = '' , $_value = '' )
	{
		return false;
	}

	/**
	 * 插入一条数据
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_value	给定的岁应 key 的 value 值
	 * @param string $_where	数据插入的位置，默认为链表尾端
	 * @return bool
	 */
	public function push( $_key = '' , $_value = '' , $_where = self::REDIS_LIST_END )
	{
		$key = $this->calculateKey( $_key );

		$pushResult = 0;
		// 根据位置存储数据
		if ( $_where == self::REDIS_LIST_END && self::getConnection() )
			$pushResult = self::getConnection()->rPush( $key , $_value );
		else if ( $_where == self::REDIS_LIST_FRONT && self::getConnection() )
			$pushResult = self::getConnection()->lPush( $key , $_value );

		return $pushResult > 0 ? true : false;
	}

	/**
	 * 获得一条数据
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_where	数据的位置，默认为链表头
	 * @return bool
	 */
	public function pop( $_key = '' , $_where = self::REDIS_LIST_FRONT )
	{
		$key = $this->calculateKey( $_key );

		// 根据指定的链表位置，获得数据
		if ( $_where == self::REDIS_LIST_END && self::getConnection() )
			$returnData = self::getConnection()->rPop( $key );
		else if ( $_where == self::REDIS_LIST_FRONT && self::getConnection() )
			$returnData = self::getConnection()->lPop( $key );

		return $returnData;
	}

	/**
	 * 替换值
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_value	给定的岁应 key 的 value 值
	 * @param string $_where	替换数据的位置，默认为链表尾端
	 * @return bool
	 */
	public function set( $_key = '' , $_value = '' , $_where = self::REDIS_LIST_END )
	{
		$key = $this->calculateKey( $_key );

		$setResult = 0;
		// 根据位置存储数据
		if ( $_where == self::REDIS_LIST_END && self::getConnection() )
			$setResult = self::getConnection()->lSet( $key , -1 , $_value );
		else if ( $_where == self::REDIS_LIST_FRONT && self::getConnection() )
			$setResult = self::getConnection()->lSet( $key , 0 , $_value );

		return $setResult > 0 ? true : false;
	}

	/**
	 * 获得值
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_where	数据的位置，默认为链表尾端
	 * @param int $_index		强制获得索引位置的数据
	 * @return bool
	 */
	public function get( $_key = '' , $_where = self::REDIS_LIST_END , $_index = 0 )
	{
		$key = $this->calculateKey( $_key );

		// 获得索引位置
		$intIndex = $_where == self::REDIS_LIST_END ? -1 : 0;
		if ( !empty( $_index ) )
			$intIndex = $_index;

		// 根据指定的链表位置，获得数据
		if ( self::getConnection() )
			$returnData = self::getConnection()->lIndex( $key , $intIndex );

		return $returnData;
	}

	/**
	 * 从List中删除一条数据
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_value	需要删除的 value
	 * @param string $_count	需要删除的个数，默认1个，负数表示从链表尾端开始删除
	 * @return bool
	 */
	public function remove( $_key = '' , $_value = '' , $_count = 1 )
	{
		$key = $this->calculateKey( $_key );

		// 从 List 中移除一个元素
		if ( self::getConnection() )
			$removeResult = self::getConnection()->lRem( $_key , $_value , $_count );

		return $removeResult > 0 ? true : false;
	}

	/**
	 * 判断key是否存在
	 *
	 * @param string $_key		给定的 redis key
	 * @return bool
	 */
	public function keyExists( $_key = '' )
	{
		$key = $this->calculateKey( $_key );

		$isExists = false;
		// 判断key是否存在
		if ( self::getConnection() )
			$isExists = self::getConnection()->exists( $key );

		return $isExists;
	}

//end class	
}
