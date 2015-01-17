<?php

require_once("config.filebrowser.php");
 
class cFileBrowser2
{
    var $aDirList = "";
    var $aFiles = array();
    var $aFolders = array();
    
	function cFileBrowser2( $aDirList = "" )
	{
        $this->aDirList = $aDirList;
	}

    function retrieveCombinedObjectList( $aDirList = "" )
    {
        if( $aDirList == "" )
        {
            $aDirList = $this->aDirList;
        }
        if( is_string( $aDirList ) )
        {
            $aDirList = array( $aDirList );
        }
        
        for( $nI = 0; $nI < count( $aDirList ); $nI++ )
        {
            $aList = $this->getObjectList( $aDirList[ $nI ] );
            $this->aFolders = array_merge( $this->aFolders, $aList[ "folders" ] );
            $this->aFiles = array_merge( $this->aFiles, $aList[ "files" ] );
        }
    }
    
	function &getObjectList( $sDirectoryName ) 
	{
        global $aFBTypeMappings;
		//$sFolder = cFileBrowser2::protectPath( urldecode( $sDirectoryName ) );
		$sFolder = $sDirectoryName;
		$aObjectsFolders = array();
		$aObjectsFiles = array();

		$hDirHandle = opendir( CS_START_DIR . $sFolder );
		if( $hDirHandle )
		{
			do
			{
				$hFile = readdir( $hDirHandle );
				if( $hFile == "." || $hFile === false )
				{
					continue;
				}
				if( is_dir( CS_START_DIR.$sFolder . "/" . $hFile ) )
				{
					if( $hFile == ".." )
					{
						if( $sDirectoryName == "" || $sDirectoryName == "." || $sDirectoryName == "./" )
						{
						}
						else
						{
							$sBackFolder = "";
							if( preg_match( "%(/?[^/]*)$%", $sFolder, $aRegs ) )
							{
								$sBackFolder = substr( $sFolder, 0, strlen( $sFolder ) - strlen( $aRegs[1] ) );
							}
							$aObjectsFolders[] = array( $hFile, $sBackFolder, CN_FOLDER );
						}
					}				
					else
					{
						if( !preg_match( '%'.CS_PROTECTED_DIR_NAMES.'%i', $hFile ) )
						{
							$aObjectsFolders[] = cFileBrowser2::composeObjectRecord( $hFile, cFileBrowser2::composePath( array(CS_START_DIR, $sFolder, $hFile) ), CN_FOLDER, filemtime( CS_START_DIR.$sFolder . "/" . $hFile ) );
						}
					}
				}
				else
				{
					$nFileType = CN_FILE;
                    reset( $aFBTypeMappings );
                    while( list( $nType, $sFilter ) = each ( $aFBTypeMappings ) )
                    {
    					if( preg_match( '/'.str_replace('/', '\\/', $sFilter).'/i', $hFile ) )
    					{
    						$nFileType |= $nType;
    					}
                    }
					$aObjectsFiles[] = cFileBrowser2::composeObjectRecord( $hFile, cFileBrowser2::composePath( array(CS_START_DIR, $sFolder, $hFile) ), $nFileType, filemtime( CS_START_DIR.$sFolder . "/" . $hFile ) );
				}
			} while( $hFile !== false );
		}
		return array( "folders" => $aObjectsFolders, "files" => $aObjectsFiles );
	}
    
    //return all files from the $aFiles list with the specified type
    function &getFilesByType( $nType )
    {
        $aResult = array();
        for( $nI = 0; $nI < count( $this->aFiles ); $nI++ )
        {
            if( $this->aFiles[ $nI ][ "type" ] & $nType )
            {
                $aResult[] = $this->aFiles[ $nI ];
            }
        }
        return $aResult;
    }

	function composePath( $aPath )
	{
		$sPath = "";
		for( $nI = 0; $nI < count( $aPath ); $nI++ )
		{
			if( strlen( $sPath ) > 0 && $sPath[ strlen( $sPath ) - 1 ] != "/" )
			{
				$sPath .= "/";
			}
			$sPath .= $aPath[ $nI ];
		}
		return $sPath;
	}
    
    function composeObjectRecord( $sName, $sPath, $nType, $nMTime )
    {
        return array(   "name" => $sName,
                        "path" => $sPath,
                        "type" => $nType,
                        "mtime" => $nMTime );
    }
	
	function protectPath( $sPath )
	{
		$sPath = preg_replace( "%(\.)+%", ".", $sPath );
		$sPath = preg_replace( "%^[\./]*%", "", $sPath );
		$sPath = preg_replace( "%[\./]*$%", "", $sPath  );
		
		return $sPath;
	}
    
    function clearLists()
    {
        unset( $this->aFolders );
        unset( $this->aFiles );
        $this->aFiles = array();
        $this->aFolders = array();
    }
}
?>