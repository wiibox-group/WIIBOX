<?php
class UtilMsg
{
	
	/**
	 * 将提示信息保存到session中
	 *
	 * @param string $_strMsg
	 */
	public static function saveTipToSession( $_strMsg )
	{
		Nbt::app()->session->set( '_atip' , $_strMsg );
	}
	
	public static function getTipFromSession()
	{
		//删除并返当获取到的session值
		return Nbt::app()->session->remove( '_atip' );
	}
	
	/**
	 * 将错误提示信息保存到session中
	 *
	 * @param string $_strMsg
	 */
	public static function saveErrorTipToSession( $_strMsg )
	{
		Nbt::app()->session->set( '_atip_error' , $_strMsg );
	}
	
	/**
	 * 获取错误的提示信息
	 *
	 * @return string $_strMsg
	 */
	public static function getErrorTipFromSession()
	{
		//删除并返当获取到的session值
		return Nbt::app()->session->remove( '_atip_error' );
	}
//end class	
}
