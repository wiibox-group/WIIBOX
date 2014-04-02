<?php echo "\n";?>
*****************************ERROR*************************************************
<?php if( NBT_DEBUG ):?>
	<?php echo "\nPHP Error[{$code}]";?>
	<?php echo "\n{$message}({$file}:{$line})";?>
	<?php echo "\n".debug_print_backtrace();?>
<?php else:?>	
	<?php echo "\nSystem Error";?>
<?php endif;?>
****************************************************************************************