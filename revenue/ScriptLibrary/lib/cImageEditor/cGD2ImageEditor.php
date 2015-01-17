<?php
require_once( dirname( dirname(__FILE__) ) . DIRECTORY_SEPARATOR . "cImageEditor.php" );

class cGD2ImageEditor extends cImageEditor
{
	/**
	 * This is a free font downloaded from http://www.webpagepublicity.com/free-fonts.html
	 */
	var $sUsedFont;

	function cGD2ImageEditor( $sFileName = "" )
	{
		$this->cImageEditor( $sFileName );
		$this->sUsedFont = CS_ROOT_DIR . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'arena_condensed.ttf';
	}

	function getImageLoaderHandler( $sFileName )
	{
		$aInfo = pathinfo( $sFileName );
		switch( strtolower( $aInfo[ 'extension' ] ) )
		{
			case "jpg" 	:
			case "jpeg" :	return "imagecreatefromjpeg";
							break;
			case "png"	:	return "imagecreatefrompng";
							break;
			case "gif"	:	return "imagecreatefromgif";
							break;
			case "bmp"	:	return "imagecreatefrombmp";
							break;
		}
	}

	function getImageSaverHandler( $sFileName )
	{
		$aInfo = pathinfo( $sFileName );
		switch( $aInfo[ 'extension' ] )
		{
			case "jpg" 	:
			case "jpeg" :	return "imagejpeg";
							break;
			case "png"	:	return "imagepng";
							break;
			case "gif"	:	return "imagegif";
							break;
			case "bmp"	:	return "image2wbmp";
							break;
		}
	}

	function loadImageFromFile( $sFileName = "" )
	{
		$bSuccess = (bool)parent::loadImageFromFile( $sFileName );
		$sFunctionName = $this->getImageLoaderHandler( $this->sFileName );
		if ($bSuccess) {
			$aSize = getimagesize( $this->sFileName );
			$this->aImageProperties[ "width" ] = $aSize[ 0 ];
			$this->aImageProperties[ "height" ] = $aSize[ 1 ];
			$this->aImageProperties[ "bits" ] = $aSize[ "bits" ];
		}
		
		// If getimagesize() fails $aSize will FALSE, i.e. empty.
		return $bSuccess && !empty($aSize);
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
		if (isset($this->aImageProperties[ "width" ])) {
			return $this->aImageProperties[ "width" ];
		} else return 0;
	}
	
	function getHeight()
	{
		if (isset($this->aImageProperties[ "height" ])) {
			return $this->aImageProperties[ "height" ];
		} else return 0;
	}

	function getBitDepth()
	{
		if (isset($this->aImageProperties[ "bits" ])) {
			return $this->aImageProperties[ "bits" ];
		} else return 0;
	}

	/**
	 * Resizes an image to given dimensions.
	 *
	 * @todo add imageantialias option
	 * 
	 * @access public
	 * @param int $nWidth
	 * @param int $nHeight
	 * @param bool $bKeepAspectRatio
	 * @return bool
	 */
	function resize( $nWidth, $nHeight, $bKeepAspectRatio = false, $bResizeUp = false )
	{
		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) )
		{
			$bSuccess = $this->loadImageFromFile();
			
			if ($bSuccess === false) {
				// No file loaded!!!
				return false;
			}
		}
		
		# Take care of the aspect ratio
		if ( $bKeepAspectRatio )
		{
			if ( $this->getHeight() / $nHeight >= $this->getWidth() / $nWidth )
			{
				// Height is leading
				$nWidth = round( $this->getWidth() / ( $this->getHeight() / $nHeight) );
			}
			else
			{
				// Width is leading
				$nHeight = round( $this->getHeight() / ( $this->getWidth() / $nWidth) );
			}
		}
		
		if ( $nWidth < $this->getWidth() || $nHeight < $this->getHeight() || $bResizeUp )
		{
			# Resize/resample
			
			$rNewImage = imagecreatetruecolor( $nWidth, $nHeight );
			imagealphablending($rNewImage, false);
			
			// Use imagecopyresampled() for quality optimisation
			$bSuccess = imagecopyresampled( $rNewImage, $this->rImage, 0, 0, 0, 0, $nWidth, $nHeight, $this->getWidth(), $this->getHeight() );
			
			if ( $bSuccess )
			{
				$this->setWidth( $nWidth );
				$this->setHeight( $nHeight );
				$this->rImage = $rNewImage;
			}			
		
			/**
			 * DO NOT DO imagedestroy() !!!
			 */
		}
		else
		{
			// no resize needed
			return true;
		}
		
		return $bSuccess;
	}	

	
	/**
	 * Crop an image to given dimensions.
	 *
	 * 
	 * @access public
	 * @param int $nStartX
	 * @param int $nStartY
	 * @param int $nEndX
	 * @param int $nEndY
	 * @return bool
	 */
	function crop( $nStartX, $nStartY, $nEndX, $nEndY )
	{
		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) )
		{
			$bSuccess = $this->loadImageFromFile();
			
			if ($bSuccess === false) {
				// No file loaded!!!
				return false;
			}
		}

		$nWidth = $nEndX - $nStartX;
		$nHeight = $nEndY - $nStartY;

		$rNewImage = imagecreatetruecolor( $nWidth, $nHeight );
		imagealphablending($rNewImage, false);
		
		// Use imagecopyresampled() for quality optimisation
		$bSuccess = imagecopy( $rNewImage, $this->rImage, 0, 0, $nStartX, $nStartY, $nWidth, $nHeight );
		
		if ( $bSuccess )
		{
			$this->setWidth( $nWidth );
			$this->setHeight( $nHeight );
			$this->rImage = $rNewImage;
		}			
		
		/**
		 * DO NOT DO imagedestroy() !!!
		 */
		
		return $bSuccess;
	}

	function cropPos( $sPosition, $nWidth, $nHeight )
	{
		$nX = 0;
		$nY = 0;
		
		if( $nWidth > $this->getWidth() )
		{
			$nWidth = $this->getWidth();
		}

		if( $nHeight > $this->getHeight() )
		{
			$nHeight = $this->getHeight();
		}
		
		
		# Calculate the exact position
		switch ( substr( $sPosition, 0, 1 ) )
		{
			case 't':
				// Top
				$nY = 0;
				break;
				
			case 'c':
				// Center
				$nY = intval( ( $this->getHeight() / 2 ) - ( $nHeight / 2 ) );
				break;
				
			case 'b':
				// Bottom
				$nY = $this->getHeight() - $nHeight;
				break;
				
			default:
				// Invalid parameter!
				return false;
				break;
		}

		switch ( substr($sPosition, 1, 1) )
		{
			case 'l':
				// Left
				$nX = 0;
				break;
				
			case 'c':
				// Center
				$nX = intval( ( $this->getWidth() / 2 ) - ( $nWidth / 2 ) );
				break;
				
			case 'r':
				// Right
				$nX = $this->getWidth() - $nWidth;
				break;
				
			default:
				// Invalid parameter!
				return false;
				break;
		}

		$this->crop( $nX, $nY, $nX + $nWidth, $nY + $nHeight );

		return true;
	}	

	/**
	 * Rotates an image.
	 *
	 * @param float $nDegrees The angle of rotation.
	 * @param bool $bDirectionClockwise For counterclockwise direction choose FALSE.
	 * @param string $sColor Specifies the color of the uncovered zone after the rotation. Format: 0033CC
	 * @access public
	 * @return bool
	 */
	function rotate( $nDegrees, $bDirectionClockwise = true, $sColor = '', $nIgnoreTransparent = 0 )
	{
		# Look for the needed PHP function
		if (!is_callable('imagerotate')) {
			// This function is only available if PHP is compiled with the bundled version of the GD library.
			return false;
		}
		
		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) )
		{
			$this->loadImageFromFile();
		}

		# Calculate the needed clockwise angle of rotation
		if ($bDirectionClockwise) {
		}
		else {
			$nDegrees = 360 - $nDegrees;
		}

		if (strpos($sColor, '0X') !== false) {
			$sColor = str_replace('0X', '', $sColor);
		}
		if (strpos($sColor, '0x') !== false) {
			$sColor = str_replace('0x', '', $sColor);
		}
		if (strpos($sColor, '#') !== false) {
			$sColor = str_replace('#', '', $sColor);
		}

		$sColor = '0x' . strtoupper($sColor);

		$rResult = imagerotate($this->rImage, $nDegrees, $sColor);

		if (!empty($rResult)) {
			$this->rImage = $rResult;
			
			$nNewHeight = 	intval(		abs(sin(deg2rad($nDegrees))) * $this->getWidth()
										+	abs(cos(deg2rad($nDegrees))) * $this->getHeight()
									);
							
			$nNewWidth = 	intval(		abs(cos(deg2rad($nDegrees)) * $this->getWidth())
										+	abs(sin(deg2rad($nDegrees)) * $this->getHeight())
									);
			$this->setHeight($nNewHeight);
			$this->setWidth($nNewWidth);
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Duplicates the functionality offered by imageconvolution. About 50 times slower!
	 *
	 * @access private
	 * @param resource $src
	 * @param array $filter
	 * @param float $filter_div
	 * @param float $offset
	 * @author http://www.webdevlogs.com/2007/03/02/simple-replication-of-imageconvolution-function/
	 * @return bool
	 */
	function imageconvolution_replacement($src, $filter, $filter_div, $offset){
	    if ($src==NULL) {
	        return false;
	    }
	   
	    $sx = imagesx($src);
	    $sy = imagesy($src);
	    $srcback = imagecreatetruecolor($sx, $sy);
		imagealphablending($srcback, false);
	    ImageCopy($srcback, $src,0,0,0,0,$sx,$sy);
	   
	    if($srcback==NULL){
	        return false;
	    }
	       
	    for ($y=0; $y<$sy; ++$y){
	        for($x=0; $x<$sx; ++$x){
	            $new_r = $new_g = $new_b = 0;
	            $alpha = imagecolorat($srcback, $pxl[0], $pxl[1]);
	            $new_a = $alpha >> 24;
	           
	            for ($j=0; $j<3; ++$j) {
	                $yv = min(max($y - 1 + $j, 0), $sy - 1);
	                for ($i=0; $i<3; ++$i) {
	                        $pxl = array(min(max($x - 1 + $i, 0), $sx - 1), $yv);
	                    $rgb = imagecolorat($srcback, $pxl[0], $pxl[1]);
	                    $new_r += (($rgb >> 16) & 0xFF) * $filter[$j][$i];
	                    $new_g += (($rgb >> 8) & 0xFF) * $filter[$j][$i];
	                    $new_b += ($rgb & 0xFF) * $filter[$j][$i];
	                }
	            }
	
	            $new_r = ($new_r/$filter_div)+$offset;
	            $new_g = ($new_g/$filter_div)+$offset;
	            $new_b = ($new_b/$filter_div)+$offset;
	
	            $new_r = ($new_r > 255)? 255 : (($new_r < 0)? 0:$new_r);
	            $new_g = ($new_g > 255)? 255 : (($new_g < 0)? 0:$new_g);
	            $new_b = ($new_b > 255)? 255 : (($new_b < 0)? 0:$new_b);
	
	            $new_pxl = imagecolorallocatealpha($src, (int)$new_r, (int)$new_g, (int)$new_b, $new_a);
	            if ($new_pxl == -1) {
	                $new_pxl = imagecolorclosestalpha($src, (int)$new_r, (int)$new_g, (int)$new_b, $new_a);
	            }
	            if (($y >= 0) && ($y < $sy)) {
	                imagesetpixel($src, $x, $y, $new_pxl);
	            }
	        }
	    }
	    imagedestroy($srcback);
	    return true;
	}
	
	/**
	 * Adds blur effect to the image.
	 * 
	 * @todo Make the strength of the effect adjustable.
	 *
	 * @access public
	 * @param bool $bGaussianBlur
	 * @param float $nStrength Not implemented yet! This param is here just for compatibility.
	 * @return bool
	 */
	function blur($bGaussianBlur = false, $nStrength = 1.0)
	{
		if (is_callable('imageconvolution')) {
			// This function is only available if PHP (>5.1.0) is compiled with the bundled version of the GD library.
			$sFunction = 'imageconvolution';
		}
		else {
			$sFunction = 'imageconvolution_replacement';
		}

		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) ) {
			$this->loadImageFromFile();
		}
		
		# Prepare the User Defined Filter
		if ($bGaussianBlur) {
			$aParams = array(
							array( 1, 2, 1),
							array( 2, 4, 2),
							array( 1, 2, 1),
						);
			$nDiv = 16; // The sum of the numbers in the matrix
		}
		else {
			$aParams = array(
							array( 1, 1, 1),
							array( 1, 1, 1),
							array( 1, 1, 1),
						);
			$nDiv = 9; // The sum of the numbers in the matrix
		}
		
		//$bSuccess = call_user_func_array($sFunction, array( $this->rImage, $aParams, $nDiv, 0));
		if( $sFunction == 'imageconvolution' )
		{
			$bSuccess = $sFunction( $this->rImage, $aParams, $nDiv, 0);
		}
		else if( $sFunction == 'imageconvolution' )
		{
			$bSuccess = cGD2ImageEditor::imageconvolution_replacement( $this->rImage, $aParams, $nDiv, 0 );
		}
		
		return $bSuccess;
	}
	
	/**
	 * Adds sharpening effect to the image.
	 * 
	 * @todo Make the strength of the effect adjustable.
	 *
	 * @access public
	 * @param float $nStrength Not implemented yet! This param is here just for compatibility.
	 * @return bool
	 */
	function sharpen( $nStrength = 1.0 )
	{
		if (is_callable('imageconvolution')) {
			// This function is only available if PHP (>5.1.0) is compiled with the bundled version of the GD library.
			$sFunction = 'imageconvolution';
		}
		else {
			$sFunction = 'imageconvolution_replacement';
		}

		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) ) {
			$this->loadImageFromFile();
		}
		
		# Prepare the User Defined Filter
		$aParams = array(
						array( -1, -1, -1),
						array( -1, 16, -1),
						array( -1, -1, -1),
					);
		$nDiv = 8; // The sum of the numbers in the matrix
		
		$bSuccess = call_user_func_array($sFunction, array( $this->rImage, $aParams, $nDiv, 0));
		
		return $bSuccess;
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
		if ( is_null( $this->rImage ) )
		{
			$this->loadImageFromFile();
		}
	
		$bSuccess = imagecopymergegray( $this->rImage, $this->rImage, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), 0);
		
		return $bSuccess;
	}


	/**
	 * Adds a string of text to the image with relative coordinates.
	 *
	 * @todo Implement tilted text (that can run diagonally on the screen).
	 * 
	 * @param int $nPosX
	 * @param int $nPosY
	 * @param string $sText
	 * @param int $nFontSize
	 * @param string $sFont
	 * @param string $sFontStyle
	 * @param string $sFontColor The format is ff00cc.
	 * @uses cGD2ImageEditor::watermark()
	 * @access public
	 * @return bool
	 */
	function text( $sPosition, $sText, $nFontSize = 24, $sFontStyle = 'normal', $sFontColor = 'ffffff' )
	{
		$nMargin = 5; // We need a margin of 5 pixels
		$nPixelToPointCoefficient = 0.96;
		
		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) )
		{
			$this->loadImageFromFile();
		}

		if ( is_callable( 'imagelayereffect' ) ) {
			/**
			 * Take care of transparency.
			 *
			 * (PHP 4 >= 4.3.0, PHP 5)
			 * Note: This function is only available if PHP is compiled with the bundled version of the GD library.
			 * Note: This function requires GD 2.0.1 or later (2.0.28 or later is recommended).
			 * 
			 * Options:
			 * IMG_EFFECT_REPLACE	: Use pixel replacement (equivalent of ImageAlphaBlending(FALSE))
			 * IMG_EFFECT_NORMAL	: Use normal pixel blending (equivalent of ImageAlphaBlending(TRUE))
			 * IMG_EFFECT_OVERLAY	: Use the overlay routine. Overlay has the effect that black background 
			 * pixels will remain black, white background pixels will remain white, but grey background pixels
			 * will take the colour of the foreground pixel.
			 */
			imagelayereffect( $this->rImage, IMG_EFFECT_OVERLAY );
		}
		else {
			// There will be no transparency!
		}

		// Prepare font color
		$nColor = imagecolorallocate(	$this->rImage,
										hexdec( substr( $sFontColor, 1, 2 ) ), 
										hexdec( substr( $sFontColor, 3, 2 ) ), 
										hexdec( substr( $sFontColor, 5, 2 ) )
									);

		//calculate the text box size
		$aTextSize = imagettfbbox( (float)$nFontSize, 0, $this->sUsedFont, $sText );

		$nTextWidth = $aTextSize[ 2 ] - $aTextSize[ 0 ];
		$nTextHeight = $aTextSize[ 3 ] - $aTextSize[ 5 ];

		# Calculate the exact position
		switch ( substr( $sPosition, 0, 1 ) )
		{
			case 't':
				// Top
				$nTextTop = $nTextHeight + $nMargin;
				break;
				
			case 'c':
				// Center
				$nTextTop = intval( ( $this->getHeight() / 2 ) - ( $nTextHeight / 2 ) );
				if ( $nTextTop > ( $this->getHeight() - $nMargin ) ) {
					$nTextTop = $this->getHeight() - $nMargin ;
				}
				break;
				
			case 'b':
				// Bottom
				$nTextTop = $this->getHeight() - ( $nTextHeight + $nMargin );
				if ( $nTextTop > ( $this->getHeight() - $nMargin ) ) {
					$nTextTop = $this->getHeight() - $nMargin ;
				}
				break;
				
			default:
				// Invalid parameter!
				return false;
				break;
		}

		switch ( substr($sPosition, 1, 1) )
		{
			case 'l':
				// Left
				$nTextLeft = $nMargin;
				break;
				
			case 'c':
				// Center
				$nTextLeft = intval( ( $this->getWidth() / 2 ) - ( $nTextWidth / 2 ) );
				if ( $nTextLeft < $nMargin ) {
					$nTextLeft = $nMargin ;
				}
				break;
				
			case 'r':
				// Right
				$nTextLeft = $this->getWidth() - ( $nTextWidth + $nMargin );
				if ( $nTextLeft < $nMargin ) {
					$nTextLeft = $nMargin ;
				}
				break;
				
			default:
				// Invalid parameter!
				return false;
				break;
		}
		
		/*
		Write text using: true type, free type 2 or post script fonts
		array imagettftext ( resource $image, float $size, float $angle, int $x, int $y, int $color, string $fontfile, string $text )
		array imagefttext ( resource $image, float $size, float $angle, int $x, int $y, int $col, string $font_file, string $text [, array $extrainfo] )
		array imagepstext ( resource $image, string $text, resource $font, int $size, int $foreground, int $background, int $x, int $y [, int $space [, int $tightness [, float $angle [, int $antialias_steps]]]] )	
		
		If none of the above worked - write simple text
		bool imagestring ( resource $image, int $font, int $x, int $y, string $string, int $color )	
		*/

		//$aResult = imagettftext( $this->rImage, $nFontSize, 0, $nPosX, $nPosY, $nColor, $this->sUsedFont, $sText );
		$aResult = imagettftext(	$this->rImage,
									intval($nFontSize * $nPixelToPointCoefficient),
									0,
									$nTextLeft,
									$nTextTop,
									$nColor,
									$this->sUsedFont,
									$sText
								);
		
		$bSuccess = !empty( $aResult );
		
		return $bSuccess;
	}


	/**
	 * Adds a string of text to the image with absolute X and Y coordinates.
	 *
	 * @todo Implement tilted text (that can run diagonally on the screen).
	 * 
	 * @param int $nPosX
	 * @param int $nPosY
	 * @param string $sText
	 * @param int $nFontSize
	 * @param string $sFont
	 * @param string $sFontStyle
	 * @param string $sFontColor The format is ff00cc.
	 * @param string $sFontFace
	 * @uses cGD2ImageEditor::watermark()
	 * @access public
	 * @return bool
	 */
	function textabs( $nPosX, $nPosY, $sText, $nFontSize = 24, $sFontStyle = 'normal', $sFontColor = 'ffffff', $sFontFace = 'serif' )
	{
		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) )
		{
			$this->loadImageFromFile();
		}

		if ( is_callable( 'imagelayereffect' ) ) {
			/**
			 * Take care of transparency.
			 *
			 * (PHP 4 >= 4.3.0, PHP 5)
			 * Note: This function is only available if PHP is compiled with the bundled version of the GD library.
			 * Note: This function requires GD 2.0.1 or later (2.0.28 or later is recommended).
			 * 
			 * Options:
			 * IMG_EFFECT_REPLACE	: Use pixel replacement (equivalent of ImageAlphaBlending(FALSE))
			 * IMG_EFFECT_NORMAL	: Use normal pixel blending (equivalent of ImageAlphaBlending(TRUE))
			 * IMG_EFFECT_OVERLAY	: Use the overlay routine. Overlay has the effect that black background 
			 * pixels will remain black, white background pixels will remain white, but grey background pixels
			 * will take the colour of the foreground pixel.
			 */
			imagelayereffect( $this->rImage, IMG_EFFECT_OVERLAY );
		}
		else {
			// There will be no transparency!
		}
		
		// Prepare font color
		$nColor = imagecolorallocate(	$this->rImage,
										hexdec( substr( $sFontColor, 1, 2 ) ), 
										hexdec( substr( $sFontColor, 3, 2 ) ), 
										hexdec( substr( $sFontColor, 5, 2 ) )
									);
		
		/*
		Write text using: true type, free type 2 or post script fonts
		array imagettftext ( resource $image, float $size, float $angle, int $x, int $y, int $color, string $fontfile, string $text )
		array imagefttext ( resource $image, float $size, float $angle, int $x, int $y, int $col, string $font_file, string $text [, array $extrainfo] )
		array imagepstext ( resource $image, string $text, resource $font, int $size, int $foreground, int $background, int $x, int $y [, int $space [, int $tightness [, float $angle [, int $antialias_steps]]]] )	
		
		If none of the above worked - write simple text
		bool imagestring ( resource $image, int $font, int $x, int $y, string $string, int $color )	
		*/

		$aResult = imagettftext( $this->rImage, $nFontSize, 0, $nPosX, $nPosY, $nColor, $this->sUsedFont, $sText );
		
		$bSuccess = !empty( $aResult );
		
		return $bSuccess;
	}

	/**
	* Helper function: converts GIF to transparent RGB
	*/
	function GIFtoTrueColorTransparent( $nTransColor )
	{
		$nW = $this->getWidth();
		$nH = $this->getHeight();

		$rTrueColorImg = imagecreatetruecolor( $nW, $nH );
		imagealphablending( $this->rImage, false );
		//imagesavealpha( $this->rImage, true );
		imagecopy( $rTrueColorImg, $this->rImage, 0, 0, 0, 0, $nW, $nH );
		imagedestroy( $this->rImage );
		$this->rImage = $rTrueColorImg;


		//imagealphablending( $this->rImage, false );
		//imagesavealpha( $this->rImage, true );

		$hWImage = $this->rImage;
		
		for ( $nX = 0; $nX < $nW; $nX++ )
		{
			for ( $nY = 0; $nY < $nH; $nY++ )
			{
				if (imagecolorat( $hWImage, $nX, $nY ) == $nTransColor )
				{
					imagesetpixel( $hWImage, $nX, $nY, 127 << 24 );
				}
			}
		}
	}
	
	/**
	* @static 
	* @param cGD2ImageEditor $rImageToScale
	*/
	function scaleTransparent( &$rImageToScale, $nScaleToPercent, $nOpacity )
	{
		$nW = $rImageToScale->getWidth();
		$nH = $rImageToScale->getHeight();
		
		if ($nOpacity > 100) {
			$nOpacity = 100;
		}
		elseif ($nOpacity < 0){
			$nOpacity = 0;
		}
		
		$nTransparency = 100 - $nOpacity;
				
		$rGrayImage = imagecreatetruecolor( $nW, $nH );
		
		for ( $nX = 0; $nX < $nW; $nX++ )
		{
			for ( $nY = 0; $nY < $nH; $nY++ )
			{
				$nColor = imagecolorat($rImageToScale->rImage, $nX, $nY);
				$nAlpha = ( $nColor >> 24 ) & 0x7F;
			
				$nAlphaGray = imagecolorallocate($rGrayImage, $nAlpha, $nAlpha, $nAlpha);
				imagesetpixel($rGrayImage, $nX, $nY, $nAlphaGray);
			}
		}

		$nScaledWidth = round( $nScaleToPercent * $rImageToScale->getWidth() / 100);
		$nScaledHeight = round( $nScaleToPercent * $rImageToScale->getHeight() / 100);

		imagecopyresampled( $rGrayImage, $rGrayImage, 0, 0, 0, 0, $nScaledWidth, $nScaledHeight, $nW, $nH);
		$rImageToScale->resize( $nScaledWidth, $nScaledHeight, true ); // Keep aspect ratio!
		imagealphablending( $rImageToScale->rImage, false );
		imagesavealpha( $rImageToScale->rImage, true );

		$nGeneralAlpha = abs(intval((127 * $nTransparency) / 100));
		
		for ( $nX = 0; $nX < $nScaledWidth; $nX++ )
		{
			for ( $nY = 0; $nY < $nScaledHeight; $nY++ )
			{
				$nAlphaGray = imagecolorat($rGrayImage, $nX, $nY);
				$nAlpha = $nAlphaGray & 0xFF;
				
				if ($nAlpha < $nGeneralAlpha) {
					$nAlpha = $nGeneralAlpha;
				}
				
				$nColor = imagecolorat($rImageToScale->rImage, $nX, $nY);
				imagesetpixel( $rImageToScale->rImage, $nX, $nY, ( $nColor & 0xFFFFFF ) | ($nAlpha << 24) );
			}
		}
		return true;
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
	function watermark( $sWatermarkFileName, $sPosition = 'tr', $nMargin = 20, $nOpacity = 100, $nScaleToPercent = 100, $nTransColor = '')
	{
		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) ) {
			$this->loadImageFromFile();
		}

		# Get the watermark ready
		$rWatermark = new cGD2ImageEditor( $sWatermarkFileName );
		if (!$rWatermark->fileExists()) {
			
			// Invalid parameter!
			return false;
		}
		$rWatermark->loadImageFromFile();
		if ( is_null( $rWatermark ) ) { // PAY ATTENTION HERE!
			return false;
		}

		//assume that we have an indexed color image, and want the color nTransColor to be transparent
		if( $nTransColor !== '' )
		{
			$rWatermark->GIFtoTrueColorTransparent( $nTransColor );
		}

		# Scale the watermark
		if ( $nScaleToPercent <= 0 ) { // The less check is just in case ;)
			// No scaling needed!
			cGD2ImageEditor::scaleTransparent( $rWatermark, 100, $nOpacity );
		}
		else {
			$nWatermarkWidth = round( $nScaleToPercent * $this->getWidth() / 100);
			$nWatermarkHeight = round( $nScaleToPercent * $this->getHeight() / 100);

			$rWatermark->resize( $nWatermarkWidth, $nWatermarkHeight, true ); // Keep aspect ratio!

			// The dimensions may have changed because we are keeping the aspect ratio
			$nWatermarkWidth = $rWatermark->getWidth();
			$nWatermarkHeight = $rWatermark->getHeight();
			
			$nTransColor = imagecolorat($rWatermark->rImage, 1, 1);
			
			$rWatermark->GIFtoTrueColorTransparent( $nTransColor );

			cGD2ImageEditor::scaleTransparent( $rWatermark, $nScaleToPercent, $nOpacity );
		}
		

		# Calculate the exact position
		
		switch ( substr($sPosition, 0, 1) )
		{
			case 't':
				// Top
				$nWatermarkTop = $nMargin;
				break;
				
			case 'c':
				// Center
				$nWatermarkTop = intval( ( $this->getHeight() / 2 ) - ( $rWatermark->getHeight() / 2 ) );
				if ( $nWatermarkTop > ( $this->getHeight() - $nMargin ) ) {
					$nWatermarkTop = $this->getHeight() - $nMargin ;
				}
				break;
				
			case 'b':
				// Bottom
				$nWatermarkTop = $this->getHeight() - ( $rWatermark->getHeight() + $nMargin );
				if ( $nWatermarkTop > ( $this->getHeight() - $nMargin ) ) {
					$nWatermarkTop = $this->getHeight() - $nMargin ;
				}
				break;
				
			default:
				// Invalid parameter!
				return false;
				break;
		}

		switch ( substr($sPosition, 1, 1) )
		{
			case 'l':
				// Left
				$nWatermarkLeft = $nMargin;
				break;
				
			case 'c':
				// Center
				$nWatermarkLeft = intval( ( $this->getWidth() / 2 ) - ( $rWatermark->getWidth() / 2 ) );
				if ( $nWatermarkLeft < $nMargin ) {
					$nWatermarkLeft = $nMargin ;
				}
				break;
				
			case 'r':
				// Right
				$nWatermarkLeft = $this->getWidth() - ( $rWatermark->getWidth() + $nMargin );
				if ( $nWatermarkLeft < $nMargin ) {
					$nWatermarkLeft = $nMargin ;
				}
				break;
				
			default:
				// Invalid parameter!
				return false;
				break;
		}

		/**
		 * Alpha blending is ON by default in newer PHP versions
		 * but in some old PHPs it's OFF, so just in case:
		 */
		imagealphablending( $this->rImage, true );
		imagealphablending( $rWatermark->rImage, true );

		# WARNING! Opacity an size locked at 100%!
		
		// Use the faster imagecopy()
		$bSuccess = imagecopy(	$this->rImage,
								$rWatermark->rImage,
								$nWatermarkLeft,
								$nWatermarkTop,
								0,
								0,
								$rWatermark->getWidth(),
								$rWatermark->getHeight()
							); 
		return $bSuccess;
	}
	
	function tiledWatermark( $sWatermarkFileName, $nHSpace, $nVSpace, $nTransColor, $nOpacity )
	{
		# Prepare watermark
		$rWatermark = new cGD2ImageEditor( $sWatermarkFileName );
		$rWatermark->loadImageFromFile();
		
		$nGeneralAlpha = abs(intval((127 * ( 100 - $nOpacity)) / 100));
		
		$nWatermarkedWidth = 0;
		$nWatermarkedHeight = 0;
		$nImageWidth = $this->getWidth();
		$nImageHeight = $this->getHeight();
		$nWatermarkWidth = $rWatermark->getWidth();
		$nWatermarkHeight = $rWatermark->getHeight();

		imagealphablending( $this->rImage, true );
		imagealphablending( $rWatermark->rImage, true );
		cGD2ImageEditor::scaleTransparent( $rWatermark, 100, $nOpacity );

		while ( ($nImageWidth > $nWatermarkedWidth) && ($nImageHeight > $nWatermarkedHeight) ) {
			while ($nImageWidth > $nWatermarkedWidth)
			{
				/*
				for ($nX = $nWatermarkedWidth + $nHSpace; ($nX < $nImageWidth) && ($nX < $nWatermarkedWidth + $nHSpace + $nWatermarkWidth); $nX++)
				{
					for ($nY = $nWatermarkedHeight + $nVSpace; ($nY < $nImageHeight) && ($nY < $nWatermarkedHeight + $nVSpace + $nWatermarkHeight) ; $nY++)
					{
						# Prepare alpha
						$nAlphaColor = imagecolorat( $rWatermark->rImage, ($nX - $nWatermarkedWidth - $nHSpace), ($nY - $nWatermarkedHeight - $nVSpace));
						$nAlpha = ( $nAlphaColor >> 24 ) & 0x7F;
						
						if ($nAlpha < $nGeneralAlpha) {
							$nAlpha = $nGeneralAlpha;
						}
						$nColor = imagecolorat( $rWatermark->rImage,  ($nX - $nWatermarkedWidth - $nHSpace), ($nY - $nWatermarkedHeight - $nVSpace) );
						imagesetpixel( $this->rImage, $nX, $nY, ( $nColor & 0xFFFFFF ) | ($nAlpha << 24) );
					}
				}
				*/
				$bSuccess = imagecopy(	$this->rImage,
						$rWatermark->rImage,
						$nWatermarkedWidth,
						$nWatermarkedHeight,
						0,
						0,
						$rWatermark->getWidth(),
						$rWatermark->getHeight()
					); 

				$nWatermarkedWidth += $nWatermarkWidth + $nHSpace;
			}
			
			$nWatermarkedWidth = 0;
			$nWatermarkedHeight += $nWatermarkHeight + $nVSpace;
		}
		
		return true;
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
		# Prepare the image, if not prepared
		if ( is_null( $this->rImage ) ) {
			$this->loadImageFromFile();
		}

		$rNewImage = imagecreatetruecolor( $this->getWidth(), $this->getHeight() );
		imagealphablending( $rNewImage, false );
		
		if ($bVerticalFlip) {
			$bSuccess = imagecopyresampled( $rNewImage, $this->rImage, 0, 0, 0, ( $this->getHeight() - 1 ), $this->getWidth(), $this->getHeight(), $this->getWidth(), ( 0 - $this->getHeight() ) );
		}
		else {
			$bSuccess = imagecopyresampled( $rNewImage, $this->rImage, 0, 0, ( $this->getWidth() - 1 ), 0, $this->getWidth(), $this->getHeight(), ( 0 - $this->getWidth() ), $this->getHeight() );
		}

		if ($bSuccess) {
			$this->rImage = $rNewImage;
			# Do not imagedestroy($rNewImage)!
			return true;
		}
		else {
			imagedestroy($rNewImage);
			return false;
		}
	}

	function outputJpeg( $nQuality = 75, $bPrintHeaders = false )
	{
		if ($bPrintHeaders) {
			header('Content-Type: image/jpeg');
		}		
		
		$bSuccess = imagejpeg($this->rImage, '', $nQuality);
		
		return $bSuccess;
	}
	
	function outputPNG( $bPrintHeaders = false )
	{
		if ($bPrintHeaders) {
			header('Content-Type: image/png');
		}		

		$bSuccess = imagepng($this->rImage);
		
		return $bSuccess;
	}	

	function saveJPEG( $sFileName = '' , $nQuality = 75, $bgColor = '' )
	{
		/**
		 * I'll use negative logic in this IF because I don't really expect anybody
		 * to get into it, so it will be more productive to just skip it on the first check.
		 */
		if ($sFileName != '') {
			// We have a filename - nothing to do here!
		}
		else {
			// We don't have a filename - let's choose one

			if ($this->sFileName != '') {
				// The object has a filename associated - let's use that one
				$sFileName = $this->sFileName;
			}
			else {
				// No filename associated with the object - let's generate one
				$sFileName = time() . '.jpeg';
			}
		}
		$tWidth = $this->aImageProperties["width"];
		$tHeight = $this->aImageProperties["height"];
		$bSuccess = false;
		if ($bgColor != '')
		{
			// Create new image
			$im = imagecreatetruecolor($tWidth, $tHeight);
			
			// Set alphablending to on, we don't want transparency
			imagealphablending($im, true);
			imagealphablending($this->rImage, true);
			
			// Get the background color
			$nColor = imagecolorallocate($im,
										hexdec( substr( $bgColor, 1, 2 ) ), 
										hexdec( substr( $bgColor, 3, 2 ) ), 
										hexdec( substr( $bgColor, 5, 2 ) ));
			// Fill the new image with the background color
			imagefill($im, 0, 0, $nColor);
			
			// copy the original image over the new image
			imagecopy($im, $this->rImage, 0, 0, 0, 0, $tWidth, $tHeight);
			
			// save the new image with background color
			$bSuccess = (bool)imagejpeg($im, $sFileName, $nQuality );
			
			// destroy the image to free memory
			imagedestroy($im);
			
			imagealphablending($this->rImage, false);
		}
		else
		{
			$bSuccess = (bool)imagejpeg($this->rImage, $sFileName, $nQuality );
		}
		
		return $bSuccess;
	}
	
	function savePNG( $sFileName = '' )
	{
		/**
		 * I'll use negative logic in this IF because I don't really expect anybody
		 * to get into it, so it will be more productive to just skip it on the first check.
		 */
		if ($sFileName != '') {
			// We have a filename - nothing to do here!
		}
		else {
			// We don't have a filename - let's choose one

			if ($this->sFileName != '') {
				// The object has a filename associated - let's use that one
				$sFileName = $this->sFileName;
			}
			else {
				// No filename associated with the object - let's generate one
				$sFileName = time() . '.png';
			}
		}
		
		imageSaveAlpha($this->rImage, true);
		$bSuccess = (bool)imagepng($this->rImage, $sFileName );
		
		return $bSuccess;
	}

	
	/**
	 * reduce a truecolor image to palette image
	 *
	 */
	function reduceToPalette()
	{
	}

	/**
	 * save the image as GIF.
	 *
	 * @param string $sFileName
	 * @param BOOL $bDitherFlag
	 * @param BOOL $nColorsWanted
	 * @access public
	 * @return bool
	 */
	 
	function saveGIF( $sFileName = '', $bDitherFlag = false, $nColorsWanted = 256, $sPalette = "default" )
	{
		/**
		 * I'll use negative logic in this IF because I don't really expect anybody
		 * to get into it, so it will be more productive to just skip it on the first check.
		 */
		if ($sFileName != '') {
			// We have a filename - nothing to do here!
		}
		else {
			// We don't have a filename - let's choose one

			if ($this->sFileName != '') {
				// The object has a filename associated - let's use that one
				$sFileName = $this->sFileName;
			}
			else {
				// No filename associated with the object - let's generate one
				$sFileName = time() . '.gif';
			}
		}
		
		imagetruecolortopalette($this->rImage, $bDitherFlag, $nColorsWanted );
		$bSuccess = (bool)imagegif($this->rImage, $sFileName );
		
		return $bSuccess;
	}
}

?>