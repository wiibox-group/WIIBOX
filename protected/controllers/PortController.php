<?php
/**
 * Port Controller
 * 
 * @author wengebin
 * @date 2013-12-31
 */
class PortController extends BaseController
{
	/**
	 * init
	 */
	public function init()
	{
		parent::init();		
	}
	
	/**
	 * Index method
	 */
	public function actionIndex()
	{
		exit();
	}

	/**
	 * Check method
	 */
	public function actionCheck()
	{
		$this->layout = "blank";
		// get target check address
		$strTarAdd = isset( $_REQUEST['tar'] ) ? htmlspecialchars( $_REQUEST['tar'] ) : '';

		// get local address
		$strLocalAdd = $_SERVER['SERVER_ADDR'];

		$isok = 0;
		$data = array();
		$msg = "";

		try
		{
			if ( empty( $strTarAdd ) || $strTarAdd !== $strLocalAdd )
			{
				$data = '500';
				throw new CModelException( '我不是你要找的矿机！' );
			}
			else
			{
				$data['ip'] = $strLocalAdd;
				$data['key'] = KEY;
				$msg = '我就是矿机！';
			}
			$isok = 1;
		}
		catch ( CModelException $e )
		{
			$msg = $e->getMessage();
		}
		catch ( CException $e )
		{
			$msg = NBT_DEBUG ? $e->getMessage() : '系统错误';
		}

		$data = $this->encodeAjaxData( $isok , $data , $msg );
		$this->render( 'index' , array('data'=>$data) );
	}

	/**
	 * Generate key for machine
	 */
	public function actionGeneratekey( $_boolIsNoExist = false )
	{
		$os = DIRECTORY_SEPARATOR=='\\' ? "windows" : "linux";
		$mac_addr = new CMac( $os );
		$ip_addr = new CIp( $os );

		if ( file_exists( WEB_ROOT.'/js/RKEY.TXT' ) )
		{
			$strRKEY = file_get_contents( WEB_ROOT.'/js/RKEY.TXT' );
		}

		if ( !isset( $strRKEY ) || empty( $strRKEY ) )
		{
			$this->generateRKEY();
			$strRKEY = file_get_contents( WEB_ROOT.'/js/RKEY.TXT' );
		}

		$key_file = fopen( WEB_ROOT.'/js/showport.js' , 'w' );
		fwrite($key_file, 'add_machine(\''.md5($mac_addr->mac_addr.'-'.$strRKEY).'\',\''.$ip_addr->ip_addr.'\');');
		fclose($key_file);

		if ( $_boolIsNoExist === true )
			return true;
		else
		{
			echo '200';
			exit();
		}
	}

	/**
	 * Cancel bind
	 */
	public function actionCancelbind()
	{
		// Get key
		$os = DIRECTORY_SEPARATOR=='\\' ? "windows" : "linux";
		$mac_addr = new CMac( $os );

		$strRKEY = '';
		if ( file_exists( WEB_ROOT.'/js/RKEY.TXT' ) )
			$strRKEY = file_get_contents( WEB_ROOT.'/js/RKEY.TXT' );

		$boolResult = $this->generateRKEY();
		if ( $boolResult === true )
			$boolResult = $this->actionGeneratekey( true );

		// send cancel bind request
		UtilApi::callCancelbind( md5($mac_addr->mac_addr.'-'.$strRKEY) );

		if ( $boolResult === true )
			UtilMsg::saveTipToSession( '取消绑定成功，请重新扫描绑定！' );
		else
			UtilMsg::saveErrorTipToSession( '取消绑定失败，再试试！' );

		$this->redirect( array( 'index/index' ) );
	}

	/**
	 * Generate random key
	 */
	public function generateRKEY()
	{
		srand((double)microtime()*1000000);

		$file = fopen( WEB_ROOT.'/js/RKEY.TXT' , 'w' );
		fwrite($file, rand());
		fclose($file);

		return true;
	}

	/**
	 * Change mac address once
	 */
	public function actionGeneratemac()
	{
		$configFile = '/etc/config/network';
		$checkFile = WEB_ROOT.'/cache/gmac.d';
		if ( file_exists( $checkFile ) )
		{
			echo '500';
			exit;
		}
		
		// is need reboot
		$boolIsReboot = true;
		if ( file_exists( $configFile ) )
		{
			$content = file_get_contents( $configFile );

			$old_mac = '';
			if ( preg_match( "/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $content, $temp_array ) )
				$old_mac = $temp_array[0];

			if ( empty( $old_mac ) )
			{
				echo '500';
				exit;
			}

			$strTmp = '1234567890abcdef';
			$mac_str_1_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_1_p2 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_2_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_2_p2 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_3_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_3_p2 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_4_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_4_p2 = $strTmp{rand(0, strlen($strTmp)-1)};

			$mac_str_1 = $mac_str_1_p1.$mac_str_1_p2;
			$mac_str_2 = $mac_str_2_p1.$mac_str_2_p2;
			$mac_str_3 = $mac_str_3_p1.$mac_str_3_p2;
			$mac_str_4 = $mac_str_4_p1.$mac_str_4_p2;

			$aryMacData = explode( ':' , $old_mac );
			$aryMacData[count( $aryMacData )-4] = $mac_str_1;
			$aryMacData[count( $aryMacData )-3] = $mac_str_2;
			$aryMacData[count( $aryMacData )-2] = $mac_str_3;
			$aryMacData[count( $aryMacData )-1] = $mac_str_4;

			$new_mac = implode( ':' , $aryMacData );
			$storeContent = str_replace( $old_mac , $new_mac , $content );

			$conf = fopen( $configFile , 'w' );
			fwrite( $conf , $storeContent );
			fclose( $conf );
		}
		else
		{
			$os = DIRECTORY_SEPARATOR=='\\' ? "windows" : "linux";
			$mac_addr = new CMac( $os );
			$new_mac = $mac_addr->mac_addr;

			$boolIsReboot = false;
		}
		
		$macf = fopen( $checkFile , 'w' );
		fwrite( $macf , $new_mac );
		fclose( $macf );

		// need reboot
		if ( $boolIsReboot === true )
			exec(SUDO_COMMAND.'reboot');

		exit;
	}

//end class
}
