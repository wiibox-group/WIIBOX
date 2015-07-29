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
		$strMode = '';
		// If Sfards 3301 chip
		if ( strpos( SYS_INFO , 'SF3301' ) === 0 )
		{
			$strMode = 'sf';
		}

		if ( SpeedModel::model() -> refreshSpeedData( $strMode ) === false )
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
		$msg = '';
		$aryData = array();
		try
		{
			$objSpeedModel = SpeedModel::model();
			/*
			if( Nbt::app() -> request -> isAjaxRequest )
			{
			*/
				$aryData = $objSpeedModel -> getSpeedDataByFile();

				$strMode = '';
				// If Sfards 3301 chip
				if ( strpos( SYS_INFO , 'SF3301' ) === 0 )
				{
					$strMode = 'sf';
				}

				//如果数据不存在，则刷新一次数据
				if ( empty( $aryData ) && $objSpeedModel -> refreshSpeedData($strMode) === true )
					$aryData = $objSpeedModel -> getSpeedDataByFile();
				
				//如果数据依然不存在，则抛出系统异常错误
				if( !empty ( $aryData ) )
				{	
					$aryTemp['L'] = array_values($aryData['L']);
					$aryTemp['B'] = array_values($aryData['B']);
					$aryData = $aryTemp;
					unset( $aryTemp );
					$isOk = 1;
				}
				else
				{
					throw new CModelException( CUtil::i18n('exception,sys_error') );
				}
			/*
			}
			else
			{
				throw new CModelException( CUtil::i18n('exception,sys_error') );
			}
			*/
		}
		catch( CModelException $e )
		{
			$msg = $e -> getMessage();
		}
		echo $this -> encodeAjaxData( $isOk , $aryData , $msg );
		exit();
	}
		
	//end class
}
