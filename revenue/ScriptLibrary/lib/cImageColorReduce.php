<?php
/*
*	This works for GD for now, too much specific code
*/


/*
*	Types of palettes
*/
define( 'CN_PALETTE_BLACKWHITE', 0x01 );
define( 'CN_PALETTE_GRAYSCALE', 0x02 );
define( 'CN_PALETTE_ADAPTIVE', 0x04 );
define( 'CN_PALETTE_WEBSAFE', 0x08 );

/*
*	Types of dithering
*	Default is FloydSteinberg which is the only implemented
*/
define( 'CN_DITHER_DEFAULT', 0x01 );
define( 'CN_DITHER_FLOYDSTEINBERG', 0x01 );

/**
*
*/
class cColorReductor
{
	var $nWidth;
	var $nHeight;
	var $nX;
	var $nY;
	var $nStride;
	
	var $aColorMap = array();
	var $aColors = array();

	//rImage - image resource
	//rPalette - palette
	function reduceColors( $rImage, $nPalette, $nMaxColors, $nDitherMethod )
	{
		$this->nWidth = $rImage->getWidth();
		$this->nHeight = $rImage->getHeight();
		
		$this->aColors = array();
		$this->aColorMap = array();

		$this->setPalette( $rImage, $nPalette, $nMaxColors );
	}
	
	function setPalette( $rImage, $nPalette, $nMaxColors )
	{
		$nMaxColors = min( 255, $nMaxColors );
		$this->aColors = array();
		switch( $nPalette )
		{
			case CN_PALETTE_BLACKWHITE :
						                    $this->aColors[] = cColor::Black();
						                    $this->aColors[] = cColor::White();
						                    $this->aColors[] = cColor::FromArgb( 0, 0, 0, 0 );
											break;
			case CN_PALETTE_GRAYSCALE :
											if ( $nMaxColors > 128 )
											{
						                        for ( $nI = 0; $nI < 254; $nI++ )
						                        {
						                            $this->aColors[] = cColor.FromArgb ( 255, $nI, $nI, $nI );
						                        }
												$this->aColors[] = cColor::White();
											}
											else if ( $nMaxColors > 64 )
											{
						                        for ( $nI = 0; $nI < 128; $nI++ )
						                        {
						                            $this->aColors[] = cColor.FromArgb ( 255, $nI * 2, $nI * 2, $nI * 2 );
						                        }
											}
											else if ( $nMaxColors > 32 )
											{
						                        for ( $nI = 0; $nI < 64; $nI++ )
						                        {
						                            $this->aColors[] = cColor.FromArgb ( 255, $nI * 4, $nI * 4, $nI * 4 );
						                        }
											}
											else if ( $nMaxColors > 16 )
											{
						                        for ( $nI = 0; $nI < 32; $nI++ )
						                        {
						                            $this->aColors[] = cColor.FromArgb ( 255, $nI * 8, $nI * 8, $nI * 8 );
						                        }
											}
											else if ( $nMaxColors > 8 )
											{
						                        for ( $nI = 0; $nI < 16; $nI++ )
						                        {
						                            $this->aColors[] = cColor.FromArgb ( 255, $nI * 17, $nI * 17, $nI * 17 );
						                        }
											}
											else if ( $nMaxColors > 4 )
											{
						                        for ( $nI = 0; $nI < 8; $nI++ )
						                        {
						                            $this->aColors[] = cColor.FromArgb ( 255, $nI * 36, $nI * 36, $nI * 36 );
						                        }
											}
											else if ( $nMaxColors > 2 )
											{
						                        for ( $nI = 0; $nI < 4; $nI++ )
						                        {
						                            $this->aColors[] = cColor.FromArgb ( 255, $nI * 85, $nI * 85, $nI * 85 );
						                        }
											}
											else
											{
												$this->aColors[] = cColor::Black();
												$this->aColors[] = cColor::White();
											}

											$this->aColors[] = cColor::FromRGB( 0, 0, 0, 0 );
											break;
			case CN_PALETTE_WEBSAFE :		for ( $nR = 0; $nR < 6; $nR++ )
											{
												for (int $nG = 0; $nG < 6; $nG++ )
												{
													for (int $nB = 0; $nB < 6; $nB++ )
													{
														$this->aColors[] = cColor.FromArgb ( 255, $nR * 51, $nG * 51, $nB * 51 );
													}
												}
											}
											$this->reducePalette( $rImage, $nMaxColors );
											$this->aColors[] = cColor.FromArgb ( 0, 0, 0, 0 );
											break;
			case CN_PALETTE_ADAPTIVE :
											break;
		}
	}
	
	function reducePalette( $rImage, $nMaxColors )
	{
		$aColors = array();
		$nWidth = $rImage->getWidth();
		$nHeight = $rImage->getHeight();

		for( $nY = 0; $nY < $nHeight; $nY++ )
		{
			for( $nX = 0; $nX < $nWidth; $nX++ )
			{
				$nKey = $this->getNearestColor( $nX, $nY );
				$nC = $this->aColors[ $nKey ];
				if( isset( $aColors[ $nC ] ) )
				{
					$aColors[ $nC ]++;
				}
				else
				{
					$aColors[ $nC ] = 1;
				}
			}
		}

		//remove from $this->aColros all not used colors
		for( $nI = count( $this->aColors ) - 1; $nI >= 0; $nI-- )
		{
			if( !isset( $aColors[ $this->aColors[ $nI ] ] ) )
			{
				unset( $this->aColors[ $nI ] );
			}
		}

		$nMinCount = -1;
		while( count( $this->aColors ) > maxColors )
		{
			reset( $aColors );
			while( list( $nColor, $nCount ) = each( $aColors ) )
			{	
				if( $nMinCount = -1 )
				{
					$nMinCount = $nCount;
				}
			}
		}
	}
}

/*
$rgba = imagecolorat($im,$x,$y);
$alpha = ($rgba & 0x7F000000) >> 24;
$red = ($rgba & 0xFF0000) >> 16;
$green = ($rgba & 0x00FF00) >> 8;
$blue = ($rgba & 0x0000FF);
*/

class cColor
{
	function Black()
	{
		return cColor::FromARGB( 0, 0, 0, 0 );
	}
	
	function White()
	{
		return cColor::FromARGB( 0, 255, 255, 255 );
	}
	
	/**
	*	from Alpha, Red, Green, Blue
	*/
	function FromARGB( $nAlpha, $nRed, $nGreen, $nBlue )
	{
		$nColor = 0x00000000;
		//alpha is from 0-127
		$nAlpha = $nAlpha & 0x3F;
		$nColor = $nColor | $nBlue;
		$nColor = $nColor | ( $nGreen << 8 );
		$nColor = $nColor | ( $nRed << 16 );
		$nColor = $nColor | ( $nAlpha << 24 );
		return $nColor;
	}
}

echo cColor::White();
?>

