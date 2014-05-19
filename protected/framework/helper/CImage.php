<?php
/**
 * 图片路径
 *
 */
class CImage
{
	/**
	 * 教师图片路径
	 *
	 */
	public function teacherUrl( $_intTeacherId = 0 )
	{
		//return IMAGE_DOMAIN.'/index.php?teid={$_intTeacherId}';
		return IMAGE_DOMAIN."/teacher/{$_intTeacherId}/head-{$_intTeacherId}.jpg";
	}
	/**
	 * 用户图像路径
	 * @param $_userId int,$_imgType string
	 * @return string
	 * @author zhaojingyun
	 */
	public function userHeadImgUrl($_userId,$_imgType){
		$imgDir = ceil($_userId/1000);
		return IMAGE_DOMAIN."/member/{$imgDir}/u{$_userId}-{$_imgType}.gif";
	}
	
	/**
	 * 教程封面图片路径
	 * 
	 * @param int $_intTutorialsId 教程ID，string $_strType 图片类型
	 * @return string
	 * @author zhaojingyun
	 */
	public function tutorialsUrl( $_intTutorialsId = 0 ,$_strType = ""){
		$imgDir = floor($_intTutorialsId/500);
		return empty($_strType)?IMAGE_DOMAIN."/tutorials/{$imgDir}/{$_intTutorialsId}-big.jpg":IMAGE_DOMAIN."/tutorials/{$imgDir}/{$_intTutorialsId}-{$_strType}.jpg";
	}
	/**
	 * 身份证图片路径
	 */
	public function teacherIdCardUrl( $_intTeacherId = 0 , $_intIdCardType = 0 )
	{
		//return IMAGE_DOMAIN.'/index.php?teid={$_intTeacherId}';
		return IMAGE_DOMAIN."/teacher/{$_intTeacherId}/idcard-{$_intTeacherId}-{$_intIdCardType}.jpg";
	}
	
	/**
	 * 教程类型图片路径
	 */
	public function tuCatImgUrl( $_intCatId ){
		return IMAGE_DOMAIN."/tuc/tuc_{$_intCatId}.jpg";
	}
//end class
}