<?php

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "cImageEditor.php");

class cNetPBMImageEditor extends cImageEditor
{
	function cNetPBMImageEditor( $sFileName = "" )
	{
		die('cNetPBMImageEditor NOT IMPLEMENTED!');
		$this->cImageEditor( $sFileName );
	}

	function getImageLoaderHandler( $sFileName )
	{
		//return 'imagick_readimage';
	}

	function getImageSaverHandler( $sFileName )
	{
		//return 'imagick_writeimage';
	}

	function loadImageFromFile( $sFileName = "" )
	{
//		$bSuccess = (bool)parent::loadImageFromFile( $sFileName );
//		if ($bSuccess) {
//			$this->aImageProperties[ "width" ] = imagick_getwidth($this->rImage);
//			$this->aImageProperties[ "height" ] = imagick_getheight($this->rImage);
//			$this->aImageProperties[ "bits" ] = imagick_getimagedepth($this->rImage);
//		}
//		
//		return $bSuccess;
	}
	
	/**
	 * Sets a new width attribute to the object.
	 *
	 * @param int $nWidth
	 * @access private
	 * @return bool
	 */
	function setWidth($nWidth)
	{
		$this->aImageProperties["width"] = $nWidth;
		return true;
	}

	/**
	 * Sets a new height attribute to the object.
	 *
	 * @param int $nHeight
	 * @access private
	 * @return bool
	 */
	function setHeight($nHeight)
	{
		$this->aImageProperties["height"] = $nHeight;
		return true;
	}

	function getWidth()
	{
		return $this->aImageProperties[ "width" ];
	}
	
	function getHeight()
	{
		return $this->aImageProperties[ "height" ];
	}

	function getBitDepth()
	{
		return $this->aImageProperties[ "bits" ];
	}

	/**
	 * Resizes an image to given dimensions.
	 *
	 * @access public
	 * @param int $nWidth
	 * @param int $nHeight
	 * @param bool $bKeepAspectRatio
	 * @return bool
	 */
	function resize( $nWidth, $nHeight, $bKeepAspectRatio = false )
	{
//		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) )
//		{
//			$bSuccess = $this->loadImageFromFile();
//			
//			if ($bSuccess === false) {
//				// No file loaded!!!
//				return false;
//			}
//		}
//		
//		# Take care of the aspect ratio
//		if ( $bKeepAspectRatio )
//		{
//			if ( $this->getHeight() / $nHeight >= $this->getWidth() / $nWidth )
//			{
//				// Height is leading
//				$nWidth = round( $this->getWidth() / ( $this->getHeight() / $nHeight) );
//			}
//			else
//			{
//				// Width is leading
//				$nHeight = round( $this->getHeight() / ( $this->getWidth() / $nWidth) );
//			}
//		}
//		
//		# Resize/resample
//		
//		$bSuccess = imagick_resize ( $this->rImage, $nWidth, $nHeight, 0, 1 );
//		
//		if ( $bSuccess )
//		{
//			$this->setWidth( $nWidth );
//			$this->setHeight( $nHeight );
//		}			
//		
//		return $bSuccess;
	}

	/**
	 * Rotates an image.
	 *
	 * @todo Find out how imagick_setfillcolor() works and complete the functionality.
	 * @param float $nDegrees The angle of rotation.
	 * @param bool $bDirectionClockwise For counterclockwise direction choose FALSE.
	 * @param string $sColor Dummy variable - Image magick does not support such functionality.
	 * imagick_setfillcolor() may support that but it's not documented.
	 * @param int $nIgnoreTransparent Dummy variable - Image magick does not support such functionality.
	 * @access public
	 * @return bool
	 */
	function rotate( $nDegrees, $bDirectionClockwise = true, $sColor = '', $nIgnoreTransparent = 0 )
	{
//		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) )
//		{
//			$this->loadImageFromFile();
//		}
//
//		# Calculate the needed clockwise angle of rotation
//		if ($bDirectionClockwise) {
//		}
//		else {
//			$nDegrees = 360 - $nDegrees;
//		}
//		
//		//var_dump(imagick_setfillcolor($this->rImage, "green"));
//		$bSuccess = imagick_rotate($this->rImage, $nDegrees);
//				
//		return $bSuccess;
	}

	/**
	 * Adds blur effect to the image.
	 * 
	 * @access public
	 * @param bool $bGaussianBlur
	 * @param float $nStrength
	 * @return bool
	 */
	function blur($bGaussianBlur = false, $nStrength = 1.0)
	{
//		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) ) {
//			$this->loadImageFromFile();
//		}
//		
//		/**
//		 * For reasonable results, the radius should be larger than sigma.
//		 * Use a radius of 0 and (Gaussian)BlurImage() selects a suitable radius for you.
//		 * 
//		 * bool imagick_gaussianblur ( resource $image, float $radius, float $sigma )
//		 * bool imagick_blur ( resource $image, float $radius, float $sigma )
//		 */
//		if ($bGaussianBlur) {
//			$bSuccess = imagick_gaussianblur($this->rImage, 0, $nStrength);
//		}
//		else {
//			$bSuccess = imagick_blur($this->rImage, 0, $nStrength);
//		}
//		
//		return $bSuccess;
	}
	
	/**
	 * Adds sharpening effect to the image.
	 * 
	 * @access public
	 * @param float $nStrength The bigger this value is, the more time is needed for processing!
	 * @return bool
	 */
	function sharpen($nStrength = 1.0)
	{
		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) ) {
//			$this->loadImageFromFile();
//		}
//
//		/**
//		 * SharpenImage() sharpens an image. We convolve the image with a Gaussian operator
//		 * of the given radius and standard deviation (sigma). For reasonable results,
//		 * radius should be larger than sigma. Use a radius of 0 and SharpenImage() selects
//		 * a suitable radius for you.
//		 * 
//		 * bool imagick_sharpen ( resource $image, float $radius, float $sigma )
//		 */
//		$bSuccess = imagick_sharpen ( $this->rImage, 0, $nStrength );
//		return $bSuccess;
	}

	/**
	 * Converts a truecolor image to grayscale.
	 *
	 * @access public
	 * @return bool
	 */
	function grayscale()
	{
		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) )
//		{
//			$this->loadImageFromFile();
//		}
//		
//		/**
//		 * We need to convert this image's colorspace from RGB to Grayscal.
//		 * We'll set the colorspace to IMAGICK_COLORSPACE_GRAY.
//		 * Note: IMAGICK_TYPE_GRAYSCALE
//		 */
//
//		/**
//		 * ARGH! I'll find a way to use that imagick_convert or something!
//		 * Then I'll see how you're gonna not do anything! Damn Image Magick!
//		 */
//		
//		/**
//		 * Experienced bug: http://pear.php.net/bugs/bug.php?id=1889
//		 */
//		$bSuccess = imagick_transformrgb($this->rImage, IMAGICK_COLORSPACE_GRAY);
//
//		return $bSuccess;
	}
	
	/**
	 * Adds a string of text to the image.
	 * 
	 * @todo Fix what's in the comments in the function.
	 *
	 * @param int $nPosX
	 * @param int $nPosY
	 * @param string $sText
	 * @param int $nFontSize
	 * @param string $sFontStyle Normal, italic, bold/oblique.
	 * @param string $sFontColor The format is 'ff00f0'.
	 * @param string $sFontFace Serif, sans-serif, monospace, fantasy, etc.
	 * @access public
	 * @return bool
	 */
	function text(  $nPosX, $nPosY, $sText, $nFontSize = 24, $sFontStyle = 'normal', $sFontColor = 'ffffff', $sFontFace = 'serif' )
	{
//		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) )
//		{
//			$this->loadImageFromFile();
//		}
//
//		if ( !imagick_begindraw($this->rImage) ) {
//			return false;
//		}
//		
//		/**
//		 * This doesn't have any effect, so the font face parameter is just a bogus one now.
//		 * 
//		 * @todo make this work!
//		 */
//		if ( !imagick_setfontface($this->rImage, $sFontFace) ) {
//			return false;
//		}
//
//		if ( !imagick_setfontsize($this->rImage, $nFontSize) ) {
//			return false;
//		}
//
//		switch (strtolower($sFontStyle)) {
//			case 'normal':
//				$nFontStyle = IMAGICK_FONTSTYLE_NORMAL;
//				break;
//			case 'italic':
//				$nFontStyle = IMAGICK_FONTSTYLE_ITALIC;
//				break;
//		
//			case 'bold':
//			case 'oblique':
//				$nFontStyle = IMAGICK_FONTSTYLE_OBLIQUE;
//				break;
//			default:
//				return false;
//				break;
//		}
//		
//		if ( !imagick_setfontstyle($this->rImage, $nFontStyle) ) {
//			return false;
//		}
//		
//		/**
//		 * This doesn't have any effect, so the font color parameter is just a bogus one now.
//		 * 
//		 * @todo Make this work!
//		 * 
//		 * Damn...
//		 * Experienced bug: http://pecl.php.net/bugs/bug.php?id=6358
//		 * 
//		 * If we can't change color here the written text will be unreadable.
//		 * One option is to add some "shine" but that's done with Image Magick's
//		 * function Stroke, which is not implemented in the PHP lib. Tough luck...
//		 */
////		if ( !imagick_setfillcolor($this->rImage, '#00ff00') ) {
////			echo "<pre>\n";
////			var_export(imagick_error($this->rImage));
////			echo "</pre>\n";
////			return false;
////		}
//		
//		$bSuccess = imagick_drawannotation($this->rImage, $nPosX, $nPosY, $sText);
//
//		return $bSuccess;
	}

	/**
	 * Adds a watermark to the image.
	 *
	 * @param string $sWatermarkFileName The filename (including path) to the watermark
	 * @param string $sPosition Two characters - first is for vertical, second - for horizontal position.
	 * Characters may be:
	 * Vertical: (t)op, (c)enter or (b)ottom
	 * Horizontal: (l)eft, (c)enter or (r)ight
	 * Examples: upper left - tl, bottomcenter - bc
	 * @param int $nMargin The distance between the watermark end the end of the image
	 * @param int $nOpacity Specifies how opaque the watermark will be. In percent.
	 * Example: 100 for totally opaque, 50 for half-transparent, 0 for invisible (no watermark!)
	 * @param int $nScaleToPercent Specifies the size of the watermark as a portion 
	 * of the size of the processed image. If left 0 no scaling will be done.
	 * Example: if set to 50 the warermark will occupy half the watermarked image.
	 * 
	 * @access public
	 * @return bool
	 */
	function watermark( $sWatermarkFileName, $sPosition = 'tr', $nMargin = 20, $nOpacity = 100, $nScaleToPercent = 0)
	{
		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) ) {
//			$this->loadImageFromFile();
//		}
//		
//		# Get the watermark ready
//		$rWatermark = new cImageMagickImageEditor( $sWatermarkFileName );
//		if (!$rWatermark->fileExists()) {
//			// Invalid parameter!
//			return false;
//		}
//		$rWatermark->loadImageFromFile();
//		if ( is_null( $rWatermark ) ) { // PAY ATTENTION HERE!
//			return false;
//		}
//
//		# Scale the watermark
//		if ( $nScaleToPercent <= 0 ) { // The less check is just in case ;)
//			// No scaling needed!
//		}
//		else {
//			$nWatermarkWidth = round( $nScaleToPercent * $this->getWidth() / 100);
//			$nWatermarkHeight = round( $nScaleToPercent * $this->getHeight() / 100);
//
//			$rWatermark->resize( $nWatermarkWidth, $nWatermarkHeight, true ); // Keep aspect ratio!
//
//			// The dimensions may have changed because we are keeping the aspect ratio
//			$nWatermarkWidth = $rWatermark->getWidth();
//			$nWatermarkHeight = $rWatermark->getHeight();
//		}
//		
//
//		# Calculate the exact position
//		
//		switch ( substr($sPosition, 0, 1) )
//		{
//			case 't':
//				// Top
//				$nWatermarkTop = $nMargin;
//				break;
//				
//			case 'c':
//				// Center
//				$nWatermarkTop = intval( ( $this->getHeight() / 2 ) - ( $rWatermark->getHeight() / 2 ) );
//				if ( $nWatermarkTop > ( $this->getHeight() - $nMargin ) ) {
//					$nWatermarkTop = $this->getHeight() - $nMargin ;
//				}
//				break;
//				
//			case 'b':
//				// Bottom
//				$nWatermarkTop = $this->getHeight() - ( $rWatermark->getHeight() + $nMargin );
//				if ( $nWatermarkTop > ( $this->getHeight() - $nMargin ) ) {
//					$nWatermarkTop = $this->getHeight() - $nMargin ;
//				}
//				break;
//				
//			default:
//				// Invalid parameter!
//				return false;
//				break;
//		}
//
//		switch ( substr($sPosition, 1, 1) )
//		{
//			case 'l':
//				// Left
//				$nWatermarkLeft = $nMargin;
//				break;
//				
//			case 'c':
//				// Center
//				$nWatermarkLeft = intval( ( $this->getWidth() / 2 ) - ( $rWatermark->getWidth() / 2 ) );
//				if ( $nWatermarkLeft < $nMargin ) {
//					$nWatermarkLeft = $nMargin ;
//				}
//				break;
//				
//			case 'r':
//				// Right
//				$nWatermarkLeft = $this->getWidth() - ( $rWatermark->getWidth() + $nMargin );
//				if ( $nWatermarkLeft < $nMargin ) {
//					$nWatermarkLeft = $nMargin ;
//				}
//				break;
//				
//			default:
//				// Invalid parameter!
//				return false;
//				break;
//		}
//		
//		# Start working on the image
//		imagick_begindraw($this->rImage);
//
//		// Taking care of transparency of the watermark...
//		if ( !imagick_setfillopacity($this->rImage, $nOpacity) ) {
//			return false;
//		}
//		
//		$bSuccess = imagick_composite(	$this->rImage,
//										IMAGICK_COMPOSITE_OP_OVERLAY,
//										$rWatermark->rImage,
//										$nWatermarkLeft,
//										$nWatermarkTop
//									);
//		return $bSuccess;
	}
	
	/**
	 * Flips the image.
	 *
	 * @param bool $bVerticalFlip If set to FALSE a horizontal flip will be performed.
	 * @access public
	 * @return bool
	 */
	function flip( $bVerticalFlip = true)
	{
//		# Prepare the image, if not prepared
//		if ( is_null( $this->rImage ) ) {
//			$this->loadImageFromFile();
//		}
//
//		if ($bVerticalFlip) {
//			$bSuccess = imagick_flip($this->rImage);
//		}
//		else {
//			$bSuccess = imagick_flop($this->rImage);
//		}
//		
//		return $bSuccess;
	}

	function outputJpeg( $nQuality = 75, $bPrintHeaders = false )
	{
		//First set the image quality
//		$bSuccess_1 = imagick_setcompressiontype($this->rImage, IMAGICK_COMPRESSION_JPEG);
//		$bSuccess_2 = imagick_setcompressionquality($this->rImage, intval($nQuality));
//		$bSuccess_3 = imagick_set_image_quality($this->rImage, intval($nQuality));
//		
//		if ( !($bSuccess_1 && $bSuccess_2 && $bSuccess_3)) {
//			// I don't know what but something BAD happend! Get out of here!
//			return false;
//		}
//
//		if ($bPrintHeaders) {
//			header('Content-Type: image/jpeg');
//		}
//		
//		echo imagick_image2blob($this->rImage);
//				
//		return true;
	}
	
	function outputPNG( $bPrintHeaders = false )
	{
//		die('NOT IMPLEMENTED!');
//		if ($bPrintHeaders) {
//			header('Content-Type: image/png');
//		}		
//
//		$bSuccess = imagepng($this->rImage);
//		
//		return $bSuccess;
	}	

	/**
	 * Saves the image in JPEG format.
	 * Maybe the saveImage function should depend on this one.
	 *
	 * @param string $sFileName If empty the original filename will be used. If no
	 * original filename is selected, a random filename will be genrated.
	 * @param $nQuality Specifies JPEG quality
	 * @return bool
	 */
	function saveJPEG( $sFileName = '', $nQuality = 75 )
	{
		//First set the image quality
//		$bSuccess_1 = imagick_setcompressiontype($this->rImage, IMAGICK_COMPRESSION_JPEG);
//		$bSuccess_2 = imagick_setcompressionquality($this->rImage, intval($nQuality));
//		$bSuccess_3 = imagick_set_image_quality($this->rImage, intval($nQuality));
//		
//		if ( !($bSuccess_1 && $bSuccess_2 && $bSuccess_3)) {
//			// I don't know what but something BAD happend! Get out of here!
//			return false;
//		}
//
//		/**
//		 * I'll use negative logic in this IF because I don't really expect anybody
//		 * to get into it, so it will be more productive to just skip it on the first check.
//		 */
//		if ($sFileName != '') {
//			// We have a filename - nothing to do here!
//		}
//		else {
//			// We don't have a filename - let's choose one
//
//			if ($this->sFileName != '') {
//				// The object has a filename associated - let's use that one
//				$sFileName = $this->sFileName;
//			}
//			else {
//				// No filename associated with the object - let's generate one
//				$sFileName = time() . '.jpeg';
//			}
//		}
//
//		/*
//		 * The imagick_writeimage function chooses the format to write into
//		 * based on the file extension. So our main task is to ensure we have 
//		 * the correct extension.
//		 */
//
//		$sExtension = substr($sFileName, -4);
//		if (strtolower($sExtension) == '.jpg' || strtolower($sExtension) == 'jpeg') {
//			// We have the correct extension - go, go, go!
//		}
//		else {
//			$sFileName .= '.jpeg';  // Set the right extension
//		}
//		
//		$bSuccess = (bool)imagick_writeimage($this->rImage, $sFileName);		
//		
//		return $bSuccess;	
	}
	
	/**
	 * Saves the image in Portable Network Graphics format.
	 * Maybe the saveImage function should depend on this one.
	 *
	 * @param string $sFileName If empty the original filename will be used. If no
	 * original filename is selected, a random filename will be genrated.
	 * @return bool
	 */
	function savePNG( $sFileName = '' )
	{
//		/**
//		 * I'll use negative logic in this IF because I don't really expect anybody
//		 * to get into it, so it will be more productive to just skip it on the first check.
//		 */
//		if ($sFileName != '') {
//			// We have a filename - nothing to do here!
//		}
//		else {
//			// We don't have a filename - let's choose one
//
//			if ($this->sFileName != '') {
//				// The object has a filename associated - let's use that one
//				$sFileName = $this->sFileName;
//			}
//			else {
//				// No filename associated with the object - let's generate one
//				$sFileName = time() . '.png';
//			}
//		}
//
//		/*
//		 * The imagick_writeimage function chooses the format to write into
//		 * based on the file extension. So our main task is to ensure we have 
//		 * the correct extension.
//		 */
//
//		$sExtension = substr($sFileName, -3);
//		if (strtolower($sExtension) == 'png') {
//			// We have the correct extension - go, go, go!
//		}
//		else {
//			$sFileName .= '.png';  // Set the right extension
//		}
//		
//		$bSuccess = (bool)imagick_writeimage($this->rImage, $sFileName);		
//		
//		return $bSuccess;
	}
	
}

?>