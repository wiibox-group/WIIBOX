<?php echo "\n";?>
*****************************EXCEPTION*************************************************
<?php if( NBT_DEBUG ):?>
	<?php echo "\n".$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().')'."\n";?>
	<?php echo "\n".$exception->getTraceAsString()."\n";?>
<?php else:?>	
	<?php echo "\n".($exception instanceof CModelException ? $exception->getMessage() : CUtil::i18n('exception,havaWrong'))."\n";?>
<?php endif;?>
****************************************************************************************

