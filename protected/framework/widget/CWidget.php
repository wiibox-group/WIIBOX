<?php
/**
 * 基类部件
 * 
 * @auth zhouyang 2013-09-06
 */
abstract class CWidget extends CComponents
{
	/**
	 * 初始化
	 *
	 */
	public function init()
	{
		
	}
	
	/**
	 * 执行
	 */
	public function run()
	{
		
	}
	
	/**
	 * display view.
	 * 
	 * @param string		$_viewFile_
	 * @param array|string	$_data_
	 * @param bool			$_return_
	 * @return string
	 */
	public function render($_viewFile_,$_data_=null)
	{
		// we use special variable names here to avoid conflict when extracting data
		if(is_array($_data_))
			extract($_data_,EXTR_PREFIX_SAME,'data');
		else
			$data=$_data_;
		
		$_viewFile_1= NBT_PATH."/widget/views/{$_viewFile_}.php";
		$_viewFile_2= NBT_APPLICATION_PATH."/libs/widgets/views/{$_viewFile_}.php";
		if( file_exists( $_viewFile_1 ) || file_exists( $_viewFile_2 ) )
		{
			if( file_exists( $_viewFile_1 ) )
				require( $_viewFile_1 );
			if( file_exists( $_viewFile_2 ) )
				require( $_viewFile_2 );
		}
		else
			throw new CException( "找不到部件视图:{$_viewFile_}" );
	}
	
//end class	
}