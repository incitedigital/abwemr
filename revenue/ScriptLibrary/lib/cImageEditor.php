<?php

require_once('class.Logger.php');

$hLogger = new Logger();

define( 'CB_DEBUG_QUIET', false );
define( 'CS_PROJECT_DIR', dirname(__FILE__) );
define( 'CS_LIB_DIR', CS_PROJECT_DIR . DIRECTORY_SEPARATOR . "lib" );
define( 'CS_MESSAGE_FILE_NOT_FOUND', "This file was not found: %s" );
define( 'CS_MESSAGE_FILE_NAME_EMPTY', "Cannot save to empty file name" );
define( 'CS_MESSAGE_FUNCTION_NOT_CALLABLE', "This function is not callable: %s" );
define( 'CS_MESSAGE_FUNCTION_SHOULD_BE_IMPLEMENTED', "This function is abstract and should be implemented in ancestors: %s" );

//this is the interface
class cImageEditor
{
	var $sFileName = "";
	var $rImage = null;
	var $aImageProperties = array();
	var $hExifData = null;

	function cImageEditor( $sFileName = "" )
	{
		@define('CW', 1); // clockwise
		@define('CCW', 0); // counterclockwise

		if( $sFileName != "" )
		{
			$this->sFileName = $sFileName;
		}
	}

	/**
	 * Checks whether a given function is available.
	 *
	 * @param string $sFunctionName
	 * @access public
	 * @static
	 * @return bool
	 * @example call $hImageEditor->queryFunctionality( "blur" ) to check if blur is implemented.
	 * @author Mircho
	 */
	function queryFunctionality( $sFunctionName )
	{
		if( is_callable( array( $this, $sFunctionName ) ) )
		{
			//return $this->$sFunctionName(); // FOR DEVELOPMENT PURPOSES ONLY!
			return true;
		}
		else
		{
			return false;
		}
	}

	function call( $sFunctionName )
	{
		if( is_callable( array( $this, $sFunctionName ) ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function strColorToNumber( $sColor )
	{
		$sColor = strtoupper( $sColor );

		preg_match( "/#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i", $sColor, $aRGB );

		$nColor = 0;
		for( $nI = 1; $nI < count( $aRGB ); $nI++ )
		{
			$aRGB[ $nI ] = ("0x".$aRGB[ $nI ] );
			$aRGB[ $nI ] = base_convert( $aRGB[ $nI ], 16, 10 );
			$nColor =  $nColor | (int)$aRGB[ $nI ] << 8*($nI-1);
		}
		return $nColor;
	}

	function strColorToTriad( $sColor )
	{
		$sColor = strtoupper( $sColor );

		preg_match( "/#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i", $sColor, $aRGB );

		return $aRGB;
	}

	function getWidth()
	{
		return false;
	}

	function getHeight()
	{
		return false;
	}

	function getBitDepth()
	{
		return false;
	}

	/**
	 * Gets the EXIF data from a JPEG ot TIFF image.
	 * Needs PHP compiled with --enable-exif an (optionally) module mbstring
	 * Doesn't need a specific graphic library, so it lives in this class.
	 *
	 * @access public
	 * @return array
	 */
	function getExif()
	{
		if (is_null($this->hExifData)) {
			// No EXIF data loaded into the object - get it, if possible

			if (is_callable( 'exif_read_data' )) {

				$nImageType = exif_imagetype($this->sFileName);

				if ($nImageType == IMAGETYPE_JPEG || $nImageType == IMAGETYPE_TIFF_II || $nImageType == IMAGETYPE_TIFF_MM) {

					$hExifData = exif_read_data($this->sFileName, 'FILE');

					if ($hExifData === false) {
						// No EXIF data present in this image
						return false;
					}
					else {
						// Data gathered - load the important part of it into the object and return it

						$this->hExifData['FileName'] = $hExifData['FileName'];
						$this->hExifData['FileDateTime'] = $hExifData['FileDateTime'];
						$this->hExifData['FileSize'] = $hExifData['FileSize'];
						$this->hExifData['FileType'] = $hExifData['FileType'];
						$this->hExifData['MimeType'] = $hExifData['MimeType'];
						$this->hExifData['COMPUTED'] = $hExifData['COMPUTED'];
						$this->hExifData['Make'] = $hExifData['Make'];
						$this->hExifData['Model'] = $hExifData['Model'];
						$this->hExifData['Orientation'] = $hExifData['Orientation'];
						$this->hExifData['XResolution'] = $hExifData['XResolution'];
						$this->hExifData['YResolution'] = $hExifData['YResolution'];
						$this->hExifData['ResolutionUnit'] = $hExifData[''];
						$this->hExifData['DateTime'] = $hExifData['DateTime'];
						$this->hExifData['YCbCrPositioning'] = $hExifData['YCbCrPositioning'];
						$this->hExifData['Exif_IFD_Pointer'] = $hExifData['Exif_IFD_Pointer'];
						$this->hExifData['THUMBNAIL'] = $hExifData['THUMBNAIL'];
						$this->hExifData['ExposureTime'] = $hExifData['ExposureTime'];
						$this->hExifData['FNumber'] = $hExifData['FNumber'];
						$this->hExifData['ExifVersion'] = $hExifData['ExifVersion'];
						$this->hExifData['DateTimeOriginal'] = $hExifData['DateTimeOriginal'];
						$this->hExifData['DateTimeDigitized'] = $hExifData['DateTimeDigitized'];
						$this->hExifData['ComponentsConfiguration'] = $hExifData['ComponentsConfiguration'];
						$this->hExifData['CompressedBitsPerPixel'] = $hExifData['CompressedBitsPerPixel'];
						$this->hExifData['ShutterSpeedValue'] = $hExifData['ShutterSpeedValue'];
						$this->hExifData['ApertureValue'] = $hExifData['ApertureValue'];
						$this->hExifData['ExposureBiasValue'] = $hExifData['ExposureBiasValue'];
						$this->hExifData['MaxApertureValue'] = $hExifData['MaxApertureValue'];
						$this->hExifData['MeteringMode'] = $hExifData['MeteringMode'];
						$this->hExifData['Flash'] = $hExifData['Flash'];
						$this->hExifData['FocalLength'] = $hExifData['FocalLength'];
						$this->hExifData['FlashPixVersion'] = $hExifData['FlashPixVersion'];
						$this->hExifData['ColorSpace'] = $hExifData['ColorSpace'];
						$this->hExifData['ExifImageWidth'] = $hExifData['ExifImageWidth'];
						$this->hExifData['ExifImageLength'] = $hExifData['ExifImageLength'];
						$this->hExifData['InteroperabilityOffset'] = $hExifData['InteroperabilityOffset'];
						$this->hExifData['FocalPlaneXResolution'] = $hExifData['FocalPlaneXResolution'];
						$this->hExifData['FocalPlaneYResolution'] = $hExifData['FocalPlaneYResolution'];
						$this->hExifData['FocalPlaneResolutionUnit'] = $hExifData['FocalPlaneResolutionUnit'];
						$this->hExifData['SensingMethod'] = $hExifData['SensingMethod'];
						$this->hExifData['FileSource'] = $hExifData['FileSource'];
						$this->hExifData['CustomRendered'] = $hExifData['CustomRendered'];
						$this->hExifData['ExposureMode'] = $hExifData['ExposureMode'];
						$this->hExifData['WhiteBalance'] = $hExifData['WhiteBalance'];
						$this->hExifData['DigitalZoomRatio'] = $hExifData['DigitalZoomRatio'];
						$this->hExifData['SceneCaptureType'] = $hExifData['SceneCaptureType'];
						$this->hExifData['InterOperabilityIndex'] = $hExifData['InterOperabilityIndex'];
						$this->hExifData['InterOperabilityVersion'] = $hExifData['InterOperabilityVersion'];
						$this->hExifData['RelatedImageWidth'] = $hExifData['RelatedImageWidth'];
						$this->hExifData['RelatedImageHeight'] = $hExifData['RelatedImageHeight'];
						$this->hExifData['ImageType'] = $hExifData['ImageType'];
						$this->hExifData['FirmwareVersion'] = $hExifData['FirmwareVersion'];

						return $this->hExifData;
					}
				}
				else {
					// Image type does not support EXIF
					return false;
				}
			}
			else {
				// There is no EXIF support on this system
				return false;
			}
		}
		else {
			// Data is loaded into the object - just return it
			return $this->hExifData;
		}
	}

	/**
	 * Resizes an image to given dimensions.
	 *
	 * @abstract
	 * @access public
	 * @param int $nWidth
	 * @param int $nHeight
	 * @param bool $bKeepAspectRatio
	 * @return bool
	 */
	function resize($nWidth, $nHeight, $bKeepAspectRatio = false, $bResizeUp = false)
	{
		return false;
	}

	function rotate( $nDegrees, $bDirectionClockwise = true, $nBgColor = '', $nIgnoreTransparent = 0 )
	{
		return false;
	}

	function blur()
	{
		return false;
	}

	function sharpen()
	{
		return false;
	}

	function grayscale()
	{
		return false;
	}

	/* FIXIT: fix text implementation */
	function text( $sPosition, $sText, $nFontSize = 24, $sFontStyle = 'normal', $sFontColor = 'ffffff' )
	{
		return false;
	}

	function watermark( $rImage )
	{
		return false;
	}

	function flip( $bVerticalFlip = true) // Flip directions may be defined into external constants to help auto-complete.
	{
		return false;

	}

	function ConvertBMP2GD($src, $dest = false) {
	//Convert BMP -> GD
	if(!($src_f = fopen($src, "rb"))) {
		 $this->debug("Cannot open the source. <font color=\"#FF0000\"><b>".$src."</b></font><br/>");
		 return false;
	}
	if(!($dest_f = fopen($dest, "wb"))) {
		$this->debug("Cannot open the destination for writing <font color=\"#FF0000\"><b>".dest."</b></font><br/>");
		return false;
	}
	$header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,
	14));
	$info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
	fread($src_f, 40));

	extract($info);
	extract($header);

	if($type != 0x4D42) { // signature "BM"
		$this->debug("The file is from wrong format. <font color=\"#FF0000\"><b>Does not contain BMP signature!</b></font><br/>");
		return false;
	}

	$palette_size = $offset - 54;
	$ncolor = $palette_size / 4;
	$gd_header = "";
	// true-color vs. palette
	$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
	$gd_header .= pack("n2", $width, $height);
	$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
	if($palette_size) {
		$gd_header .= pack("n", $ncolor);
	}
	// no transparency
	$gd_header .= "\xFF\xFF\xFF\xFF";

	fwrite($dest_f, $gd_header);

	if($palette_size) {
		$palette = fread($src_f, $palette_size);
		$gd_palette = "";
		$j = 0;
		while($j < $palette_size) {
			$b = $palette{$j++};
			$g = $palette{$j++};
			$r = $palette{$j++};
			$a = $palette{$j++};
			$gd_palette .= "$r$g$b$a";
		}
		$gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
		fwrite($dest_f, $gd_palette);
	}

	$scan_line_size = (($bits * $width) + 7) >> 3;
	$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size &
	0x03) : 0;

	for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
		// BMP stores scan lines starting from bottom
		fseek($src_f, $offset + (($scan_line_size + $scan_line_align) *
		$l));
		$scan_line = fread($src_f, $scan_line_size);
		if($bits == 24) {
			$gd_scan_line = "";
			$j = 0;
			while($j < $scan_line_size) {
				$b = $scan_line{$j++};
				$g = $scan_line{$j++};
				$r = $scan_line{$j++};
				$gd_scan_line .= "\x00$r$g$b";
			}
		}
		else if($bits == 8) {
			$gd_scan_line = $scan_line;
		}
		else if($bits == 4) {
			$gd_scan_line = "";
			$j = 0;
			while($j < $scan_line_size) {
				$byte = ord($scan_line{$j++});
				$p1 = chr($byte >> 4);
				$p2 = chr($byte & 0x0F);
				$gd_scan_line .= "$p1$p2";
			} $gd_scan_line = substr($gd_scan_line, 0, $width);
		}
		else if($bits == 1) {
			$gd_scan_line = "";
			$j = 0;
			while($j < $scan_line_size) {
				$byte = ord($scan_line{$j++});
				$p1 = chr((int) (($byte & 0x80) != 0));
				$p2 = chr((int) (($byte & 0x40) != 0));
				$p3 = chr((int) (($byte & 0x20) != 0));
				$p4 = chr((int) (($byte & 0x10) != 0));
				$p5 = chr((int) (($byte & 0x08) != 0));
				$p6 = chr((int) (($byte & 0x04) != 0));
				$p7 = chr((int) (($byte & 0x02) != 0));
				$p8 = chr((int) (($byte & 0x01) != 0));
				$gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
			} $gd_scan_line = substr($gd_scan_line, 0, $width);
		}

		fwrite($dest_f, $gd_scan_line);
	}

	fclose($src_f);
	fclose($dest_f);
	return true;
  }

  function imagecreatefrombmp($filename) {
	//Create image from BMP
	// $this->debug("Execute function: <b>imagecreatefrombmp</b><br/>");
	$tmp_name = tempnam("/tmp", "GD");
	// $this->debug("Filename: <b>".$filename."</b><br/>");
	// $this->debug("Tempfilename: <b>".$tmp_name."</b><br/>");
	if($this->ConvertBMP2GD($filename, $tmp_name)) {
		$img = imagecreatefromgd($tmp_name);
		unlink($tmp_name);
		// $this->debug("ConvertBMP2GD successful execution");
		return $img;
	}
	 $this->debug("<font color=\"#FF0000\"><b>ConvertBMP2GD</b></font> failed!");
	return false;
  }

	function loadImageFromFile( $sFileName = "" )
	{
		$sFileName = $sFileName == "" ? $this->sFileName : $sFileName;
		$this->sFileName = $sFileName;
		if( $sFileName == "" || !$this->fileExists( $this->sFileName ) )
		{
			$this->debug( sprintf( CS_MESSAGE_FILE_NOT_FOUND, $this->sFileName ) );
		}
		else
		{
			$sFunctionName = $this->getImageLoaderHandler( $this->sFileName );
			if( $sFunctionName != "" && is_callable( $sFunctionName ) )
			{
				$this->rImage = $sFunctionName( $this->sFileName );

				// $this->rImage is a resource and will be evaluated as TRUE
				return (bool)$this->rImage;
			}
			else
			{
				if ( "imagecreatefrombmp" == $this->getImageLoaderHandler( $this->sFileName ))
				{
					// $this->debug("function <font color=\"#0000FF\"><b>loadImageFrom</b></font> call: imagecreatefrombmp <br/>");
					$this->rImage = $this->imagecreatefrombmp($this->sFileName);
					return (bool)$this->rImage;
				}

				$this->debug( sprintf( CS_MESSAGE_FUNCTION_NOT_CALLABLE, $sFunctionName ) );
				return false;
			}
		}
	}

	/**
	 * Returns true if the file exists.
	 *
	 * @access public
	 * @return bool
	 */
	function fileExists()
	{
		return file_exists( $this->sFileName );
	}

	function saveImage( $sFileName )
	{
		$sFileName = $sFileName == "" ? $this->sFileName : $sFileName;
		$this->sFileName = $sFileName;
		if( $sFileName == "" )
		{
			$this->debug( CS_MESSAGE_FILE_NAME_EMPTY );
		}
		else
		{
			$sFunctionName = $this->getImageSaverHandler( $sFileName );
			if( $sFunctionName != "" && is_callable( $sFunctionName ) )
			{
				$bSuccess = (bool)$sFunctionName( $this->rImage, $sFileName );
				return $bSuccess;
			}
			else
			{
				$this->debug( sprintf( CS_MESSAGE_FUNCTION_NOT_CALLABLE, $sFunctionName ) );
				return false;
			}
		}
	}

	//this is abstract, should be implemented in the ancestor
	function getImageLoaderHandler( $sFileName )
	{
		$this->debug( sprintf( CS_MESSAGE_FUNCTION_SHOULD_BE_IMPLEMENTED, __FUNCTION__ ) );
	}

	//this is abstract, should be implemented in the ancestor
	function getImageSaverHandler( $sFileName )
	{
		$this->debug( sprintf( CS_MESSAGE_FUNCTION_SHOULD_BE_IMPLEMENTED, __FUNCTION__ ) );
	}

	function debug( $sMessage )
	{
		global $hLogger;
		if( $hLogger || CB_DEBUG_QUIET )
		{
			$hLogger->logMessage( $sMessage );
		}
		else
		{
			if( !CB_DEBUG_QUIET )
			{
				echo $sMessage."<br/>";
			}
		}
	}

	function outputJpeg( $nQuality = 75, $bPrintHeaders = false )
	{
		return false;
	}

	function outputPNG( $bPrintHeaders = false )
	{
		return false;
	}

	function saveJpeg( $nQuality = 75, $sFileName = "" )
	{
		return false;
	}

	function savePNG( $sFileName = "" )
	{
		return false;
	}

	function saveGIF( $sFileName = "", $bDitherFlag = false, $nColorsWanted = 0 )
	{
		return false;
	}

	function revertAllChanges()
	{
		$this->loadImageFromFile();
		return true;
	}
}

?>