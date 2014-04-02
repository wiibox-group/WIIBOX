<?php
class CValidator
{
	public static function cRequired( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		if( self::isEmpty( $_val ) )
		{
			$_message = empty( $_message ) ? "can not be empty." : $_message;
			return false;
		}
		return true;
	}
	
	public static function cLength( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$min = isset( $_aryParams['min'] ) ? $_aryParams['min'] : null;
		$max = isset( $_aryParams['max'] ) ? $_aryParams['max'] : null;
		
		//if( $min === null && $max === null )
		//	throw new CException( "CValidator::cLength() must defined min value or max value." );
		
		$length = strlen( $_val );
		$message = "";
		if( $min !== null && $length < $min )
		{
			$message = empty( $message ) ? "min length is {$min}." : $message;
		}
		if( $max !== null && $length > $max )
		{
			$message = empty( $message ) ? "max length is {$max}." : $message;
		}
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cCompare( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$compareValue = isset( $_aryParams['compareValue'] ) ? $_aryParams['compareValue'] : null;
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$operator = isset( $_aryParams['operator'] ) ? $_aryParams['operator'] : '=';
		//boolean whether the comparison is strict (both value and type must be the same
		$strict = isset( $_aryParams['strict'] ) ? $_aryParams['strict'] : false;
		$message = "";
		
		if( $allowEmpty && self::isEmpty( $_val ) )
		{
			return true;
		}
		switch ( $operator )
		{
			case '=':				
			case '==':
				if( ($strict && $_val !== $compareValue) || (!$strict && $_val != $compareValue) )
				{
					$message = "must be equal to `{$compareValue}`";
				}
				break;
			case '!=':
				if(($strict && $_val===$compareValue) || (!$strict && $_val==$compareValue))
				{
					$message = "must not be equal to `{$compareValue}`";
				}
				break;
			case '>':
				if( $_val <= $compareValue )
				{
					$message = "must be greater than `{$compareValue}`";
				}
				break;
			case '>=':
				if( $_val < $compareValue )
				{
					$message = "must be greater than or equal to `{$compareValue}`";
				}
				break;
			case '<':
				if( $_val >= $compareValue )
				{
					$message = "must be less than `{$compareValue}`";
				}
				break;
			case '<=':
				if( $_val > $compareValue )
				{
					$message = "must be less than or equal to `{$compareValue}`";
				}
				break;
			default:
				throw new CException( "Invalid operator '{$operator}'" );
		}
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cEmail( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;		
		$pattern = "/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/";
		$message = "";
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if( !preg_match( $pattern , $_val ) )
		{
			$message = "is not a valid email address.";
		}
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cNumber( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$integerOnly = isset( $_aryParams['integerOnly'] ) ? $_aryParams['integerOnly'] : false;
		$max = isset( $_aryParams['max'] ) ? $_aryParams['max'] : null;
		$min = isset( $_aryParams['min'] ) ? $_aryParams['min'] : null;		
		$message = "";
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
			
		if($integerOnly)
		{
			if(!preg_match('/^\s*[+-]?\d+\s*$/',"$_val"))
			{
				$message=" must be an integer.";
			}
		}
		else
		{
			if(!preg_match('/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/',"$_val"))
			{
				$message=" must be a number.";
			}
		}
		if( $min!==null && $_val< $min)
		{
			$message=" is too small (minimum is {$min} ).";
		}
		if( $max !==null && $_val > $max )
		{
			$message=" is too big (maximum is {$max} ).";
		}
				
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cDatetime( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$format = isset( $_aryParams['format'] ) ? $_aryParams['format'] : "yyyy-MM-dd hh:mm:ss";
		$message = "";
		
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if( empty($format))
			throw new CException( 'The "format" property is not be defined.' );
			
		if( CDateTimeParser::parse( $_val , $format ) === false )
		{
			$message = " is must be `{$format}`";	
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cDate( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$format = isset( $_aryParams['format'] ) ? $_aryParams['format'] : "yyyy-MM-dd";
		$message = "";
		
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if( empty($format))
			throw new CException( 'The "format" property is not be defined.' );
			
		if( CDateTimeParser::parse( $_val , $format ) === false )
		{
			$message = " is must be `{$format}`";	
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cTime( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$format = isset( $_aryParams['format'] ) ? $_aryParams['format'] : "hh:mm:ss";
		$message = "";
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if( empty($format))
			throw new CException( 'The "format" property is not be defined.' );
		if( CDateTimeParser::parse( $_val , $format ) === false )
		{
			$message = " is must be `{$format}`";	
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cString( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;		
		$max = isset( $_aryParams['max'] ) ? $_aryParams['max'] : null;
		$min = isset( $_aryParams['min'] ) ? $_aryParams['min'] : null;
		$is = isset( $_aryParams['is'] ) ? $_aryParams['is'] : null;
		$encoding = isset( $_aryParams['encoding'] ) ? $_aryParams['encoding'] : false;
		$message = "";
		$length = strlen( $_val );
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if( $encoding!==false && function_exists('mb_strlen') )
			$length = mb_strlen( $_val , $encoding );
			
		if( $min!==null && $length< $min)
		{
			$message=" is too small (minimum is {$min} ).";
		}
		if( $max !==null && $length > $max )
		{
			$message=" is too big (maximum is {$max} ).";
		}
		if( $is !== null && $length !== $is )
		{
			$message=" is of the wrong length (should be {$is} characters)";
		}
				
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cArray( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$message = "";
		
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if( !is_array( $_val ) )
		{
			$message = " is must be an array";	
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cInteger( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$pattern = "/^[-+]?[0-9]+$/";
		$message = "";
		
		if( !preg_match( $pattern , trim($_val) ) )
		{
			$message = "is must be integer.";
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cFloat( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$pattern = "/^[-+]?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/";
		$message = "";
		
		if( !preg_match( $pattern , $_val ) )
		{
			$message = "is must be float.";
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cUrl( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$pattern = "/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i";
		$message = "";
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if( !preg_match( $pattern , $_val ) )
		{
			$message = "is not a valid URL.";
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public static function cRegular( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$allowEmpty = isset( $_aryParams['allowEmpty'] ) ? $_aryParams['allowEmpty'] : false;
		$pattern = isset( $_aryParams['pattern'] ) ? $_aryParams['pattern'] : null;
		$message = "";
		
		if( $allowEmpty && self::isEmpty( $_val ) )
			return true;
		
		if($pattern===null)
			throw new CException( 'The "pattern" property must be specified with a valid regular expression.' );
			
		if( !preg_match( $pattern , $_val ) )
		{
			$message = "is not valid.";
		}
		
		$_message = empty( $_message ) ? $message : $_message;
		return empty( $message ) ? true : false;
	}
	
	public function isEmpty( $_value,$_trim=false )
	{
		return $_value===null || $_value===array() || $_value==='' || $_trim && is_scalar($_value) && trim($_value)==='';
	}
	
	public function cCaptcha( $_val = null , $_aryParams = array() , &$_message = "" )
	{
		$objWidgetCaptchaRenderImage = new CWidgetCaptchaRenderImage();
		if( !$objWidgetCaptchaRenderImage->validate( $_val ) )
		{
			$_message = "不正确";
			return false;
		}
		return true;
	}
//end class
}