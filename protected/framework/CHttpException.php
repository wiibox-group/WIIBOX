<?php
/**
 * CHttpExceptin class file.
 * 
 * 
 * 
 * @author samson.zhou <samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-08-12
 */

class CHttpException extends CException
{
	/**
	 * @var integer HTTP status code, such as 403, 404, 500, etc.
	 */
	public $statusCode;

	/**
	 * Constructor.
	 * @param integer HTTP status code, such as 404, 500, etc.
	 * @param string error message
	 * @param integer error code
	 */
	public function __construct( $status , $message=null , $code=0 )
	{
		$this->statusCode = $status;
		parent::__construct( $message , $code );
	}
	
//end class	
}