<?php

//this is the class factory, it transparently creates the object of the needed class
class cImageEditorFactory
{
	var $hOptions = null;
	var $aPreference = array( "gd2", "gd", "imagemagick", "netpbm" );
	function cImageEditorFactory( $hConfig )
	{
		$this->hOptions = new cImageEditorFactoryConfig( $hConfig );
		for( $nI = 0; $nI < count( $this->aPreference ); $nI++ )
		{
			if( $this->hOptions->aAvailableLibraries[ $this->aPreference[ $nI ] ] )
			{
				$this->hOptions->sType = $this->aPreference[ $nI ];
				break;
			}
		}
	}

	function getImageEditor( $sType = '' )
	{
		$sType = strtolower($sType);
		
		if ( $sType == 'gd' || $sType == 'gd2' || $sType == 'imagemagick' || $sType == 'netpbm' ) {
			$this->hOptions->sType = $sType;
		}
		
		if( $this->hOptions->sType == 'gd' )
		{
			return new cGDImageEditor();
		}
		else if( $this->hOptions->sType == 'gd2' )
		{
			return new cGD2ImageEditor();
		}
		else if( $this->hOptions->sType == 'imagemagick' )
		{
			return new cImageMagickImageEditor();
		}
		else if( $this->hOptions->sType == 'netpbm' )
		{
			return new cNetPBMImageEditor();
		}
		else
		{
		}
	}
}

?>