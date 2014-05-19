<?php
/**
 * 空白模型
 * 
 * @author wengebin
 * @date 2013-11-08
 */
class BlankModel extends CModel
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 返回惟一实例
	 *
	 * @return Model
	 */
	public static function model()
	{
		return parent::model( __CLASS__ );
	}
	
	/**
	 * 空白方法
	 *
	 * @return bool
	 */
	public function blankMethod()
	{
		// code here...
	}
	
//end class
}
