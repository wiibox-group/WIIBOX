<?php
class EWidgetSessionTipMsg extends CWidget 
{
	public function run()
	{
		//success msg
		$msg = UtilMsg::getTipFromSession();
		if( !empty( $msg ) )
		{
			echo "<div class=\"alert alert-success\">{$msg}</div>";
		}
		//error msg
		$msg = UtilMsg::getErrorTipFromSession();
		if( !empty( $msg ) )
		{
			echo "<div class=\"alert alert-danger\">{$msg}</div>";
		}
		return;
	}
	
}
