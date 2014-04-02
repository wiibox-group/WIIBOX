<?php
class CWidgetCaptchaRenderImage
{
	/**
	 * The name of the GET parameter indicating whether the CAPTCHA image should be regenerated.
	 */
	const REFRESH_GET_VAR='refresh';
	/**
	 * Prefix to the session variable name used by the action.
	 */
	const SESSION_VAR_PREFIX='qinxue.';
	/**
	 * @var integer how many times should the same CAPTCHA be displayed. Defaults to 3.
	 * A value less than or equal to 0 means the test is unlimited (available since version 1.1.2).
	 */
	public $testLimit = 3;
	/**
	 * @var integer the width of the generated CAPTCHA image. Defaults to 120.
	 */
	public $width = 120;
	/**
	 * @var integer the height of the generated CAPTCHA image. Defaults to 50.
	 */
	public $height = 50;
	/**
	 * @var integer padding around the text. Defaults to 2.
	 */
	public $padding = 2;
	/**
	 * @var integer the background color. For example, 0x55FF00.
	 * Defaults to 0xFFFFFF, meaning white color.
	 */
	public $backColor = 0xFFFFFF;
	/**
	 * @var integer the font color. For example, 0x55FF00. Defaults to 0x2040A0 (blue color).
	 */
	public $foreColor = 0x2040A0;
	/**
	 * @var boolean whether to use transparent background. Defaults to false.
	 */
	public $transparent = false;
	/**
	 * @var integer the minimum length for randomly generated word. Defaults to 6.
	 */
	public $minLength = 6;
	/**
	 * @var integer the maximum length for randomly generated word. Defaults to 7.
	 */
	public $maxLength = 6;
	/**
	 * @var integer the offset between characters. Defaults to -2. You can adjust this property
	 * in order to decrease or increase the readability of the captcha.
	 * @since 1.1.7
	 **/
	public $offset = -2;
	/**
	 * @var string the TrueType font file. Defaults to SpicyRice.ttf which is provided with the Yii release.
	 * Note that non-free Duality.ttf has been changed to open/free SpicyRice.ttf since 1.1.14.
	 */
	public $fontFile;
	/**
	 * @var string the fixed verification code. When this is property is set,
	 * {@link getVerifyCode} will always return this value.
	 * This is mainly used in automated tests where we want to be able to reproduce
	 * the same verification code each time we run the tests.
	 * Defaults to null, meaning the verification code will be randomly generated.
	 * @since 1.1.4
	 */
	public $fixedVerifyCode;
	/**
	 * @var string the graphic extension that will be used to draw CAPTCHA image. Possible values
	 * are 'gd', 'imagick' and null. Null value means that fallback mode will be used: ImageMagick
	 * is preferred over GD. Default value is null.
	 * @since 1.1.13
	 */
	public $backend;

	/**
	 * Runs the action.
	 */
	public function run()
	{
		$this->renderImage($this->getVerifyCode());
	}
	
	/**
	 * Gets the verification code.
	 * @param boolean $regenerate whether the verification code should be regenerated.
	 * @return string the verification code.
	 */
	public function getVerifyCode($regenerate=false)
	{
		if($this->fixedVerifyCode !== null)
			return $this->fixedVerifyCode;

		$session = Nbt::app()->session;
		$session->open();
		$name = $this->getSessionKey();
		$session->set($name , $this->generateVerifyCode() );
		return $session->get($name);
	}

	/**
	 * Validates the input to see if it matches the generated code.
	 * @param string $input user input
	 * @return boolean whether the input is valid
	 */
	public function validate($input)
	{
		$name = $this->getSessionKey();
		$session = Nbt::app()->session;
		$session->open();
		$code = $session->get($name);
		return $input === $code ? true : false;
	}

	/**
	 * Generates a new verification code.
	 * @return string the generated verification code
	 */
	protected function generateVerifyCode()
	{
		if($this->minLength > $this->maxLength)
			$this->maxLength = $this->minLength;
		if($this->minLength < 3)
			$this->minLength = 3;
		if($this->maxLength > 20)
			$this->maxLength = 20;
		$length = mt_rand($this->minLength,$this->maxLength);

		$letters = 'bcdfghjklmnpqrstvwxyz';
		$vowels = 'aeiou';
		$code = '';
		for($i = 0; $i < $length; ++$i)
		{
			if($i % 2 && mt_rand(0,10) > 2 || !($i % 2) && mt_rand(0,10) > 9)
				$code.=$vowels[mt_rand(0,4)];
			else
				$code.=$letters[mt_rand(0,20)];
		}

		return $code;
	}

	/**
	 * Returns the session variable name used to store verification code.
	 * @return string the session variable name
	 */
	protected function getSessionKey()
	{
		return self::SESSION_VAR_PREFIX.'captcha';
	}

	/**
	 * Renders the CAPTCHA image based on the code using library specified in the {@link $backend} property.
	 * @param string $code the verification code
	 */
	protected function renderImage($code)
	{
		if($this->backend===null && CWidgetCaptcha::checkRequirements('imagick') || $this->backend==='imagick')
			$this->renderImageImagick($code);
		else if($this->backend===null && CWidgetCaptcha::checkRequirements('gd') || $this->backend==='gd')
			$this->renderImageGD($code);
	}

	/**
	 * Renders the CAPTCHA image based on the code using GD library.
	 * @param string $code the verification code
	 * @since 1.1.13
	 */
	protected function renderImageGD($code)
	{
		$image = imagecreatetruecolor($this->width,$this->height);

		$backColor = imagecolorallocate($image,
				(int)($this->backColor % 0x1000000 / 0x10000),
				(int)($this->backColor % 0x10000 / 0x100),
				$this->backColor % 0x100);
		imagefilledrectangle($image,0,0,$this->width,$this->height,$backColor);
		imagecolordeallocate($image,$backColor);

		if($this->transparent)
			imagecolortransparent($image,$backColor);

		$foreColor = imagecolorallocate($image,
				(int)($this->foreColor % 0x1000000 / 0x10000),
				(int)($this->foreColor % 0x10000 / 0x100),
				$this->foreColor % 0x100);

		if($this->fontFile === null)
			$this->fontFile = NBT_APPLICATION_CONFIG_PATH . DIRECTORY_SEPARATOR.'SpicyRice.ttf';

		$length = strlen($code);
		$box = imagettfbbox(30,0,$this->fontFile,$code);
		$w = $box[4] - $box[0] + $this->offset * ($length - 1);
		$h = $box[1] - $box[5];
		$scale = min(($this->width - $this->padding * 2) / $w,($this->height - $this->padding * 2) / $h);
		$x = 10;
		$y = round($this->height * 27 / 40);
		for($i = 0; $i < $length; ++$i)
		{
			$fontSize = (int)(rand(26,32) * $scale * 0.8);
			$angle = rand(-10,10);
			$letter = $code[$i];
			$box = imagettftext($image,$fontSize,$angle,$x,$y,$foreColor,$this->fontFile,$letter);
			$x = $box[2] + $this->offset;
		}

		imagecolordeallocate($image,$foreColor);

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Transfer-Encoding: binary');
		header("Content-Type: image/png");
		imagepng($image);
		imagedestroy($image);
	}

	/**
	 * Renders the CAPTCHA image based on the code using ImageMagick library.
	 * @param string $code the verification code
	 * @since 1.1.13
	 */
	protected function renderImageImagick($code)
	{
		$backColor=$this->transparent ? new ImagickPixel('transparent') : new ImagickPixel(sprintf('#%06x',$this->backColor));
		$foreColor=new ImagickPixel(sprintf('#%06x',$this->foreColor));

		$image=new Imagick();
		$image->newImage($this->width,$this->height,$backColor);

		if($this->fontFile===null)
			$this->fontFile = NBT_APPLICATION_CONFIG_PATH . DIRECTORY_SEPARATOR.'SpicyRice.ttf';

		$draw=new ImagickDraw();
		$draw->setFont($this->fontFile);
		$draw->setFontSize(30);
		$fontMetrics=$image->queryFontMetrics($draw,$code);

		$length=strlen($code);
		$w=(int)($fontMetrics['textWidth'])-8+$this->offset*($length-1);
		$h=(int)($fontMetrics['textHeight'])-8;
		$scale=min(($this->width-$this->padding*2)/$w,($this->height-$this->padding*2)/$h);
		$x=10;
		$y=round($this->height*27/40);
		for($i=0; $i<$length; ++$i)
		{
			$draw=new ImagickDraw();
			$draw->setFont($this->fontFile);
			$draw->setFontSize((int)(rand(26,32)*$scale*0.8));
			$draw->setFillColor($foreColor);
			$image->annotateImage($draw,$x,$y,rand(-10,10),$code[$i]);
			$fontMetrics=$image->queryFontMetrics($draw,$code[$i]);
			$x+=(int)($fontMetrics['textWidth'])+$this->offset;
		}

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Transfer-Encoding: binary');
		header("Content-Type: image/png");
		$image->setImageFormat('png');
		echo $image;
	}
}