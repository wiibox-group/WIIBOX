<?php
/**
 * CSort Class Files.
 * 
 * 
 * 
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-09-24
 */
class CSort extends CComponents
{
	/**
	 * sort name in url 
	 * http://demo.com/index.php?r=demo/index&sort=id.desc
	 * 
	 * @var string
	 */
	public $sortVar = "sort";
	
	/**
	 * desc name in url
	 * http://demo.com/index.php?r=demo/index&sort=id.$descTag
	 * 
	 * @var string
	 */
	public $descTag = "desc";
	
	/**
	 * asc name in url
	 * http://demo.com/index.php?r=demo/index&sort=id.$ascTag
	 * 
	 * @var string
	 */
	public $ascTag = "asc";
	
	/**
	 * Class name when order by desc
	 * <a class="$descClass"></a>
	 * 
	 * @var string
	 */
	public $descClass = "sort_down";
	
	/**
	 * Class name when order by asc
	 * <a class="$ascClass"></a>
	 * 
	 * @var string
	 */
	public $ascClass = "sort_up";
	
	/**
	 * CDbCriteira default order
	 * <pre>
	 * 		$defaultOrder = "id DESC";
	 * </pre>
	 * 
	 * @var string
	 */
	public $defaultOrder = "";
	
	/**
	 * separator in url sort field
	 * <pre>
	 * 		http://demo.com/index.php?r=demo/index&sort=id[$separators]desc	
	 * 		http://demo.com/index.php?r=demo/index&sort=id.desc
	 * </pre>
	 * 
	 * @var string
	 */
	public $separators = ".";
	
	/**
	 * sort field and sort type
	 * <pre>
	 * 		$_director = array(
	 * 							'id'=>'desc',
	 * 						);
	 * </pre>
	 * 
	 * @var string
	 */
	private $_director = null;
	
	/**
	 * init
	 * 
	 */
	public function __construct()
	{
	
	}
	
	/**
	 * set CDbCriteria->order
	 * 
	 * 
	 * @param CDbCriteria $_criteria
	 */
	public function applyOrder( CDbCriteria $_criteria )
	{
		$order = $this->getOrderBy();
		if( !empty( $order ) )
		{
			if( !empty( $_criteria->order ) )
			{
				$_criteria->order .= ', ';
			}
			$_criteria->order .= $order;
		}
	}
	
	/**
	 * Get order
	 * 
	 * 
	 * @return string
	 */
	public function getOrderBy()
	{
		$order = "";
		foreach( $this->getDirector() as $field=>$sort )
		{
			if( $order != "" )
			{
				$order .= ",";
			}
			$order .= "{$field} {$sort}";
		}
		if( $order == "" )
		{
			$order = $this->defaultOrder;
		}		
		return $order;
	}
	
	/**
	 * get sort url
	 * 
	 * @param string $_fieldName	field name in database
	 * @param string $_label		header of report
	 * @param string $_htmlOptions	more link options.@see CHtml::link() htmloptions.
	 */
	public function link( $_fieldName , $_label = null , $_htmlOptions = array() )
	{
		$director = $this->getDirector();
		$newLinkOrder = $this->ascTag;
		
		if(isset($director[$_fieldName]))
		{
			$attOrder = $director[$_fieldName];
			
			if( $attOrder == $this->ascTag )
			{
				$newLinkOrder = $this->descTag;
			}
			
			$aryConfigClass = array( $this->descTag=>$this->descClass , $this->ascTag=>$this->ascClass );
			$class = $aryConfigClass[$attOrder];
			if(isset($_htmlOptions['class']))
				$_htmlOptions['class'].=' '.$class;
			else
				$_htmlOptions['class']=$class;
		}
		
		$routeName = Nbt::app()->getRequest()->routeName;		
		$aryParams = isset( $_GET ) ? $_GET : array();
		
		$aryParams[$this->sortVar] = "{$_fieldName}{$this->separators}{$newLinkOrder}";
		$route = isset( $aryParams[$routeName] ) ? $aryParams[$routeName] : null;
		unset($aryParams[$routeName]);
		
		$url = Nbt::app()->createUrl( $route , $aryParams );
		
		return CHtml::link( $_label , $url , $_htmlOptions );
	}
	
	/**
	 * get sort field and sort type.
	 * 
	 * @return array
	 * @see $this->_director.
	 */
	public function getDirector()
	{
		if( $this->_director === null )
		{
			$this->_director = array();
			if(isset($_GET[$this->sortVar]))
			{
				$attribute=explode($this->separators,$_GET[$this->sortVar]);
				$attName = isset($attribute[0])?$attribute[0]:'';
				$attOrder = isset($attribute[1])?$attribute[1]:'';				
				$this->_director[$attName]=$attOrder;
			}
		}
		return $this->_director;
	}
	
//end class	
}