<?php

/**
 * Speed Controller
 * 
 * @author zhangyi
 * @date 2014-06-4
 */
class SpeedController extends BaseController
{

	/**
	 * 通过api获取矿机速度
	 * 
	 */
	public function actionIndex()
	{
		
		if( SpeedModel::model() -> refreshSpeedData() === false )
			echo '500';
		echo '200';
		exit();
	}
	
	/**
	 * 获取矿机数据
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 */
	public function actionSpeedData()
	{
		$isOk = 0;
		$msg = '获取数据失败';
		$aryData = array();
		$objSpeedModel = SpeedModel::model();
		if( Nbt::app() -> request -> isAjaxRequest )
		{
			$aryData = $objSpeedModel -> getSpeedDataByFile();
			$temp['L'] = array_values($aryData['L']);
			$temp['B'] = array_values($aryData['B']);
			$aryData = $temp;
			unset( $temp );
			$isOk = 1;
			$msg = '获取数据成功';
		}
		echo $this -> encodeAjaxData( $isOk , $aryData , $msg );
		exit();
	}
		
	//end class
}
