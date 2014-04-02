<?php
/**
 * base model.
 * 
 * 
 * 
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-13
 */
abstract class CModel extends CApplicationComponents
{
	private $_scenario = null;
	private $_aryError = array();
	private $_aryData = array();
	private $_dbconnection = null;
	
	private static $_classModel = array();
	
	public function init()
	{
		parent::init();
	}
	
	public static function model( $className = __CLASS__ )
	{
		if( !isset( self::$_classModel[$className] ) )
			self::$_classModel[$className] = new $className();
		
		return self::$_classModel[$className];
	}
	
	/**
	 * 获取数据库连接
	 *
	 * @return CPdo
	 */
	public function getDb()
	{
		if( $this->_dbconnection instanceof  DbConnection )
			throw new CException( NBT_DEBUG ? 'CModel->getDb() is not a corrected DbConnection.' : 'error:010006' );
			
		return $this->_dbconnection;
	}
	
	/**
	 * 设置数据库连接
	 *
	 * @param DbConnection $_objDbConnection  数据库连接
	 * @return CPdo
	 */
	public function setDb( CPdo $_objDbConnection )
	{
		if( $_objDbConnection instanceof  DbConnection )
			throw new CException( NBT_DEBUG ? 'CModel->setDb() is not a corrected DbConnection.' : 'error:010006' );
		$this->_dbconnection = $_objDbConnection;
	}
	
	/**
	 * 使用指定的Database数据库
	 *
	 * @param string $_strDatabaseName	数据库名称
	 * @return CModel
	 */
	public function useDb( $_strDatabaseName = "" )
	{
		$this->getDb()->changeDb( $_strDatabaseName );
		return $this;
	}
	
	/**
	 * table name
	 */
	public function tableName()
	{
		throw new CException( get_class($this).' have not defined table name.' );
	}
	
	/**
	 * primary key
	 */
	public function primaryKey()
	{
		throw new CException( get_class($this).' have not defined primary key.' );
	}	
	
	/**
	 * validate rules
	 * 
	 */
	public function rules(){
		return array();
	}
	
	/**
	 * 英文字段转换为中文名称
	 * 主要用于验证的提示信息获取
	 *
	 * @param string $_fieldEn
	 * @return atring
	 */
	public function fieldCnName()
	{
		return array();
	}
	
	/**
	 * 添加错误信息
	 * 
	 */
	public function addError( $_key , $_val )
	{
		if( !isset( $this->_aryError[$_key] ) )
		{
			$this->_aryError[$_key] =  array();
		}
		$aryConfigFieldCnName = $this->fieldCnName();
		$keyCn = isset( $aryConfigFieldCnName[$_key] ) ? $aryConfigFieldCnName[$_key] : $_key;
		$this->_aryError[$_key][] = $keyCn.$_val;
		//unique
		$this->_aryError[$_key] = array_unique( $this->_aryError[$_key] );
	}
	
	/**
	 * 返回所有的错误信息
	 * 
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_aryError;
	}
	
	/**
	 * 获取某一特定的错误信息
	 * 
	 * 
	 * @return string | null
	 */
	public function getError( $_key , $_isToString = TRUE , $_toStringSeparater = ";")
	{
		$aryError = isset( $this->_aryError[$_key] ) ? $this->_aryError[$_key] : array(); 
		if( $_isToString )
		{
			if( !empty($aryError) && is_array( $aryError ) )
			{
				return implode( $_isToString , $aryError );
			} 
			return '';
		}
		return  $aryError;
	}
	
	/**
	 * 是否有某一特定的错误信息
	 * 
	 * 
	 */
	public function hasError( $_key = null )
	{
		if( $_key !== null )
			return isset( $this->_aryError[$_key] ) ? true : false;
		else
			return empty( $this->_aryError ) ? false : true;
	}
	
	/**
	 * 设置业务应用场景
	 * 
	 */
	public final function setScerian( $_scenario )
	{
		$this->_scenario = $_scenario;
	}
	
	/**
	 * 获取业务应用场景
	 * 
	 */
	public final function getScerian()
	{
		return $this->_scenario;	
	}
	
	/**
	 * 设置传入当前model的数据
	 * 
	 * @param array $_aryData
	 */
	public final function setData( $_aryData = array() )
	{
		$this->_aryData = $_aryData;
	}
	
	/**
	 * 设置传入当前model的数据
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $value
	 */
	public final function setDataItem( $_key = null , $value = "" )
	{
		$this->_aryData[$_key] = $value;
	}
	
	/**
	 * 获取当前传入model的数据
	 * 
	 */
	public final function getData( $_key = null , $_isEncode = false )
	{
		if( $_key !== null )
		{
			$res = isset( $this->_aryData[$_key] ) ? $this->_aryData[$_key] : '';
			return  $_isEncode ? CHtml::encode( $res ) : $res;
		}
		return $this->_aryData;
	}
	
	/**
	 * 校验规则
	 * 
	 */
	public function validate( &$_aryData = array() )
	{
		$boolres = true;
		$aryRules = $this->rules();
		$aryValidators = array(
								'required'=>'cRequired',
								'length'=>'cLength',
								'compare'=>'cCompare',
								'email'=>'cEmail',
								'number'=>'cNumber',
								'datetime'=>'cDatetime',
								'date'=>'cDate',
								'time'=>'cTime',
								'string'=>'cString',
								'array'=>'cArray',
								'integer'=>'cInteger',
								'float'=>'cFloat',
								'url'=>'cUrl',
								'regular'=>'cRegular',								
								'captcha'=>'cCaptcha',
							);
		foreach( $aryRules as $rules )
		{
			$aryRuleParams = array();
			$fields = array();
			$rule = "";
			$message = "";
			$on = array();
			foreach( $rules as $keyRules=>$valRules )
			{
				switch( $keyRules )
				{
					case '0':
						$fields = explode(',',$rules[0]);
						break;
					case '1':
						$rule = $rules[1];
						break;
					case 'message':
						$message = $rules['message'];
						break;
					case 'on':
						$on = explode(',',$rules['on']);
						break;
					default:
						$aryRuleParams[$keyRules] = $valRules;
						break;
				}
			}
			//check scerian
			if( in_array( $this->getScerian() , $on ) || empty( $on ) )
			{
				foreach( $fields as $field )
				{
					$field = trim( $field );
					if( $this->hasError( $field ) )
						continue;
					if( in_array( $rule , array_keys($aryValidators) ) )
					{
						$cyrule = $aryValidators[$rule];
						$val = isset($_aryData[$field])?$_aryData[$field]:null;
						if( !method_exists( 'CValidator' , $cyrule ) )
							throw new CException( "CValidator have not defined function {$cyrule}." );
						if( !CValidator::$cyrule( $val , $aryRuleParams , $message ) )
						{
							$this->addError( $field , $message );
							$boolres = false;
						}						
					}
					elseif( method_exists( $this , $rule ) )
					{
						if( !$this->$rule( $field , $aryRuleParams ) )
						{
							$this->addError( $field , $message );
							$boolres = false;
						}
					}
					else
					{
						throw new CException( "{$rule} have not defined." );
					}
				}
			}
		}
		return $boolres;
	}
	
	/**
	 * 根据主键删除记录
	 * <pre>
	 * 		$_strCondition = "name=:name AND sex=:sex AND date >= :date";
	 * 		$_aryParams = array(
	 * 								':name'=>'xx',
	 * 								':sex'=>'man',
	 * 								':date'=>'2010-06-15',
	 * 							);
	 * </pre>
	 * 
	 * @param	Int		$_intPk		the primary key
	 * @param 	String	$_strCondition		condition of delete
	 * @param	Array	$_aryParams
	 * @return	Bool	
	 * 
	 */
	public  function deleteByPk( $_intPk = null , $_strCondition = null , $_aryParams = array() )
	{
		$primaryKey = $this->primaryKey();
		$where = "{$primaryKey} = :{$primaryKey}";
		$_aryParams[":{$primaryKey}"] = $_intPk;
		
		if( $_strCondition !== null )
		{
			$where .= " AND {$_strCondition}";
		}
		$res = $this->getDb()->delete( $this->tableName() , $where , $_aryParams );
		return ( $res === false ) ? false : true;
	}
	
	/**
	 * 根据条件删除记录集
	 * <pre>
	 * 		$_strCondition = "name=:name AND sex=:sex AND date >= :date";
	 * 		$_aryParams = array(
	 * 								':name'=>'xx',
	 * 								':sex'=>'man',
	 * 								':date'=>'2010-06-15',
	 * 							);
	 * </pre> 
	 * @param	string	$_strCondition	Condition of delete.
	 * @param	array	$_aryParams
	 * @return false|Intregrer
	 */
	public function deleteAll( $_strCondition = null , $_aryParams = array() )
	{
		$where = "";
		if( $_strCondition !== null )
		{
			$where = $_strCondition;
		}
		$res = $this->getDb()->delete( $this->tableName() , $where , $_aryParams ) ;
		return $res;
		//return ( $res === false ) ? false : true;
	}
	
	/**
	 * 根据查询条件对像返回一条记录
	 * 
	 * @param	CDbCriteria|condition	$_criteria
	 * @retrun	array
	 */
	public function find( $_criteria = null )
	{
		if( $_criteria instanceof CDbCriteria )
			$criteria = $_criteria;
		else
		{
			$criteria = new CDbCriteria();
			$criteria->condition = $_criteria;
		}
		
		if( empty( $criteria->from ) )
			$criteria->from = $this->tableName();
		$strSql = $criteria->toSql();
		return $this->getDb()->find( $strSql , $criteria->params );
	}
	
	/**
	 * 根据主键返回一条记录
	 * 
	 * @param	Int		$_intPk
	 * @param	String|CDbCriteria	$_criteria
	 * @return	array	
	 */
	public function findByPk( $_intPk = null , $_criteria = null )
	{
		$condition = " {$this->primaryKey()} = '{$_intPk}' ";
		if( !($_criteria instanceof CDbCriteria) )
		{
			$criteria = new CDbCriteria();
			if( !empty( $_criteria ) )
			{
				$condition .= " AND {$_criteria}";
			}
		}
		else
		{
			$criteria = $_criteria;
		}
		$oldCondition = $criteria->condition;
		if( !empty( $oldCondition ) )
		{
			$condition .= " AND {$oldCondition}";
		}	
		$criteria->condition = $condition;
		if( empty( $criteria->from ) )
			$criteria->from = $this->tableName();
		$sql = $criteria->toSql();
		return $this->getDb()->find( $sql , $criteria->params );
	}
	
	/**
	 * 根据条件返回一条记录
	 * <pre>
	 * 		$_sql = "SELECT * FROM TB1 WHERE name=:name AND sex=:sex AND date >= :date";
	 * 		$_aryParams = array(
	 * 								':name'=>'xx',
	 * 								':sex'=>'man',
	 * 								':date'=>'2010-06-15',
	 * 							);
	 * </pre>
	 * @param string $_sql
	 * @param array $_aryParams
	 * @return Array
	 */
	public function findBySql( $_sql , $_aryParams = array() )
	{
		$strSql = $_sql;
		return $this->getDb()->find( $strSql , $_aryParams );
	}
	
	/**
	 * 根据查询条件对像返回多条记录集
	 * 
	 * @param	String	CDbCriteria|condition $_criteria
	 * @return	array	
	 */
	public function findAll( $_criteria = null )
	{
		if( $_criteria instanceof CDbCriteria )
			$criteria = $_criteria;
		else
		{
			$criteria = new CDbCriteria();
			$criteria->condition = $_criteria;
		}
		
		if( empty( $criteria->from ) )
			$criteria->from = $this->tableName();
		return $this->getDb()->findAll( $criteria , $criteria->params );
	}
	
	/**
	 * 根据sql条件查询记录集
	 * <pre>
	 * 		$_sql = "SELECT * FROM TB1 WHERE name=:name AND sex=:sex AND date >= :date";
	 * 		$_aryParams = array(
	 * 								':name'=>'xx',
	 * 								':sex'=>'man',
	 * 								':date'=>'2010-06-15',
	 * 							);
	 * </pre>
	 * @param string $_sql
	 * @return Array
	 */
	public function findAllBySql( $_sql , $_aryParams = array() )
	{
		return $this->getDb()->findAll( $_sql , $_aryParams );
	} 
	
	/**
	 * 统计符合查询条件对像的记录数
	 * 
	 * @param	CDbCriteria	$_criteria
	 * @return Int	
	 */
	public function count( CDbCriteria $_criteria )
	{
		if( empty( $_criteria->from ) )
			$_criteria->from = $this->tableName();
		$where = empty( $_criteria->condition ) ? "" : "WHERE $_criteria->condition";
		$join = count( $_criteria->join ) > 0 ? implode( "" , $_criteria->join ) : "";
		$strSql = "SELECT count(*) AS count FROM {$_criteria->from} {$join} {$where}";
		$res = $this->getDb()->find( $strSql , $_criteria->params );
		return $res['count'];
		//return $this->getDb()->count( $strSql , $_criteria->params ); 
	}
	
	/**
	 * 统计符合查询条件的记录数
	 * 
	 * 
	 * @param string $_sql
	 * @param array $_aryParams
	 * @todo
	 */
	public function countBySql( $_sql , $_aryParams = array() )
	{
		$res = $this->getDb()->find( $_sql , $_aryParams );
		return isset( $res['count'] ) ? $res['count'] : 0;
	}
	
	/**
	 * 判断符合条件的记录是否存在
	 * <pre>
	 * 		$_where = "name=:name AND sex=:sex AND date >= :date";
	 * 		$_aryParams = array(
	 * 								':name'=>'xx',
	 * 								':sex'=>'man',
	 * 								':date'=>'2010-06-15',
	 * 							);
	 * </pre>
	 * @param string $_where
	 * @param array $_aryParams
	 * @return bool
	 */
	public function exists( $_where = "" , $_aryParams = array() )
	{
		$criteria = new CDbCriteria();
		$criteria->from = $this->tableName();
		$criteria->condition = $_where;
		$criteria->offset = 0;
		$criteria->limit = 1;
		$aryRes = $this->getDb()->find( $criteria->toSql() , $_aryParams );
		return empty( $aryRes ) ? false : true;
	} 
	
	/**
	 * 新增一条数据
	 * 
	 * @param	array	$_aryRow
	 * @param	bool	$_isValidate
	 * @return	false|interger	If insert success return interger,otherwise return false.
	 */
	public function insert( $_isValidate = true )
	{
		$aryRow = $this->getData();
		if( empty($aryRow) )
			throw new CException( NBT_DEBUG ? 'CModel->setData() is not called before CModel->updateByCondition' : 'error:010107' );
		if( $this->getScerian() === null )
        {
            $this->setScerian('insert');
        }
		if( $_isValidate && !$this->validate( $aryRow ) )
		{
			return false;
		}
		return $this->getDb()->insert( $this->tableName() , $aryRow );
	}
	
	/**
	 * 新增多条数据
	 * 
	 * 
	 */
	public function insertRowSet( $_isValidate = true , $_isStopWhenError = false )
	{
//		$aryRow = $this->getData();
//		if( $_isValidate && $this->validate( $aryRow ) )
//		{
//			return $this->getDb()->insert( $this->tableName() , $aryRow );
//		}
//		return false;
		throw new CException( 'The function insertRowSet() has no implement.' );
	}
	
	/**
	 * 根据数据
	 * 
	 * @param Bool $_isValidate
	 * @return bool
	 */
	public function update( $_isValidate = true )
	{
		$aryRow = $this->getData();
		if( empty($aryRow) )
			throw new CException( NBT_DEBUG ? 'CModel->setData() is not called before CModel->updateByCondition' : 'error:020001' );
		if( $_isValidate && !$this->validate( $aryRow ) )
		{
			return false;
		}
		
		$primaryKey = $this->primaryKey();
		$res =$this->getDb()->update( $this->tableName() , $aryRow , "{$primaryKey} = ?" , array($aryRow[$primaryKey]) );
		return $res === false ? false : true;
	}
	
	/**
	 * 
	 * 根据主键和其它条件更新记录
	 * <pre>
	 * 		$_strCondition = "name=? AND sex=? AND date >=?";
	 * 		$_aryParams = array($name,$sex,$date);
	 * </pre> 
	 * 
	 * @param Int $_intPk
	 * @param string $_strCondition
	 * @param array $_aryParams
	 * @return bool
	 */
	public function updateByPk( $_pk = null , $_strCondition = null , $_aryParams = array() )
	{
		$aryRow = $this->getData();
		if( empty($aryRow) )
			throw new CException( NBT_DEBUG ? 'CModel->setData() is not called before CModel->updateByCondition' : 'error:020001' );		
		if( $this->validate( $aryRow ) )
		{
			$primaryKey = $this->primaryKey();
			$where = "{$primaryKey} = ?";
			$aryParams = array($_pk);
			$aryParams = array_merge( $aryParams , $_aryParams );
			if( $_strCondition !== null )
				$where .= " AND {$_strCondition}";
			$res =$this->getDb()->update( $this->tableName() , $aryRow , $where , $aryParams );
			return $res === false ? false : true;
			//return $this->getDb()->updateByPk();
		}
		return false;
	}
	
	/**
	 * 根据条件更新数据
	 * before update you should validate the data.
	 * 
	 * <pre>
	 * 		$_strCondition = "name=? AND sex=? AND date >= ?";
	 * 		$_aryParams = array($name,$sex,$date);
	 * </pre>
	 * @param	string	$_strCondition
	 * @param	array	$_aryParams
	 * @return 	bool
	 */
	public function updateByCondition( $_aryData = array() , $_strCondition = null , $_aryParams = array() , $_isValidate = false )
	{
		$aryRow = $_aryData;
		if( empty($aryRow) )
			throw new CException( NBT_DEBUG ? 'CModel->updateByCondition() $_aryData is not allowed empty.' : 'error:020001' );
		
        if( $_isValidate && !$this->validate( $aryRow ) )
		{
			return false;
		}
        
		$where = "";
		if( $_strCondition !== null )
			$where = $_strCondition;
		$res = $this->getDb()->update( $this->tableName() , $aryRow , $where , $_aryParams );
		return $res === false ? false : true;
	}
	
	/**
	 * 执行纯SQL
	 *
	 * @params string $_strSql SQL语句
	 * @params string $_strExecuteWay 执行方式，默认exec执行，可选为query
	 * @return bool
	 */
	public function executeSql( $_strSql = '' , $_strExecuteWay = 'exec' )
	{
		if ( empty( $_strSql ) ) 
			return false;

		if ( $_strExecuteWay == 'exec' )
			return $this->getDb()->execute( $_strSql );
		else if ( $_strExecuteWay == 'query' )
			return $this->getDb()->query( $_strSql );
		else
			return false;
	}

//end class	
}
