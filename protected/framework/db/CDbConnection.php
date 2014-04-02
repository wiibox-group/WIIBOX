<?php
class CDbConnection
{
	private static $_dbConnectionWeb = null;
	private static $_dbTransaction = array(
				'web'=>'getWebDbConnection'
			);

	/**
	 * 获取Web Db connection
	 * @return CPdo
	 */
	public static function getWebDbConnection()
	{
		if( self::$_dbConnectionWeb === null )
		{
			$db = new CPdo(); 
			$db->setDsn( DB_WEB_DSN );
			$db->setUserName( DB_WEB_USERNAME );
			$db->setPassword( DB_WEB_PASSWORD );
			$db->setChargset( DB_WEB_CHARGSET );
			$db->connect();
			
			self::$_dbConnectionWeb = $db;
		}
		return self::$_dbConnectionWeb;
	}

	/**
	 * 根据要求开启事务
	 *
	 * @params array $_aryTransDb 需要开启的事务集
	 * @return void
	 */
	public static function startTransaction( $_aryTransDb = array() )
	{
		foreach ( $_aryTransDb as $dbName )
		{
			if ( array_key_exists( $dbName , self::$_dbTransaction ) )
			{
				$method = self::$_dbTransaction[$dbName];
				self::$method()->beginTransaction();
			}
		}
	}

	/**
	 * 根据要求提交事务
	 *
	 * @params array $_aryTransDb 需要提交的事务集
	 * @return void
	 */
	public static function commitTransaction( $_aryTransDb = array() )
	{
		foreach ( $_aryTransDb as $dbName )
		{
			if ( array_key_exists( $dbName , self::$_dbTransaction ) )
			{
				$method = self::$_dbTransaction[$dbName];
				self::$method()->commit();
			}
		}
	}

	/**
	 * 根据要求回滚事务
	 *
	 * @params array $_aryTransDb 需要回滚的事务集
	 * @return void
	 */
	public static function rollBackTransaction( $_aryTransDb = array() )
	{
		foreach ( $_aryTransDb as $dbName )
		{
			if ( array_key_exists( $dbName , self::$_dbTransaction ) )
			{
				$method = self::$_dbTransaction[$dbName];
				self::$method()->rollBack();
			}
		}
	}
	
//end class
}
