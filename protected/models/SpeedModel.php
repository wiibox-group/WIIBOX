<?php
/**
 * 速度操作类
 *
 * @author wengebin
 * @date 2014-05-08
 */
class SpeedModel extends CModel
{
	/** Redis store hander **/
	private $_redis;

	/** 系统名 */
	public $_sys = '';
	
	/** 速度集合KEY **/
	private $_fileName = 'list.speed.log';

	/** 未同步数据KEY **/
	private $_noSyncDataFileName = 'list.no.sync.data';
	
	/** 本地数据存储 记录忽略次数的 key **/
	private $_ignoreKey= 'string.ignore.num';

	/** 图表最多显示多少个点 **/
	public $_maxPoint = 145;
	
	/** 间隔时间 ( 按 分钟 计算  ) **/
	public $_waitTime = 10;
	
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
		$this -> _waitTime = $this -> _waitTime * 60;
		$this -> _nowTime = strtotime(date( 'Y-m-d H:i' ));
		$this -> _pointTime = $this -> _waitTime * intval( $this -> _nowTime / $this -> _waitTime );
	}

	/**
	 * 返回惟一实例
	 *
	 * @return Model
	 */
	public static function model( $className = __CLASS__ )
	{
		return parent::model( __CLASS__ );
	}

	/**
	 * 获得未同步数据缓存名
	 *
	 * @return string
	 */
	public function getNoSyncFilePath()
	{
		return $this -> _noSyncDataFileName ;
	}

	/**
	 * 获得速度数据
	 *
	 * @return array
	 */
	public static function getSpeedDataByApi()
	{
		$aryApiData = SocketModel::request( 'devs' );

		$aryUsbData = array();
		$strMinerName = '';
		foreach ( $aryApiData as $key=>$data ) 
		{
			// 获得通讯协议名称
			if ( $key === 'STATUS' )
			{
				preg_match( '/.*\s(\w*)\(s\).*/' , $data['Msg'] , $matchs );
				$strMinerName = $matchs[1];

				continue;
			}

			if ( empty($strMinerName) )
				break; 

			/*
			// 获得系统名
			$sys = new CSys();
			// 系统全称
			$strSysInfo = $sys->cursys.'_'.SYS_INFO;

			// 如果属于没有统计Accept的系统，则将share作为Accept
			if ( $strSysInfo === 'OPENWRT_GS_S_V3' )
				$data['Accepted'] = intval( $data['Total MH'] );
			*/

			$aryUsb = $strMinerName.$data[$strMinerName];
			$aryUsbData[$aryUsb] = array( 'A'=>$data['Accepted'] , 
										'R'=>$data['Rejected'] , 
										'S'=>$data['MHS av'] , 
										'RUN'=>$data['Device Elapsed'],
										//'LAST'=>$data['Last Share Time']
										'LAST'=>$data['Last Valid Work']
									);

		}

		return $aryUsbData;
	}
	
	/**
	 * 根据文件获取数据
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 * 
	 */
	public function getSpeedDataByFile()
	{
		$aryData = $this -> getRedis() -> readByKey( $this -> _fileName );
		if( !empty( $aryData ) )
			return json_decode( $aryData , 1 );
		return array();
	}
	
	/**
	 * 将数据写入文件
	 * 
	 * @param array $_aryData 需要写入的数据
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 */
	public function storeSpeedData( $_aryData = array() )
	{
		if( empty( $_aryData ) )
			return false;
		return $this -> getRedis() -> writeByKey( $this -> _fileName , json_encode( $_aryData ) );
	}
	
	/**
	 * 获取文件路径
	 * @return string
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 */
	public function getFilePath()
	{
		$redis = $this -> getRedis();
		return $redis -> getFilePath( $this -> _fileName );
	}
	
	/**
	 * 获取redis
	 * 
	 * @return CRedisFile
	 * 
	 * @author zhangyi
	 * @date 2014-6-5
	 */
	public function getRedis()
	{
		if( empty( $this -> _redis ) )
			$this -> _redis = new CRedisFile();
				
		return $this -> _redis;
	}
	
	/**
	 * 获取控制器总算力
	 * 
	 * @return Ambigous <number, unknown>
	 * 
	 * @author zhangyi
	 * @date 2014-6-13
	 */
	public function getSpeedSum()
	{
		$arySpeedDatas = $this -> getSpeedDataByApi();	
		//获取总算力
		$intSpeedSum = 0;
		if( !empty( $arySpeedDatas ) )
		{
			foreach ( $arySpeedDatas as $arySpeedData )
			{
				$intSpeedSum += $arySpeedData['S'];
			}
		}
		
		return $intSpeedSum * 1024;
	}

	/**
	 * 创建供同步的数据
	 *
	 * @author zhangyi
	 * @date 2014-06-30
	 */
	public function createSyncSpeedData()
	{
		//准备基础数据
		$redis = $this -> getRedis();
		$aryData = array(
					'maxPoint' => $this -> _maxPoint,
					'nowTime' => $this -> _nowTime,
					'pointTime' => $this -> _pointTime,
					'waitTime' => $this -> _waitTime
				);

		$arySpeedData = array();
		//判断是否存在同步错误数据
		if( file_exists( $redis -> getFilePath( $this -> _noSyncDataFileName ) ) === true )
		{
			//将未同步数据进行取出
			$strSpeedData = $redis -> readByKey( $this -> _noSyncDataFileName);
			if( empty( $strSpeedData ) )
				return false;
			$arySpeedData = json_decode( $strSpeedData , 1 );
		}

		//获取当前算力速度以及运行模式数据
		$intSpeedSum = $this -> getSpeedSum();
		$strRunModel = RunModel::model() -> getRunMode();
		$intSpeedL = $strRunModel === 'L' ? $intSpeedSum : 0;
		$intSpeedB = $strRunModel === 'B' ? $intSpeedSum : 0;
		
		//将数据写入
		$arySpeedData['L'][''.$this -> _pointTime] = $intSpeedL;
		$arySpeedData['B'][''.$this -> _pointTime] = $intSpeedB;

		//判断当前总数已经超过了最大点数
		if( count( $arySpeedData['L'] ) > $this -> _maxPoint )
		{
			array_shift( $arySpeedData['L'] );
			array_shift( $arySpeedData['B'] );
		}
		$aryData['localSpeed'] = $arySpeedData;
		return $aryData;
	}


	/**
	 * 创建数据并存储数据
	 * @throws CException
	 * @return boolean
	 * 
	 * @author zhangyi
	 * @date 2014-6-11
	 */
	public function refreshSpeedData( )
	{
		$boolFlag = false;
		try
		{
			//创建数据
			$intSpeedSum = $this -> getSpeedSum();
			$strRunModel = RunModel::model() -> getRunMode();
			$intSpeedL = $strRunModel === 'L' ? $intSpeedSum : 0;
			$intSpeedB = $strRunModel === 'B' ? $intSpeedSum : 0;
			
			//如果文件不存在,则写入默认数据
			if( file_exists( $this -> getFilePath()) === false )
			{
				$aryData = array(
							'L' => array( ''.$this -> _pointTime => array( $this -> _pointTime , $intSpeedL )),
							'B' => array( ''.$this -> _pointTime => array( $this -> _pointTime , $intSpeedB ))
						);
				$aryData = $this -> changeNullData( $aryData ); 
			}
			//如果文件存在,则判断最后一次同步时间和当前所允许同步时间差距 
			else
			{
				//如果当前时间减去最后一次时间大于 点与点之间时间差距 则执行插入数据功能,否则替换最后一次数据
				$aryData = $this -> getSpeedDataByFile();
				$aryPointsDataL = $aryData['L'];
				$aryPointsDataB = $aryData['B'];
				$aryLastDataL = end($aryData['L']);
				$intLastTime = array_shift( $aryLastDataL );
				$pointTime = $this -> _pointTime;
				
				//如果当前可同步时间 与 最后一次同步时间中间相差两个点，则跳过三次
				if( $pointTime < $intLastTime || $pointTime - $intLastTime >= 2 * $this -> _waitTime )
				{
					//获取忽略的次数
					$intIgnoreNum = $this -> getIgnoreNum();
					
					//当且仅当等于 3 时程序才能执行
					if( $intIgnoreNum != 3 )
					{
						$intIgnoreNum = ( $intIgnoreNum > 3 || $intIgnoreNum < 0 ) ? 0 : ++$intIgnoreNum;
						
						//将数据进行存储
						if( $this -> storeIgnoreNum( $intIgnoreNum ) === false )
							return false;
						return false;
					}
					//如果等于3,则让程序正常执行
					else
					{
						if( $this -> delIgnoreNumFile() === false )
							return false;
					}
				}
				
				$aryPointsDataB[''.$pointTime] = array( $pointTime , $intSpeedB );
				$aryPointsDataL[''.$pointTime] = array( $pointTime , $intSpeedL );
						
				//对空白数据进行补充
				unset( $aryData );
				$aryData = array(
							'L' => $aryPointsDataL,
							'B' => $aryPointsDataB
						);
				$aryData = $this -> changeNullData( $aryData );
			}
			
			//进行数据写入
			$boolFlag = $this -> storeSpeedData( $aryData );
		}
		catch (CException $e)
		{
			$boolFlag = false;
		}
		return $boolFlag;
	}
	
	/**
	 * 补充空白数据
	 * @param array $_aryData
	 * 
	 * @author zhangyi
	 * @date 2014-6-11
	 */
	public function changeNullData( $_aryData = array() )
	{
		
		$aryPointsDataL = empty( $_aryData ) ? array() : $_aryData['L'];
		$aryPointsDataB = empty( $_aryData ) ? array() : $_aryData['B'];
		$aryTempB = array();
		$aryTempL = array();
		
		//假如时间存在,则赋予原值
		for( $i = $this -> _maxPoint -1 ; $i >= 0 ; $i-- )
		{
			$point = $this -> _pointTime - $this -> _waitTime * $i ;
			if( array_key_exists( ''.$point , $aryPointsDataL ) )
			{
				$aryTempL[''.$point] = $aryPointsDataL[''.$point];
				$aryTempB[''.$point] = $aryPointsDataB[''.$point];
			}
			else
			{
				$aryTempL[''.$point] = array( $point , 0 );
				$aryTempB[''.$point] = array( $point , 0 );
			}
		}
		$aryData = array(
				'L' => $aryTempL,
				'B' => $aryTempB
				);
		
		unset( $_aryData , $aryPointsDataB , $aryPointsDataL , $aryTempB , $aryTempL);
		return $aryData;
	}
	
	/**
	 * 获取 忽略 的次数
	 *
	 * @author zhangyi
	 * @date 2014-07-08
	 */
	public function getIgnoreNum()
	{	
		$redis = $this -> getRedis();
		if( file_exists( $redis -> getFilePath( $this -> _ignoreKey ) ) === true )
		{
			$strNum = $redis -> readByKey( $this -> _ignoreKey );
			if( empty( $strNum ) )
				return 0;
			else
				return intval( $strNum );
		}
		else
			return 0;
	}
	
	/**
	 * 存储 忽略 的次数
	 *
	 * @param int $_intNum 数值
	 *
	 * @author zhangyi 
	 * @date 2014-07-08
	 */
	public function storeIgnoreNum( $_intNum = 0 )
	{
		if( !is_int( $_intNum ) )
			return false;
		return $this -> getRedis() -> writeByKey( $this -> _ignoreKey , ''.$_intNum );
	}
	
	/**
	 *
	 * 删除 记录忽略次数  的文件
	 *
	 * @author zhangyi 
	 * @date 2014-07-08
	 */
	public function delIgnoreNumFile()
	{
		$redis = $this -> getRedis();
		return $redis -> deleteByKey( $this -> _ignoreKey );
	}

	
//end class
}
