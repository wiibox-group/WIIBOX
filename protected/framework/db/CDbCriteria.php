<?php
class CDbCriteria extends CApplicationComponents
{
	public $select = "*";
	public $from = "";
	public $join = array();
	public $condition = "";
	public $order = "";
	public $offset = null;
	public $limit = null;
	public $group = ""; 
	public $params = array();

	public function addCondition($condition,$operator='AND')
	{
		if( $this->condition != "" )
		{
			$this->condition .= " {$operator} {$condition}";
		}
		else
		{
			$this->condition = $condition;
		}
		/*
		if(is_array($condition))
		{
			if($condition===array())
				return $this;
			$condition='('.implode(') '.$operator.' (',$condition).')';
		}
		if($this->condition==='')
			$this->condition=$condition;
		else
			$this->condition='('.$this->condition.') '.$operator.' ('.$condition.')';
		*/
	}

	/**
	 * 设置 JOIN 语句
	 *
	 * @params string $_strJoin	JOIN方式
	 * @params string $_strJoinCondition	JOIN条件语句
	 * @return void
	 */
	public function setJoin( $_strJoin = 'LEFT' , $_strJoinCondition = '' )
	{
		$this->join[] = " {$_strJoin} JOIN {$_strJoinCondition}";
	}
	
	public function toSql()
	{
		if( empty( $this->select ) )
			throw new CException( NBT_DEBUG ? "CDbCriteria->select is not allowed empty." : 'error:010101' );
		if( empty( $this->from ) )
			throw new CException( NBT_DEBUG ? "CDbCriteria->from is not allowed empty." : 'error:010102' );
		
		$join = count( $this->join ) > 0 ? implode( "" , $this->join ) : "";
		$where = empty( $this->condition ) ? "" : "WHERE {$this->condition}";
		$order = empty( $this->order ) ? "" : "ORDER BY {$this->order}";
		$group = empty( $this->group ) ? "" : "GROUP BY {$this->group}";
		$limit = "";
		if( !empty( $this->limit ) )
		{
			if( $this->offset < 0 )
				throw new CException( NBT_DEBUG ? "CDbCriteria->offset is not allowed empty." : 'error:010103' );
			$limit = "LIMIT {$this->offset},{$this->limit}";
		}
		//echo "SELECT {$this->select} FROM {$this->from} {$where} {$order} {$group} {$limit}";;
		return "SELECT {$this->select} FROM {$this->from} {$join} {$where} {$order} {$group} {$limit}";
	}
	

//end class
}
