<?php
/**
 * Upgrade Controller
 * 
 * @author wengebin
 * @date 2013-12-29
 */
class UpgradeController extends BaseController
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
		$this->replaceSeoTitle( CUtil::i18n( 'controllers,upgrade_index_seoTitle' ) );

		$aryData = array();
		$this->render( 'index' , $aryData );
	}

	/**
	 * Check is has new version method
	 */
	public function actionHasnew( $_boolIsExit = false )
	{
		// check version
		$aryVersionData = UtilApi::callCheckNewVersion( CUR_VERSION );
		
		if ( $_boolIsExit === false )
		{
			header('Content-Type: text/html; charset=utf-8');
			echo json_encode( $aryVersionData );
			exit();
		}
		else
		{
			return $aryVersionData;
		}
	}

	/**
	 * Upgrade version method
	 */
	public function actionUpgradeversion()
	{
		// check is newest
		$aryVersionData = $this->actionHasnew( true );

		$isok = 0;
		$data = array();
		$msg = "";

		try
		{
			if ( $aryVersionData['ISOK'] !== 1 || empty( $aryVersionData['DATA']['v'] ) )
				throw new CModelException( CUtil::i18n( 'exception,version_upgrad_withoutUpgrad' ) );

			// get up to version
			$strVersion = $aryVersionData['DATA']['v'];

			if ( empty( $strVersion ) )
				throw new CModelException( CUtil::i18n( 'exception,version_upgrad_upgradFaild' ) );

			if ( $strVersion <= CUR_VERSION )
				throw new CModelException( CUtil::i18n( 'exception,version_upgrad_withoutUpgrad' ) );

			// execute upgrade
			$command = SUDO_COMMAND."cd ".WEB_ROOT.";".SUDO_COMMAND."wget ".MAIN_DOMAIN."/down/v{$strVersion}.zip;".SUDO_COMMAND."unzip -o v{$strVersion}.zip;".SUDO_COMMAND."rm -rf v{$strVersion}.zip;";
			exec( $command );
			
			$isok = 1;
		}
		catch ( CModelException $e )
		{
			$msg = $e->getMessage();
		}
		catch ( CException $e )
		{
			$msg = NBT_DEBUG ? $e->getMessage() : CUtil::i18n( 'exception,sys_error' );
		}

		header('Content-Type: text/html; charset=utf-8');
		echo $this->encodeAjaxData( $isok , $data , $msg );
		exit();
	}

//end class
}
