<?php
/**
 * CPdo
 * 
 * 
 * @author samson.zhou<samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-31
 */
class CPdo extends CApplicationComponents
{
	private $host = null;	
	private $username = null;
	private $password = null;
	private $dbname = null;
	private $chargset = null;
	/**@var PDO*/
	private $dbh = null;
	private $pdoStatement = null;	
	
	public function init()
	{
		
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	/**
	 * pdo connect mysql
	 * 
	 */
	public function connect()
	{
		try
		{
			$this->dbh = new PDO( $this->getDsn() , $this->getUserName() , $this->getPassword() );
		} 
		catch( PDOException $e )
		{
			throw new CException( NBT_DEBUG ? $e->getMessage() : 'connect failed' );
		}
		$this->dbh->exec( 'set names "'.$this->chargset.'"' );
		//$sql = "SET time_zone = '-8:00';";
		//$this->dbh->exec($sql);
	}
	
	/**
	 * close
	 * 
	 */
	public function close()
	{
		$this->pdoStatement = null;
		$this->dbh = null;
	}
	
	/**
	 * get one record
	 * 
	 * @param string|CDbCriteria $_sql
	 * <pre>
	 * 		$_sql = "SELECT * FROM TB1 WHERE name=:name AND sex=:sex AND date >= :date";
	 * </pre>
	 * @param Array	$_aryParams
	 * <pre>
	 * 		array(":name"=>$name,":sex"=>$sex,":date"=>$date)
	 * </pre>
	 * @return Array
	 */
	public function find( $_sql , $_aryParams = array() )
	{
		if( $_sql instanceof  CDbCriteria )
			$_sql = $_sql->toSql();
		$this->addCollectSql( $_sql );
		try
		{
			$pdoStatement = $this->dbh->prepare( $_sql );
			foreach( $_aryParams as $k=>$v )
			{
				$pdoStatement->bindValue( $k , $v );
			}
			$result = $pdoStatement->execute();		
			if( !$result )
			{
				$aryErrorInfo = $pdoStatement->errorInfo();
				throw new CException( "{$aryErrorInfo[0]}-{$aryErrorInfo[1]}-{$aryErrorInfo[2]}----{$_sql}" );
			}
			$result = $pdoStatement->fetch( PDO::FETCH_ASSOC );
		}
		catch ( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}
		return ( $result === false ) ? array() : $result;
	}
	
	/**
	 * Get records. 
	 * @param string $_sql
	 * <pre>
	 * 		$_sql = "SELECT * FROM TB1 WHERE name=:name AND sex=:sex AND date >= :date";
	 * </pre>
	 * @param array $_aryParams
	 * <pre>
	 * 		array(":name"=>$name,":sex"=>$sex,":date"=>$date)
	 * </pre>
	 * @return Array
	 */
	public function findAll( $_sql , $_aryParams = array() )
	{
		if( $_sql instanceof  CDbCriteria )
			$_sql = $_sql->toSql();
		$this->addCollectSql( $_sql );
		//$aryFieldValues = empty( $_aryParams ) ? array() : array_fill(0,count($_aryParams),'?');
		try
		{
			$pdoStatement = $this->dbh->prepare( $_sql );
			foreach( $_aryParams as $k=>$v )
			{
				//$pdoStatement->bindValue( ($k+1) , $_aryParams[$k] );
				//$pdoStatement->bindParam( $k , $v );
				$pdoStatement->bindValue( $k , $v );
			}						
			$result = $pdoStatement->execute();
			if( !$result )
			{
				$aryErrorInfo = $pdoStatement->errorInfo();
				throw new CException( "{$aryErrorInfo[0]}-{$aryErrorInfo[1]}-{$aryErrorInfo[2]}----{$_sql}" );
			}
			$result = $pdoStatement->fetchAll( PDO::FETCH_ASSOC );
			return ( $result === false ) ? array() : $result;
		}
		catch ( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}		
	}
	
	/**
	 * count by sql
	 * 
	 * @param string $_sql
	 * <pre>
	 * 		$_sql = "SELECT count(*) FROM TB1 WHERE name=:name AND sex=:sex AND date >= :date";
	 * </pre>
	 * @param array $_aryParams
	 * <pre>
	 * 		array(":name"=>$name,":sex"=>$sex,":date"=>$date)
	 * </pre>
	 * @return Int
	 */
	public function count( $_sql , $_aryParams = array() )
	{
		//$aryFieldValues = empty( $_aryParams ) ? array() : array_fill(0,count($_aryParams),'?');
		$this->addCollectSql( $_sql );
		try
		{
			$pdoStatement = $this->dbh->prepare( $_sql );
			
			foreach( $_aryParams as $k=>$v )
			{
				//$pdoStatement->bindValue( ($k+1) , $_aryParams[$k] );
				$pdoStatement->bindValue( $k , $v );
			}
			$result = $pdoStatement->execute();
			if( !$result )
			{
				$aryErrorInfo = $pdoStatement->errorInfo();
				throw new CException( "{$aryErrorInfo[0]}-{$aryErrorInfo[1]}-{$aryErrorInfo[2]}----{$_sql}" );
			}
			return $pdoStatement->rowCount();
		}
		catch ( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}
	}
	
	/**
	 * Insert one row data.
	 * 
	 * @param string $_tableName table name.
	 * @param Array $_aryData
	 * 
	 */
	public function insert( $_tableName , $_aryData = array() )
	{
		$aryField = array_keys( $_aryData );
		$aryValues = array_values( $_aryData );
		$aryFieldValues = array_fill(0,count($aryField),'?');
		
		foreach( $aryField as $k=>$v )
		{
			$aryField[$k] = "`{$v}`";
		}
		$field = implode( ',' , $aryField );		
		$fieldValues = implode( ',' , $aryFieldValues );
		
		$sql = "INSERT INTO {$_tableName}($field)VALUES({$fieldValues});";
		$this->addCollectSql( $sql );
		try
		{
			$pdoStatement = $this->dbh->prepare( $sql );
			foreach( $aryFieldValues as $k=>$v )
			{
				$pdoStatement->bindValue( ($k+1) , $aryValues[$k] );
			}
			$res = $pdoStatement->execute();
			if( !$res )
			{
				$aryErrorInfo = $pdoStatement->errorInfo();
				throw new CException( "{$aryErrorInfo[0]}-{$aryErrorInfo[1]}-{$aryErrorInfo[2]}----{$sql}" );
			}
			else
			{
				$res = $this->lastInsertId();
			}
			return $res;
		}
		catch( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}
		if( $res )
		{
			$res = $this->dbh->lastInsertId();
		}
		return $res;
	}
	
	/**
	 * 
	 */
	public function lastInsertId()
	{
		$lastInsertId = $this->dbh->lastInsertId();
		return $lastInsertId;
	}
	
	/**
	 * 
	 * @param String $_tableName
	 * @param Array $_aryData
	 * @param string $_where
	 * <pre>
	 * 		$_where = "name=? AND sex=? AND date >= ?";
	 * </pre>
	 * @param Array $_aryParams
	 * <pre>
	 * 		array($name,$sex,$date)
	 *	</pre>
	 */
	public function update( $_tableName , $_aryData = array() , $_where = "" , $_aryParams = array() )
	{
		$aryField = array_keys( $_aryData );
		$aryValues = array_values( $_aryData );
		$arySet = array();
		
		foreach( $aryField as $v )
		{
			$arySet[] = "`{$v}`=?";	
		}
		
		$set = implode( ',' , $arySet );
		$where = empty( $_where ) ? "" : "WHERE {$_where}";
		
		$sql = "UPDATE {$_tableName} SET {$set} {$where}";
		$this->addCollectSql( $sql );
		try
		{
			$pdoStatement = $this->dbh->prepare( $sql );
			$i = 1;
			foreach( $aryField as $k=>$v )
			{
				$pdoStatement->bindValue( $i , $aryValues[$k] );
				$i++;
			}
			foreach( $_aryParams as $k=>$v )
			{
				$pdoStatement->bindValue( $i , $v );
				$i++;
			}
			$res = $pdoStatement->execute();
			if( $res === false )
			{
				$aryErrorInfo = $pdoStatement->errorInfo();
				throw new CException( "{$aryErrorInfo[0]}-{$aryErrorInfo[1]}-{$aryErrorInfo[2]}----{$sql}" );
			}
			return $res;
		}
		catch( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}
	}
	
	/**
	 * delete by condition
	 * 
	 * @param string $_tableName
	 * @param string $_where
	 * <pre>
	 * 		$_where = "name=:name AND sex=:sex AND date<=:date";
	 * </pre>
	 * @param Array $_aryParams
	 * <pre>
	 * 		array(":name"=>$name,":sex"=>$sex,":date"=>$date)
	 * </pre>
	 * @return effect rows
	 * 
	 */
	public function delete( $_tableName , $_where = "" , $_aryParams = array() )
	{
		$where = empty( $_where ) ? "" : "WHERE {$_where}";
		$sql = "DELETE FROM {$_tableName} {$where}";
		$this->addCollectSql( $sql );
		
		try
		{
			$pdoStatement = $this->dbh->prepare( $sql );
			foreach ( $_aryParams as $k=>$v )
			{
				$pdoStatement->bindValue( $k , $v );
			}
			$res = $pdoStatement->execute();
			if( !$res )
			{
				$aryErrorInfo = $pdoStatement->errorInfo();
				throw new CException( "{$aryErrorInfo[0]}-{$aryErrorInfo[1]}-{$aryErrorInfo[2]}----{$sql}" );
			}
			return $res;
		}
		catch( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}
	}
	
	/**
	 * 获取表字段
	 * 
	 * 
	 * @param string $_tableName
	 * @return Array
	 */
	public function getTableField( $_tableName )
	{
		$strSql = "SELECT * FROM {$_tableName} LIMIT 0,1";
		$pdoStatement = $this->dbh->prepare( $strSql );
		$res = $pdoStatement->execute();
		$field = array();
		for( $i=0 ; $count=$pdoStatement->columnCount(),$i<=$count-1; $i++ )
		{
			$columnMeta = $pdoStatement->getColumnMeta($i); 
			$field[] = $columnMeta['name']; 
		}
		return $field;
	}
	
	/**
	 * 事务开启
	 *
	 */
	public function beginTransaction()
	{
		$this->addCollectSql( 'beginTransaction' );
		$this->dbh->beginTransaction();
	}
	
	/**
	 * 事务提交
	 *
	 */
	public function commit()
	{
		$this->addCollectSql( 'commit' );
		$this->dbh->commit();
	}
	
	/**
	 * 事务回滚
	 *
	 */
	public function rollBack()
	{
		$this->addCollectSql( 'rollBack' );
		$this->dbh->rollBack();
	}
	
	/**
	 * 将sql脚本添加到本次访问请求中
	 *
	 * @param string $_strSql
	 */
	public function addCollectSql( $_strSql = "" )
	{
		//$this->collectSql[] = $_strSql;
		Nbt::$aryCollectSql[] = $_strSql;
	}
	
	/**
	 * 获取本次访问请求的sql脚本
	 *
	 * @return string
	 */
	public function getCollectSql()
	{
		return $this->collectSql;
	}

	/**
	 * 设置数据源连接DSN
	 *
	 * @param string $_dsn
	 */
	public function setDsn( $_dsn )
	{
		$this->dsn = $_dsn;
	}
	
	/**
	 * 获取数据源连接DSN
	 *
	 * @return string
	 */
	public function getDsn()
	{
		return $this->dsn;
	}
	
	/**
	 * 设置用户名
	 *
	 * @param string $_username
	 */
	public function setUserName( $_username )
	{
		$this->username = $_username;
	}
	
	/**
	 * 获取用户名
	 *
	 * @return string
	 */
	public function getUserName()
	{
		if( $this->username === null )
			throw new CException( NBT_DEBUG ? 'CMysqlPdo have not set username.' : 'error:010002' );
		return $this->username;
	}
	
	/**
	 * 设置数据库密码
	 *
	 * @param unknown_type $_password
	 */
	public function setPassword( $_password )
	{
		$this->password = $_password;
	}
	
	/**
	 * 获取数据库密码
	 *
	 * @return string
	 */
	public function getPassword()
	{
		if( $this->password === null )
			throw new CException( NBT_DEBUG ? 'CMysqlPdo have not set password.' : 'error:010003' );
		return $this->password;
	}
	
	/**
	 * 设置当前使用的数据库名
	 *
	 * @param string $_dbname
	 */
	public function setDbname( $_dbname )
	{
		$this->dbname = $_dbname;
	}
	
	/**
	 * 获取当前使用的数据库名
	 *
	 * @return string
	 */
	public function getDbname()
	{
		if( $this->password === null )
			throw new CException( NBT_DEBUG ? 'CMysqlPdo have not set database name.' : 'error:010004' );
		return $this->dbname;
	}
	
	/**
	 * 设置字符集
	 *
	 * @param string $_chargset
	 */
	public function setChargset( $_chargset )
	{
		$this->chargset = $_chargset;
	}
	
	/**
	 * 获取字符集
	 *
	 * @return string
	 */
	public function getChargset()
	{
		if( $this->chargset === null )
			throw new CException( NBT_DEBUG ? 'CMysqlPdo have not set chargset.' : 'error:010005' );
		return $this->chargset;
	}
	
	/**
	 * 切换数据库并使用数据库
	 *
	 * @param string $_dbname	数据库名
	 */
	public function changeDb( $_dbname )
	{
		$this->setDbname( $_dbname );
		$strSql = "USE {$_dbname}";
		$this->addCollectSql( $strSql );
		$this->dbh->exec( $strSql );
	}

	/**
	 * 执行纯SQL
	 *
	 * @param string $_strSql SQL语句
	 */
	public function execute( $_strSql )
	{
		try
		{
			return $this->dbh->exec( $_strSql );
		}
		catch( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}
	}

	/**
	 * 执行纯SQL
	 *
	 * @param string $_strSql SQL语句
	 */
	public function query( $_strSql )
	{
		try
		{
			return $this->dbh->query( $_strSql );
		}
		catch( PDOException $e )
		{
			throw new CException( $e->getMessage() );
		}
	}
	
//end class	
}
