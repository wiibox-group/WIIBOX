<?php
/**
 * 验证码部件
 *
 */
class CWidgetCaptcha extends CWidget
{
	public $imageOptions = array();
	
	/**
	 * 初始化
	 *
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 运行
	 *
	 */
	public function run()
	{
		/**
		 * 检测是否包含渲染验证码所必备的环境
		 */
		if( self::checkRequirements('imagick') || self::checkRequirements('gd') )
		{
			$this->renderImage();
			if ( $this->imageOptions['changebt'] !== false ) $this->renderChangeImageLink();
		}
		else
			throw new CException( "GD with FreeType or ImageMagick PHP extensions are required." );
	}
	
	/**
	 * 输出渲染图片
	 *
	 */
	public function renderImage()
	{
		if(!isset($this->imageOptions['id']))
			$this->imageOptions['id']='jqCaptcha';

		$url = Nbt::app()->createUrl( 'captcha/index' , array('random'=>rand(10000,99999)) );
		$alt=isset($this->imageOptions['alt'])?$this->imageOptions['alt']:'';
		echo CHtml::image($url,$alt,$this->imageOptions);
	}
	
	/**
	 * 输出更换验证码的链接
	 *
	 */
	public function renderChangeImageLink()
	{
		echo '<span id="jqChangeCaptcha">看不请?换一张</span>';
		$url = Nbt::app()->createUrl( 'captcha/index' );
		echo '<script>$(function(){ $("#jqChangeCaptcha").click(function(){ $("#jqCaptcha").attr("src","'.$url.(REWRITE_MODE===true?'?':'&').'random="+Math.random()); }); });</script>';
	}
	
	/**
	 * 检测渲染图片所必需的环境
	 *
	 * @param string $extension
	 * @return bool
	 */
	public static function checkRequirements($extension=null)
	{
		if(extension_loaded('imagick'))
		{
			$imagick=new Imagick();
			$imagickFormats=$imagick->queryFormats('PNG');
		}
		if(extension_loaded('gd'))
		{
			$gdInfo=gd_info();
		}
		if($extension===null)
		{
			if(isset($imagickFormats) && in_array('PNG',$imagickFormats))
				return true;
			if(isset($gdInfo) && $gdInfo['FreeType Support'])
				return true;
		}
		elseif($extension=='imagick' && isset($imagickFormats) && in_array('PNG',$imagickFormats))
			return true;
		elseif($extension=='gd' && isset($gdInfo) && $gdInfo['FreeType Support'])
			return true;
		return false;
	}
	
//end class	
}
