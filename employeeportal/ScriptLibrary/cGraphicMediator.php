<?php

define( 'CS_ROOT_DIR', dirname(__FILE__) );

/**
 * @todo Update the needed Pure PHP Upload version number -> $nNeededPPUVersion
 */
class cGraphicMediator
{
	# Configuration
	var $version = "2.1.1";
	var $nNeededPPUVersion = "2.1.3"; // PPU stands for Pure PHP Uploader
	var $debugger = false;
	/**
	 * @var object pureFileUpload()
	 */
	var $upload = null;

	# Private variable for the list of files which need to be processed
	var $aQueue = array();
	var $rResource;
	var $sRecordsetField = ''; // This is used only if the resource is a recordset!
	var $sMethod = "";//upload, recordset, folder, file
	var $sUploadFileds = '';

	# The object
	var $rImageEditor;

	# Just to know
	var $sGraphicLib = '';

	# Operations and options
	var $bRotate = false;
	var $bRotateClockwise = true; // default rotation direction
	var $nRotateDegrees;
	var $nRotateFillColor = '#000000'; // black

	var $bResize = false;
	var $bKeepAspect = true;
	var $nResizeWidth;
	var $nResizeHeight;

	var $bBlur = false;
	var $nBlurStrength = 1.0; // Does not yet work with GD2.
	var $bGaussianBlur = false; // The default blur is standard, not gaussian.

	var $bSharpen = false;
	var $nSharpenStrength = 1.0; // Does not yet work with GD2.

	var $bGrayscale = false;

	var $bText = false;
	var $sText = '';
	var $nTextPosX;
	var $nTextPosY;
	var $nTextFontSize = 24;
	var $sTextFontStyle = 'normal'; // ???
	var $sTextFontColor = '#ffffff'; // black
	var $sTextFontFace = 'serif'; // ???
	var $bTextBold = false;
	var $bTextItalic = false;
	var $bTextUnderline = false;

	var $bCropPos = false;
	var $bCropToFit = false;
	var $sCropPosition = 'Center-Center';
	var $nCropWidth;
	var $nCropHeight;

	var $bWatermark = false;
	var $sWatermarkFileName = '';
	var $sWatermarkPosition = 'tr'; // top right
	var $nWatermarkMargin = 0;
	var $nWatermarkOpacity = 100;
	var $nWatermarkScaleToPercent = 0; // if left "0" no scaling will be done
	var $bShrinkToFit = false;
	var $nTransColor = '';

	var $bTiledWatermark = false;
	var $sTiledWatermarkFileName = '';
	var $nTiledWatermarkHSpace = 0;
	var $nTiledWatermarkVSpace = 0;
	var $nTiledWatermarkTransColor = 0;
	var $nTiledWatermarkOpacity = 100;

	var $bFlip = false;
	var $bFlipVertical = false;

	var $bOutputJpeg = false;
	var $bOutputJpegHeaders = false;
	var $nOutputJpegQuality = 75;

	var $bOutputPNG = false;
	var $bOutputPNGHeaders = false;

	var $bSaveAuto = false;

	var $bSavePNG = false;

	var $bSaveGIF = false;
	var $sSaveGIFPalette = 'Adaptive';
	var $bSaveGIFDitherFlag = false;
	var $nSaveGIFColorsWanted = 256;

	var $bSaveJpeg = false;
	var $nSaveJpegQuality = 75;

	var $bMask = false;
	var $sMask = '';

	var $matteColor = ''; // not set

	var $overwrite = false;



	function cGraphicMediator( $sMethod, $rResource, $sRecordesetFiled = '' )
	{
		global $DMX_debug;

		if ( strtolower($sMethod) == 'upload' )
		{
			$this->upload = &$rResource;

			//removed pass by reference
			//$this->upload->registerAddOn( &$this );
			$this->upload->registerAddOn( $this );

			if (version_compare($this->upload->version, $this->nNeededPPUVersion, '<'))
			{
				$this->error("uploadversion", $this->nNeededPPUVersion);
			}
		}

		# Pre-instantiate the Image Editor.
		# If a specific Image Editor is needed it will be instantiated again.
		# This is a rather ugly and slow solution but it solves the problem at hand.
		$this->component();

		$this->sMethod = $sMethod;
		$this->rResource = &$rResource;
		$this->sRecordsetField = $sRecordesetFiled;

		$this->debugger = $DMX_debug;
		$this->debug("<br/><font color=\"#009900\"><b>Smart Image Processor version ".$this->version."</b></font><br/><br/>");
	}

	function setMatteColor( $sColor = '' )
	{
		$this->matteColor = $sColor;
	}

	function setComponent( $sForcedLib = 'auto' )
	{
		return $this->component( $sForcedLib );
	}

	function component( $sForcedLib = 'auto' )
	{
		$sLib = null;

		if ( strtolower( $sForcedLib ) == 'auto' )
		{
			//if ( is_callable( 'imagelayereffect' ) )
			if ( is_callable( 'imagealphablending' ) )
			{
				$sLib = 'gd2';
			}
			elseif ( is_callable( 'imagick_begindraw' ) )
			{
				$sLib = 'imagick';
			}
			elseif ( is_callable( 'SOME_NETPBM_FUNCTION' ) ) // This is dummy!!!
			{
				$sLib = 'netpbm';
			}
			elseif ( is_callable( 'imagecreate' ) )
			{
				$sLib = 'gd';
			}
		}
		else
		{
			switch ( strtolower( $sForcedLib ) ) {
				case 'gd':
					if ( is_callable( 'imagecreate' ) ) { $sLib = 'gd'; }
					break;

				case 'gd2':
					if ( is_callable( 'imagelayereffect' ) ) { $sLib = 'gd2'; }
					break;

				case 'imagick':
				case 'imagemagick':
				case 'image magick':
					if ( is_callable( 'imagick_begindraw' ) ) { $sLib = 'imagick'; }
					break;

				case 'netpbm':
					if ( is_callable( 'SOME_NETPBM_FUNCTION' ) ) { $sLib = 'netpbm'; }
					break;
			}
		}

		# Now do the real job:
		switch ( $sLib )
		{
			case 'gd':
				# GD 1.x.x detected!
				require_once('lib/cImageEditor/cGDImageEditor.php');

				$this->rImageEditor = new cGDImageEditor();
				$this->sGraphicLib = 'GD1';
				break;

			case 'gd2':
				# GD 2.0.1 or better detected!
				require_once('lib/cImageEditor/cGD2ImageEditor.php');

				$this->rImageEditor = new cGD2ImageEditor();
				$this->sGraphicLib = 'GD2';
				break;

			case 'imagick':
				# Image Magick detected!
				require_once('lib/cImageEditor/cImageMagickImageEditor.php');

				$this->rImageEditor = new cImageMagickImageEditor();
				$this->sGraphicLib = 'ImageMagick';
				break;

			case 'netpbm':
				# NetPBM detected!
				require_once('lib/cImageEditor/cNetPBMImageEditor.php');

				$this->rImageEditor = new cNetPBMImageEditor();
				$this->sGraphicLib = 'NetPBM';
				break;

			case null:
			default:
				# No graphic library detected!
				trigger_error( 'Error with graphic library! Forced library not installed or no graphic library installed!', E_USER_ERROR );
				break;
		}

		return true;
	}

	/**
	 * This function checks whether we have to work on a file or a whole directory.
	 * If it's a directory it finds the graphic files and calls processImage() for
	 * each of them.
	 *
	 * @uses processImage()
	 *
	 * @return bool
	 */
	function process(){

		# Prepare queue
		switch ($this->sMethod) {
			case 'file':
				$this->prepareQueueFromFile( $this->rResource );
				break;
			case 'folder':
				$this->prepareQueueFromDirectory( $this->rResource );
				break;
			case 'upload':
				$this->prepareQueueFromUpload( $this->rResource, $this->sRecordsetField );
				break;
			case 'recordset':
				$this->prepareQueueFromRecordset( $this->rResource, $this->sRecordsetField );
				break;

			default:
				return false;
		}

		# Process queue
		foreach ($this->aQueue as $file) {
			$bSuccess = $this->processImage( $file );

			if (!$bSuccess) {
				$aErrors[]['filename'] = $file;

			}
		}

		if (empty($aErrors)) {
			// No errors - it's all ok

			return true;
		}
		else {
			// Here we can do something with the files we got errors on
			$this->debug($aErrors);
			return false;
		}
	}

	function __checkFailed( $bSuccess ){
		global $bStrict, $bSuccessInAll;

		if (!$bSuccess) {
			$bSuccessInAll = false;

			if ($bStrict) {
				$this->rImageEditor->revertAllChanges();
				return false;
			}
		}
	} /* end __checkFailed() */

	/**
	 * This is the main function that actually does the job of executing
	 * members of SIPP2 so that the needed graphic transformations can be done.
	 * Works on a single image image!
	 *
	 * @param $sImageFileName
	 * @param $bStrict If TRUE the function will revert all actions and return FALSE
	 * if an error occurs. Otherwise it will just return FALSE to indicate that some
	 * of the operations weren't completed successfully.
	 * @return bool
	 */
	function processImage( $sImageFileName, $bStrict = false ){

		/**
		 * This is out flag whether ALL the operations went well.
		 */
		$bSuccessInAll = true;

		if (!is_callable('__checkFailed')) {
			/**
			 * Pure private function - will be used just here.
			 * That's why it's defined here and not out there in the class.
			 * (Note: Yes, I realise it will be accessible in the same way as the others.)
			 */
			function __checkFailed( $bSuccess ){
				global $bStrict, $bSuccessInAll;

				if (!$bSuccess) {
					$bSuccessInAll = false;

					if ($bStrict) {
						$this->rImageEditor->revertAllChanges();
						return false;
					}
				}
			} /* end __checkFailed() */
		}

		// @todo /* BAD! BAD! BAD! */$this->debug( var_export( $this ) ); // This is just alpha!!! Write something decent here!

		if ( is_file( $sImageFileName ) )
		{
			$this->rImageEditor->loadImageFromFile( $sImageFileName );
		}
		else
		{
			return false;
		}


		if ( $this->bRotate )
		{
			$bSuccess = $this->rImageEditor->rotate( $this->nRotateDegrees, $this->bRotateClockwise, $this->nRotateFillColor, 0);

			# Write the size changes back in the uploader object
			if ($this->overwrite) {
				$this->setNewDimensionsInUpload( $this->rImageEditor->getWidth(), $this->rImageEditor->getHeight() );
			}

			$this->__checkFailed( $bSuccess );
		}

		if ( $this->bResize )
		{
			if ($this->bCropToFit) {
				$nCropWidth = 10000;
				$nCropHeight = 10000;

				if ($this->nResizeWidth/$this->rImageEditor->getWidth() < $this->nResizeHeight/$this->rImageEditor->getHeight()) {
					$nCropHeight = $this->nResizeHeight;
				} else {
					$nCropWidth = $this->nResizeWidth;
				}
				$bSuccess = $this->rImageEditor->resize( $nCropWidth, $nCropHeight, true );
				$bSuccess = $this->rImageEditor->cropPos( 'cc', $this->nResizeWidth, $this->nResizeHeight );

			} else {
				$bSuccess = $this->rImageEditor->resize( $this->nResizeWidth, $this->nResizeHeight, $this->bKeepAspect );
			}

			# Write the size changes back in the uploader object
			if ($this->overwrite) {
				$this->setNewDimensionsInUpload( $this->rImageEditor->getWidth(), $this->rImageEditor->getHeight() );
			}

			$this->__checkFailed( $bSuccess );
		}

		if( $this->bCropPos )
		{

			$bSuccess = $this->rImageEditor->cropPos( $this->sCropPosition, $this->nCropWidth, $this->nCropHeight );

			# Write the size changes back in the uploader object
			if ($this->overwrite) {
				$this->setNewDimensionsInUpload( $this->rImageEditor->getWidth(), $this->rImageEditor->getHeight() );
			}

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bBlur) {
			$bSuccess = $this->rImageEditor->blur( $this->bGaussianBlur, $this->nBlurStrength );

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bSharpen) {
			$bSuccess = $this->rImageEditor->sharpen( $this->nSharpenStrength );

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bGrayscale) {
			$bSuccess = $this->rImageEditor->grayscale();

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bText) {
			/*
			*FIXIT:
			*/
			/*
			$bSuccess = $this->rImageEditor->textabs(	$this->nTextPosX,
													$this->nTextPosY,
													$this->sText,
													$this->nTextFontSize,
													$this->sTextFontStyle,
													$this->sTextFontColor,
													$this->sTextFontFace
												);
			*/
			$bSuccess = $this->rImageEditor->text(	$this->sTextPosition,
													$this->sText,
													$this->nTextFontSize,
													$this->sTextFontStyle,
													$this->sTextFontColor,
													$this->sTextFontFace
												);
			$this->__checkFailed( $bSuccess );
		}

		if ($this->bWatermark) {
			$bSuccess = $this->rImageEditor->watermark(	$this->sWatermarkFileName,
														$this->sWatermarkPosition,
														$this->nWatermarkMargin,
														$this->nWatermarkOpacity,
														$this->nWatermarkScaleToPercent,
														$this->nTransColor
													);

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bTiledWatermark) {
			$bSuccess = $this->rImageEditor->tiledWatermark($this->sTiledWatermarkFileName,
															$this->nTiledWatermarkHSpace,
															$this->nTiledWatermarkVSpace,
															$this->nTiledWatermarkTransColor,
															$this->nTiledWatermarkOpacity
													);

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bFlip) {
			$bSuccess = $this->rImageEditor->flip( $this->bFlipVertical );

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bOutputJpeg) {
			$bSuccess = $this->rImageEditor->outputJpeg( $this->nOutputJpegQuality, $this->bOutputJpegHeaders );

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bOutputPNG) {
			$bSuccess = $this->rImageEditor->outputPNG( $this->bOutputPNGHeaders );

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bSaveAuto) {
			$this->bSaveJpeg = $this->bSavePNG = $this->bSaveGIF = false;

			switch (strtolower($this->getExt($this->rImageEditor->sFileName))) {
				case 'png':
					$this->bSavePNG = true;
					break;
				case 'gif':
					$this->bSaveGIF = true;
					break;
				default:
					$this->bSaveJpeg = true;
			}
		}

		//edit here
		if ($this->bSaveJpeg) {
			if ($this->overwrite) {
				$currExt = substr($this->rImageEditor->sFileName, strrpos($this->rImageEditor->sFileName,'.') + 1);
				if((strtolower($currExt) == "jpg") || (strtolower($currExt) == "jpeg"))
				{
					$newExt = $currExt;
				}
				else
				{
					$newExt = "jpg";
				}
				//, '\.(jpg|jpeg)$'
				$sNewName = $this->switchExt($this->rImageEditor->sFileName, $newExt);
				$this->setNewFileNameInUpload($sNewName);
				if ($this->matteColor != '')
				{
					$bSuccess = $this->rImageEditor->saveJpeg( $sNewName, $this->nSaveJpegQuality, $this->matteColor );
				}
				else
				{
					$bSuccess = $this->rImageEditor->saveJpeg( $sNewName, $this->nSaveJpegQuality );
				}
				if ($sNewName != $this->rImageEditor->sFileName) {
					@unlink($this->rImageEditor->sFileName);
					$this->rImageEditor->sFileName = $sNewName;
				}
			}
			else {

				$currExt = substr($this->rImageEditor->sFileName, strrpos($this->rImageEditor->sFileName,'.') + 1);
				//------------------Hier Background  check + setten--------------------
				if((strtolower($currExt) == "jpg") || (strtolower($currExt) == "jpeg"))
				{
					$newExt = $currExt;
					//echo "NewExt: ".$newExt;
				}
				else
				{
					$newExt = "jpg";
				}
				//, '\.(jpg|jpeg)$'
				$maskedFile = $this->applyMask( $this->rImageEditor->sFileName );
				//echo "FIle: ".$maskedFile;
				$sNewName = $this->switchExt( $maskedFile, $newExt);
				//$bSuccess = $this->rImageEditor->saveJpeg( $sNewName, $this->nSaveJpegQuality );
				if ($this->matteColor != '')
				{
					$bSuccess = $this->rImageEditor->saveJpeg( $sNewName, $this->nSaveJpegQuality, $this->matteColor );
				}
				else
				{
					$bSuccess = $this->rImageEditor->saveJpeg( $sNewName, $this->nSaveJpegQuality );
				}
			}

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bSavePNG) {

			if ($this->overwrite) {
				$currExt = substr($this->rImageEditor->sFileName, strrpos($this->rImageEditor->sFileName,'.') + 1);
				if(strtolower($currExt) == "png")
				{
					$newExt = $currExt;
				}
				else
				{
					$newExt = "png";
				}
				$sNewName = $this->switchExt($this->rImageEditor->sFileName, $newExt );
				$this->setNewFileNameInUpload($sNewName);
				$bSuccess = $this->rImageEditor->savePNG( $sNewName );
				if ($sNewName != $this->rImageEditor->sFileName) {
					@unlink($this->rImageEditor->sFileName);
					$this->rImageEditor->sFileName = $sNewName;
				}
			}
			else {
				$currExt = substr($this->rImageEditor->sFileName, strrpos($this->rImageEditor->sFileName,'.') + 1);
				if(strtolower($currExt) == "png")
				{
					$newExt = $currExt;
				}
				else
				{
					$newExt = "png";
				}
				$sNewName = $this->switchExt( $this->applyMask( $this->rImageEditor->sFileName ), $newExt );
				$bSuccess = $this->rImageEditor->savePNG( $sNewName );
			}

			$this->__checkFailed( $bSuccess );
		}

		if ($this->bSaveGIF) {

			if ($this->overwrite) {
				$currExt = substr($this->rImageEditor->sFileName, strrpos($this->rImageEditor->sFileName,'.') + 1);
				if(strtolower($currExt) == "gif")
				{
					$newExt = $currExt;
				}
				else
				{
					$newExt = "gif";
				}
				$sNewName = $this->switchExt($this->rImageEditor->sFileName, $newExt );
				$this->setNewFileNameInUpload($sNewName);
				$bSuccess = $this->rImageEditor->saveGIF(	$sNewName,
															$this->bSaveGIFDitherFlag,
															$this->nSaveGIFColorsWanted,
															$this->sSaveGIFPalette
														);
				if ($sNewName != $this->rImageEditor->sFileName) {
					@unlink($this->rImageEditor->sFileName);
					$this->rImageEditor->sFileName = $sNewName;
				}
			}
			else {
				$currExt = substr($this->rImageEditor->sFileName, strrpos($this->rImageEditor->sFileName,'.') + 1);
				if(strtolower($currExt) == "gif")
				{
					$newExt = $currExt;
				}
				else
				{
					$newExt = "gif";
				}
				$sNewName = $this->switchExt( $this->applyMask( $this->rImageEditor->sFileName ), $newExt );
				$bSuccess = $this->rImageEditor->saveGIF(	$sNewName,
															$this->bSaveGIFDitherFlag,
															$this->nSaveGIFColorsWanted,
															$this->sSaveGIFPalette
														);
			}

			$this->__checkFailed( $bSuccess );
		}


		return $bSuccessInAll;
	}

	/**
	 * Prepares an array, each element of which contains a string.
	 * This string is the path to the file that needs processing.
	 * If it is a single file the array will have just one element.
	 * The array is kept in the private variable $this->aQueue;
	 *
	 * @param string $sFile
	 * @return bool
	 */
	function prepareQueueFromFile( &$sFile ){

		$this->aQueue = array($sFile);

		return true;
	}

	/**
	 * Prepares an array, each element of which contains a string.
	 * This string is the path to the file that needs processing.
	 * If it is a single file the array will have just one element.
	 * The array is kept in the private variable $this->aQueue;
	 *
	 * @param string $sPath
	 * @return bool
	 * @author Mircho
	 */
	function prepareQueueFromDirectory( &$sPath ){
		if( is_dir( $sPath ) )
		{
			require_once( "lib/config.filebrowser.php" );
			//define( CS_PROTECTED_DIR_NAMES, "" );
			if	(	substr($sPath, 0, 1) === '/'
				||	substr($sPath, 1, 2) === ':\\'
				||	substr($sPath, 0, 2) === './'
				||	substr($sPath, 0, 3) === '../'
				)
			{
				@define( 'CS_START_DIR', "" ); // a little redefine here
			}
			else {
				@define( 'CS_START_DIR', "./" ); // a little redefine here
			}

			require_once( "lib/class.filebrowser2.php" );
			$hFB = new cFileBrowser2( "", false );
			$hObjects = &$hFB->getObjectList( $sPath );
			$hFiles = $hObjects[ "files" ];
			$hDirs = $hObjects[ "folders" ];

			foreach ($hFiles as $hFile) {

				# Filter files through a whitelist:
				if ( $hFile['type'] & CN_FILE_TYPE_IMAGE ) {
					# This file type is OK!
				}
				else {
					# Other file types are not OK -> skip this!
					continue;
				}

				$this->aQueue[] = $hFile['path'];
			}
		}
	}

	/**
	 * Prepares an array, each element of which contains a string.
	 * This string is the path to the file that needs processing.
	 * If it is a single file the array will have just one element.
	 * The array is kept in the private variable $this->aQueue;
	 *
	 * @param pureFileUpload $rUpload
	 * @param string $sField
	 * @return bool
	 */
	function prepareQueueFromUpload( &$rUpload, $sField ){

		if (@is_a( $rUpload, 'pureFileUpload' )) {
			if (!isset($mm_abort_edit) || !$mm_abort_edit) {
				// loop through all upload fields
				foreach ($rUpload->uploadedFiles as $file) {
					// check if a file was uploaded and is the correct field
					if (!empty($file->fileName) && ($sField == '' || $file->field == $sField)) {
						// do we have an image, try getimagesize (returns FALSE if no image)
						if ($imageSize = @getimagesize($file->filePath.'/'.$file->fileName)) {
							// extra check to see if image has dimensions
							if ($imageSize[0] > 0 && $imageSize[1] > 0) {
								$this->aQueue[] = $file->filePath.'/'.$file->fileName;
							}
						}
					}
				}
			}

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Prepares an array, each element of which contains a string.
	 * This string is the path to the file that needs processing.
	 * If it is a single file the array will have just one element.
	 * The array is kept in the private variable $this->aQueue;
	 *
	 * @param resource $rRecordset
	 * @param string $sRecordsetField
	 * @return bool
	 */
	function prepareQueueFromRecordset( &$rRecordset, $sRecordsetField )
	{
		if( mysql_num_rows( $rRecordset ) != 0 )
		{
			mysql_data_seek ( $rRecordset, 0 );
			while ( $aFileData = mysql_fetch_assoc( $rRecordset ) )
			{
				$this->aQueue[] = $aFileData[ $sRecordsetField ];
			}

			//mysql_errno( $rRecordset )
			if ( mysql_errno( ) )
			{
				// Revert -> empty the queue
				$this->aQueue = array();
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			$this->aQueue = array();
			return false;
		}
	}


	/**
	 * Checks if the supplied version number is equal ot better than
	 * the current version of this class.
	 *
	 * @uses compareVersions()
	 * @param string $sVersion Version written with separating dots
	 * @param bool $bIssueAnError Whether to print out an error message or not.
	 * @return bool
	 */
	function checkVersion( $sVersion, $bIssueAnError = true ) {
		$nCompare = $this->compareVersions( $sVersion, $this->version );

		if ( $nCompare == '-1') {
			# version is older than needed
			if ($bIssueAnError) {
				$this->error('version');
			}
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * NOTE: This function is good to be in some general lib...
	 */
	/**
	 * Checks if the supplied 'current' version number is equal ot better than
	 * the supplied 'test' version. Version numbers may contain separating dots.
	 *
	 * @param mixed $mCurrentVersion
	 * @param mixed $mTestVersion
	 * @return int Returns 1 if test > current, 0 when equal and -1 if test < current.
	 */
	function compareVersions( $mTestVersion, $mCurrentVersion ){

/**
 *  @todo napravi sravnenie po dekadi s explode!	*/


		$nTestVer = intval( str_replace( "." , "", $mTestVersion ) );
		$nCurrentVer = intval( str_replace( ".", "", $mCurrentVersion ) );

		if ($nTestVer > $nCurrentVer) {
			return 1;
		}
		elseif ($nTestVer = $nCurrentVer) {
			return 0;
		}
		else {
			return -1;
		}
	}

	/**
	 * Just an interface to the built-in version_compare().
	 * Left here for backward compatibility.
	 *
	 * @param string $version A 'PHP Stadartized' version number.
	 * @return bool
	 */
	function check_php_version($version) {

		return version_compare( $version, phpversion(), '>=');
	}

	function error($error, $extra = "") {

		// Display error
		echo "<b>Error occurred in the Smart Image Processor</b><br/><br/>";

		switch ($error) {
			// Incorrect version
			case 'version':
				echo "<b>You don't have latest version of Smart Image Processor uploaded on the server.</b><br/>";
				echo "That is required for the current page.<br/>";
				break;

			// Needs newer version of Pure PHP Upload
			case 'uploadversion':
				echo "This version of Smart Image Processor requires version ".$extra." or better of Pure PHP Upload<br/>";
				break;

			// Error renaming the file
			case 'invalid':
				echo "The uploaded file is not in a valid image format that is supported<br/>";
				break;

			// Error with netpbm
			case 'netpbm':
				echo "There is an error occurred with the NetPBM Component<br/>";
				break;

			// Error with image magick
			case 'imagick':
				echo "A problem with Image Magick library occurred<br/>";
				break;

			// Error renaming the file
			case 'gdinvalid':
				echo "The GD Library that is installed does not support ".$extra."<br/>";
				break;

			// GD Library is not found
			case 'gdinstall':
				echo "The GD Library is not installed correctly<br/>";
				break;
		}

		// Allow to go back and stop the script
		echo "Please correct and <a href=\"javascript:history.back(1)\">try again</a>";
		$this->upload->failUpload();
		exit(1);
	}

	/**
	 * Left here for backward compatibility.
	 *
	 * @return array
	 */
	function gd_info() {
		return gd_info();
	}

	/**
	 * @todo We should think how to optimise debugging.
	 *
	 * @param mixed $info
	 */
	function debug($info) {
		if ($this->debugger) {
			if (is_array($info)) {
				echo "<pre>\n";
				print_r($info);
				echo "</pre>\n";
			}
			else {
				echo "<font face=\"verdana\" size=\"2\">".$info."</font>";
			}
		}
	}

	function cleanUp() {

		foreach ($this->upload->uploadedFiles as $file) {
			$fileName = $file->name.$this->suffix.".jpg";
			$this->debug("<font color=\"#FF0000\"><b>Deleting ".$fileName."</b></font><br/>");
			@unlink($filename);
		}
	}

	/**
	 * INTERFACE FUNCTIONS
	 *
	 * These are just setters for the members. They are here for backward compatibility with the ASP version.
	 */

	function rotateLeft( $nRotateDegrees = 90, $nRotateFillColor = '#000000' )
	{
		$this->bRotate = true;
		$this->bRotateClockwise = true;
		$this->nRotateDegrees = $nRotateDegrees;
		$this->nRotateFillColor = $nRotateFillColor;

		return true;
	}

	function rotateRight( $nRotateDegrees = 90, $nRotateFillColor = '#000000' )
	{
		$this->bRotate = true;
		$this->bRotateClockwise = false;
		$this->nRotateDegrees = $nRotateDegrees;
		$this->nRotateFillColor = $nRotateFillColor;

		return true;
	}

	function resize( $nResizeWidth, $nResizeHeight, $bKeepAspect = false )
	{
		$this->bResize = true;
		$this->nResizeWidth = $nResizeWidth;
		$this->nResizeHeight = $nResizeHeight;
		$this->bKeepAspect = $bKeepAspect;

		return true;
	}

	function resizeEx( $nResizeWidth, $nResizeHeight, $bKeepAspect = false, $bCropToFit = false )
	{
		$this->bResize = true;
		$this->nResizeWidth = $nResizeWidth;
		$this->nResizeHeight = $nResizeHeight;
		$this->bKeepAspect = $bKeepAspect;
		$this->bCropToFit = $bCropToFit;

		return true;
	}


	function blur( $nBlurStrength = 1.0, $bGaussianBlur = false )
	{
		$this->bBlur = true;
		$this->nBlurStrength = $nBlurStrength;
		$this->bGaussianBlur = $bGaussianBlur;

		return true;
	}

	function sharpen( $nSharpenStrength = 1.0 )
	{
		$this->bSharpen = true;
		$this->nSharpenStrength = $nSharpenStrength;

		return true;
	}

	function grayscale()
	{
		$this->bGrayscale = true;

		return true;
	}

	function addTextAbs( $sText, $nTextPosX, $nTextPosY, $nTextFontSize = 24, $sTextFontStyle = 'normal', $sTextFontColor = '#ffffff', $sTextFontFace = 'serif' )
	{
		$this->bText = true;
		$this->sText = $sText;
		$this->nTextPosX = $nTextPosX;
		$this->nTextPosY = $nTextPosY;
		/*
		*FIXIT:
		*/
		/*
		$this->nTextFontSize = $nTextFontSize;
		$this->sTextFontStyle = $sTextFontStyle;
		$this->sTextFontColor = $sTextFontColor;
		$this->sTextFontFace = $sTextFontFace;
		*/
		return true;
	}

	function addText( $sText, $sPosition = "Center-Center", $nTextFontSize = 24, $sTextFontStyle = 'normal', $sTextFontColor = '#ffffff', $sTextFontFace = 'serif' )
	{
		$this->bText = true;
		$this->sText = $sText;
		preg_match( "/(Top|Center|Bottom)-(Left|Center|Right)/i", $sPosition, $aRes );
		//convert Top-Left to lt
		$this->sTextPosition = strtolower( $aRes[ 1 ][ 0 ] ).strtolower( $aRes[ 2 ][ 0 ] );
		/*
		*FIXIT:
		*/
		/*
		$this->nTextPosX = $nTextPosX;
		$this->nTextPosY = $nTextPosY;
		$this->nTextFontSize = $nTextFontSize;
		$this->sTextFontStyle = $sTextFontStyle;
		$this->sTextFontColor = $sTextFontColor;
		$this->sTextFontFace = $sTextFontFace;
		*/
		return true;
	}

//	function addWatermark( $sWatermarkFileName, $sWatermarkPosition, $nWatermarkScaleToPercent = 0, $nWatermarkMargin = 20, $nWatermarkOpacity = 100 )
//	{
//		$this->bWatermark = true;
//		$this->sWatermarkFileName = $sWatermarkFileName;
//
//		switch ($sWatermarkPosition) {
//			case "Top-Left":
//				$this->sWatermarkPosition = 'tl';
//				break;
//
//			case "Top-Center":
//				$this->sWatermarkPosition = 'tc';
//				break;
//
//			case "Top-Right":
//				$this->sWatermarkPosition = 'tr';
//				break;
//
//			case "Center-Left":
//				$this->sWatermarkPosition = 'cl';
//				break;
//
//			case "Center-Right":
//				$this->sWatermarkPosition = 'cr';
//				break;
//
//			case "Center-Center":
//				$this->sWatermarkPosition = 'cc';
//				break;
//
//			case "Bottom-Left":
//				$this->sWatermarkPosition = 'bl';
//				break;
//
//			case "Bottom-Right":
//				$this->sWatermarkPosition = 'br';
//				break;
//
//			case "Bottom-Center":
//				$this->sWatermarkPosition = 'bc';
//				break;
//
//			default:
//				$this->bWatermark = false;
//				return false;
//				break;
//		}
//
//		$this->nWatermarkMargin = $nWatermarkMargin;
//		$this->nWatermarkOpacity = $nWatermarkOpacity;
//		$this->nWatermarkScaleToPercent = $nWatermarkScaleToPercent;
//
//		return true;
//	}

	function addStretchedWatermark( $sWatermarkName, $sTransColor = '', $nOpacity ){
		return $this->addWatermark( $sWatermarkName, 'Center-Center', true, $sTransColor, $nOpacity );
	}

	function addTiledWatermark( $sWatermarkFileName, $nHSpace, $nVSpace, $nTansColor, $nOpacity )
	{
		$this->bTiledWatermark = true;
		$this->sTiledWatermarkFileName = $sWatermarkFileName;
		$this->nTiledWatermarkHSpace = $nHSpace;
		$this->nTiledWatermarkVSpace = $nVSpace;
		$this->nTiledWatermarkTransColor = $nTansColor;
		$this->nTiledWatermarkOpacity = $nOpacity;

		return true;
	}

	function addWatermark( $sWatermarkFileName, $sPosition, $bShrinkToFit, $sTransColor = '', $nWatermarkOpacity )
	{
		$this->bWatermark = true;
		$this->sWatermarkFileName = $sWatermarkFileName;
		preg_match( "/(Top|Center|Bottom)-(Left|Center|Right)/i", $sPosition, $aRes );
		$this->sWatermarkPosition = strtolower( $aRes[ 1 ][ 0 ] ).strtolower( $aRes[ 2 ][ 0 ] );
		if ($bShrinkToFit) {
			//this means that we strech the watermark
			$this->nWatermarkScaleToPercent = 100;
		}

		$this->nWatermarkOpacity = $nWatermarkOpacity;
		if( $sTransColor != '' )
		{
			$this->nTransColor = $this->rImageEditor->strColorToNumber( $sTransColor );
		}

		return true;
	}

	function cropPos( $sPosition, $nWidth, $nHeight)
	{
		$this->bCropPos = true;
		preg_match( "/(Top|Center|Bottom)-(Left|Center|Right)/i", $sPosition, $aRes );
		$this->sCropPosition = strtolower( $aRes[ 1 ][ 0 ] ).strtolower( $aRes[ 2 ][ 0 ] );
		$this->nCropWidth = $nWidth;
		$this->nCropHeight = $nHeight;

		return true;
	}

	function flipHorizontal()
	{
		$this->bFlip = true;
		$this->bFlipVertical = false;

		return true;
	}

	function flipVertical()
	{
		$this->bFlip = true;
		$this->bFlipVertical = true;

		return true;
	}

	function save()
	{
		$this->bSaveAuto = true;
	}

	function saveJPEG( $nSaveJpegQuality = 75 )
	{
		$this->bSaveJpeg = true;
		$this->nSaveJpegQuality = $nSaveJpegQuality;

		return true;
	}

	function savePNG()
	{
		$this->bSavePNG = true;

		return true;
	}

	function saveGIF( $sPalette = "Adaptive", $bDitherFlag = false, $nColorsWanted = 256 )
	{
		$this->bSaveGIF = true;
		$this->sSaveGIFPalette = $sPalette;
		$this->bSaveGIFDitherFlag = $bDitherFlag;
		$this->nSaveGIFColorsWanted = $nColorsWanted;

		return true;
	}

	function outputJpeg( $bOutputJpegHeaders, $nOutputJpegQuality = 75 )
	{
		$this->bOutputJpeg = true;
		$this->bOutputJpegHeaders = $bOutputJpegHeaders;
		$this->nOutputJpegQuality = $nOutputJpegQuality;

		return true;
	}

	function outputPNG( $bOutputPNGHeaders )
	{
		$this->bOutputPNG = true;
		$this->bOutputPNGHeaders = $bOutputPNGHeaders;

		return true;
	}

	function setMask( $sMask )
	{
		$this->bMask = true;
		$this->sMask = $sMask;

		return true;
	}

	function getMask()
	{
		if ($this->bMask) {
			return $this->sMask;
		}
		else {
			return false;
		}
	}

	/**
	 * Applies a renaming mask if such mask is defined.
	 *
	 * @access private
	 * @param cImageEditor $rFileObject
	 * @return bool
	 */
	function applyMask( $sOriginalFileName, $sAddExtension = null )
	{
		//$this->rImageEditor = new cGD2ImageEditor; // DEBUG

		# Get path components:
		$aPathInfo = pathinfo( $sOriginalFileName );

		$sDirname = $aPathInfo["dirname"];
		$sFilename = $aPathInfo["basename"];

		//$sName = $aPathInfo[ "filename" ];  // works only since PHP 5.2.0
		$sName = basename( $sOriginalFileName, '.'.$aPathInfo["extension"] );

		$sNewFilename = $this->getMask();

		if ($sNewFilename) {
			$sNewFilename = str_replace( "##path##", $sDirname.DIRECTORY_SEPARATOR, $sNewFilename );
			$sNewFilename = str_replace( "##filename##", $sFilename, $sNewFilename );
			$sNewFilename = str_replace( "##name##", $sName, $sNewFilename );
		}

		/*
		* FIXIT: What is this?
		*/
		# Check if we need a certain extension
		if ( !is_null( $sAddExtension ) )
		{
			$sNewFilename .= '.' . $sAddExtension;
			/*
			$sEnd = substr( $sNewFilename, ( 0 - strlen( $sAddExtension ) ) );

			# Check if we already have that extension
			if (strcmp( strtolower($sEnd), strtolower($sAddExtension) ) != 0) {
				if ($sEnd == '.jpg' && $sAddExtension == 'jpeg') {
					# Exception case
				}
				else {
					# No,  we don't - add it
					$sNewFilename .= '.' . $sAddExtension;
				}
			}
			*/
		}

		return $sNewFilename;
	}

	/**
	 * Sets a new font file to be used with GD2.
	 * The parameter MUST be just the font file!
	 * The font file MUST be present in the 'resources' directory!
	 *
	 * @param string $sFontName
	 */
	function setFontFamily( $sFontName = "arena_condensed.ttf" )
	{
		if (isset($this->rImageEditor->sUsedFont)) {
			$this->rImageEditor->sUsedFont = CS_ROOT_DIR . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $sFontName;
		}

		return true;
	}

	function setFontSize( $nNewFontSize )
	{
		$this->nTextFontSize = $nNewFontSize;
		return true;
	}

	function setFontColor( $sFontColor )
	{
		$this->sTextFontColor = $sFontColor;
		return true;
	}

	function setBold( $bBold = true )
	{
		$this->bTextBold = $bBold;
		return true;
	}

	function setItalic( $bItalic = true )
	{
		$this->bTextItalic = $bItalic;
		return true;
	}

	function setUnderline( $bUnderline = true )
	{
		$this->bTextUnderline = $bUnderline;
		return true;
	}

	function setNewDimensionsInUpload( $nNewWidth, $nNewHeight )
	{
		if ( $this->sMethod != 'upload') {
			return false;
		}
		else {
			$sFileName = basename($this->rImageEditor->sFileName);

			foreach ($this->upload->uploadedFiles as $file) {

				if	(	$file->getFileName() == $sFileName
						&&	( $this->sUploadFileds == $file->field || $this->sUploadFileds == '' )
					)
				{

					$file->setImageSize( $nNewWidth, $nNewHeight );

					$_POST[$this->upload->saveWidth] = $nNewWidth;
					$_POST[$this->upload->saveHeight] = $nNewHeight;
					$HTTP_POST_VARS[$this->upload->saveWidth] = $nNewWidth;
					$HTTP_POST_VARS[$this->upload->saveHeight] = $nNewHeight;

					# Work done!
					return true;
				}
			}

			# The file wasn't found! There's something wrong here...
			return false;
		}
	}

	function setNewFileNameInUpload( $sNewName )
	{
		if ( $this->sMethod != 'upload') {
			return false;
		}
		else {
			$sFileName = basename($this->rImageEditor->sFileName);
			$sNewFileName = basename($sNewName);

			foreach ($this->upload->uploadedFiles as $file) {

				if	(	$file->getFileName() == $sFileName)
				{
					$file->setFileName($sNewFileName);

					# Work done!
					return true;
				}
			}

			# The file wasn't found! There's something wrong here...
			return false;
		}
	}

	function setUploadFileds( $sUploadFileds )
	{
		$this->sUploadFileds = $sUploadFileds;

		return true;
	}

	function getExt( $sFilename )
	{
		$aPath = pathinfo( $sFilename );
		return $aPath[ 'extension' ];
	}

	/**
	 * Changes the file extension but you can pass a third parameter which is a regex which if matches will not change the extension
	 *
	 * @param string $sFilename
	 * @param string $sToExt
	 * @param string $sDontIf
	 */
	function switchExt( $sFilename, $sToExt, $sDontIf = '' )
	{
		//$sToExt = strtolower($sToExt);
		//prepend the dot
		//echo "To Ext: ".$sToExt;
		//echo "File: ".$sFilename." will get extension: ".$sToExt;
		if( $sToExt[ 0 ] != '.' )
		{
			$sToExt = '.'.$sToExt;
		}
		if( !empty( $sDontIf ) )
		{
			if( preg_match( '%'.$sDontIf.'%i', $sFilename ) )
			{
				return $sFilename;
			}
		}
		$sNewFilename = preg_replace( "/\.[^\.]*$/i", $sToExt, $sFilename );

		if ($sNewFilename != $sFilename && $this->sMethod == 'upload')
		{
			if (file_exists($sNewFilename)) {
				if ($this->rResource->nameConflict == 'uniq') {
					$sNewFilename = $this->rResource->createUniqName($sNewFilename);
				} elseif ($this->rResource->nameConflict == 'error') {
					$this->rResource->error('exist', $sNewFilename);
				}
			}
		}

		return $sNewFilename;
	}

	function switchExt_old( $sFilename, $sOldExt, $sToExt )
	{
		//$sToExt = strtolower($sToExt);

		//prepend the dot
		if( $sToExt[ 0 ] != '.' )
		{
			$sToExt = '.'.$sToExt;
		}
		$sFileName = preg_replace( "/\.[^\.]$/i", $sToExt );
		return $sFileName;

		/*
		* FIXIT: What is this?
		*/
		/*
		$sEnd = substr( strrchr($sFilename, '.'), 1);

		if ($sToExt == strtolower($sEnd)) {
			return $sFilename;
		}
		else {
			$sNewFilename = preg_replace("#$sEnd$#", $sToExt, $sFilename);
			return $sNewFilename;
		}
		*/
	}

}

?>