<?php

/**
 * Speed Controller
 * 
 * @author zhangyi
 * @date 2014-06-4
 */
class SpeedController extends BaseController
{
	public $_redis;
	
	/** 图表最多显示多少个点 **/
	public $_maxPoint = 100;
	
	/** 间隔时间 ( 按 分钟 计算  ) **/
	public $_waitTime = 2;
	
	/** 点时间 */
	public $_pointTime = 0;
	
	/** 当前时间 */
	public $_nowTime = 0;
	
	/** 运行模式 */
	public $_runModel = array(
							'L' => 'L',
							'B' => 'B'
	);
	
	public function init()
	{
		parent::init();
		$this -> _waitTime = $this -> _waitTime * 60 * 1000;
		$this -> _nowTime = strtotime(date( 'Y-m-d H:i' )) * 1000;
		$this -> _pointTime = $this -> _waitTime * intval( $this -> _nowTime / $this -> _waitTime );
	}

	/**
	 * 通过api获取矿机速度
	 * 
	 */
	public function actionIndex()
	{
		if( $this -> createSpeedData() === false )
			echo '500';
		echo '200';
		exit();
	}
	
	/**
	 * 创建数据并存储数据
	 * @throws CException
	 * @return boolean
	 * 
	 * @author zhangyi
	 * @date 2014-6-11
	 */
	public function createSpeedData( )
	{
		try
		{
			//判断速度文件是否存在
			$boolFileExists = true;
			$boolNeedSync = false;
			$objSpeedModel = SpeedModel::model();
			if( !file_exists( $objSpeedModel -> getFilePath() ) )
				$boolFileExists = false;
				
			//如果文件不存在,则刷入默认数据
			if( $boolFileExists === false )
			{
				$aryData = $this -> fillingNullData();
				$boolNeedSync = true;
			}
			//如果文件存在,则判断最后一次同步时间和当前所允许同步时间差距
			else
			{
				$aryData = $objSpeedModel -> getSpeedDataByFile();
		
				if( empty( $aryData ) )
					throw new CException( '' );
		
				$aryPointsDataB = $aryData['B'];
				$aryPointsDataL = $aryData['L'];
				
				//假如当前可同步时间 减去 最后一次同步时间 大于 间隔时间  则默认给中间空数据补白
				$intLastTime = end( array_keys( $aryPointsDataL ) );
				$pointTime = $this -> _pointTime;
		
				if( ($pointTime - $intLastTime) > $this -> _waitTime )
				{			
					$aryData = $this -> fillingNullData( $aryData );
					$boolNeedSync = true;
				}
				else
				{
					//判断是否需要同步数据
					if( $this -> _nowTime == $pointTime )
					{
						//由于此程序未发布到树莓派上,此处获取数据代码仅供测试
						$intSpeedSum = $objSpeedModel -> getSpeedSum();	
						$strRunModel = 'L';
						
						//正式获取数据代码
						//$arySpeedData = $objSpeedModel -> getSpeedDataByApi();
						//$strRunModel = $objSpeed -> getRunModel();
						
						//填充当前时间段数据
						$intSpeedB = $strRunModel === 'B' ? $intSpeedSum * 1024 : 0;
						$intSpeedL = $strRunModel === 'L' ? $intSpeedSum * 1024 : 0;
						
						$aryPointsDataB[$pointTime] = array( $pointTime , $intSpeedB );
						$aryPointsDataL[$pointTime] = array( $pointTime , $intSpeedL );
						
						//对空白数据进行补充
						unset( $aryData );
						$aryData = array(
								'B' => $aryPointsDataB,
								'L' => $aryPointsDataL
						);
						$aryData = $this -> fillingNullData( $aryData );
						$boolNeedSync = true;
					}
					else
					{
						return true;
					}
				}
			}
			
			
			//判断是否需要进行数据写入
			if( $boolNeedSync === true )
			{
				//进行数据写入
				if( $objSpeedModel -> storeSpeedData( $aryData ) === true )
					return true;
				else
					return false;
			}
			return true;
		}
		catch (CException $e)
		{
			return false;
		}
	}
	
	/**
	 * 补充空白数据
	 * @param unknown $_aryData
	 * 
	 * @author zhangyi
	 * @date 2014-6-11
	 */
	public function fillingNullData( $_aryData = array() )
	{
		$intMaxPoint = $this -> _maxPoint;
		$intNowTime = $this -> _nowTime;
		$intWaitTime = $this -> _waitTime;
		$intPointTime = $this -> _pointTime;
		
		$aryPointsDataL = empty( $_aryData ) ? array() : $_aryData['L'];
		$aryPointsDataB = empty( $_aryData ) ? array() : $_aryData['B'];
		$aryTempB = array();
		$aryTempL = array();
		
		//假如时间存在,则赋予原值
		for( $i = $intMaxPoint -1 ; $i >= 0 ; $i-- )
		{
			$point = $intPointTime - $intWaitTime * $i ;
			if( array_key_exists( ''.$point , $aryPointsDataL ) )
			{
				$aryTempB[$point] = $aryPointsDataB[$point];
				$aryTempL[$point] = $aryPointsDataL[$point];
			}
			else
			{
				$aryTempB[$point] = array( $point , 0 );
				$aryTempL[$point] = array( $point , 0 );
			}
		}
		unset( $_aryData , $aryPointsDataB , $aryPointsDataL , $intMaxPoint , $intNowTime , $intPointTime , $intWaitTime );
		$aryData = array(
					'B' => $aryTempB,
					'L' => $aryTempL
					);
		return $aryData;
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
			if( !empty( $aryData ) )
			{
				$temp['L'] = array_values($aryData['L']);
				$temp['B'] = array_values($aryData['B']);
				$aryData = $temp;
				unset( $temp );
				$isOk = 1;
				$msg = '获取数据成功';
			}
			else
			{
				if( $this -> createSpeedData() === true )
					$this -> actionSpeedData();
			}
		}
		
		echo $this -> encodeAjaxData( $isOk , $aryData , $msg );
		exit();
	}
		
	//end class
}
