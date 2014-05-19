<?php
	
	return array(
		//framework-db error
		'010001'=>'db host',
		'010002'=>'db username',
		'010003'=>'db password',
		'010004'=>'db database name',
		'010005'=>'db chargset',
		'010006'=>'db connection error',
		//framework-db-CDbCriteria error		
		'010101'=>'CDbCriteria->select',
		'010102'=>'CDbCriteria->from',		
		'010106'=>'CDbCriteria->offset',
		//framework-cmode error
		'020001'=>'CModel->setData() is not set.',
		//framework-validator error
		'030001'=>'CValidatator->cCompare() undefined compareValue.',
		
		
		//business error
		'040001'=>'Channel->runDatafeed() Undefined function _runDatafeed.',
		'040002'=>'Channel->uploadDatafeed() Undefined function _uploadDatafeed.',
		'040003'=>'Channel->datafeedField() Undefined function datafeedField.',
	);
?>