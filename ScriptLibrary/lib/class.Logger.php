<?php
/*
addLog( USER_NAME.FUNCTION_NAME.FUNCTION_PARTAMETERS.ADDITIONAL_INFO )
*/
define( 'LOG_MSG_LOG', 1 );
define( 'LOG_MSG_WARNING', 2 );
define( 'LOG_MSG_ERROR', 4 );
define( 'LOG_MSG_EMERG', 8 );
define( 'LOG_MSG_USER_MSG', 64 );
define( 'LOG_MSG_USER_ERROR', 128 );
define( 'LOG_MSG_ALL', 512 );

define( 'LOG_PRIORITY', 0 );
define( 'LOG_TIME', 1 );
define( 'LOG_MESSAGE', 2 );
define( 'LOG_CODE', 4 );
define( 'LOG_CUSTOM', 8 );

define( 'LOG_CODE_DEFAULT', 0 );

class Logger {
	var $VARS = array();
	var $LOG_MESSAGES = array();
	var $LOG_PRIORITIES = array();
	var $CS_FILTER = "[ PRIORITY ] DATE : MESSAGE";

	function priorityToString( $nPriority ) 
	{
        $priorities = array(
            LOG_MSG_USER_ERROR => 'usererror',
            LOG_MSG_USER_MSG => 'usermessage',
            LOG_MSG_EMERG   => 'emergency',
            LOG_MSG_ERROR => 'error',
            LOG_MSG_WARNING => 'warning',
            LOG_MSG_LOG => 'info',
        );
        return $priorities[$nPriority];
    }

	function getVar( $name,$default ) {
	    if( $var = getenv( $name ) ) 
		{
		    return $var;
	    } 
		else 
		{
	    	return $default;
	    }
	}

	function Logger() {
	    $this->VARS["remote_host"] = $this->getVar( "REMOTE_ADDR",  "-");
		$this->VARS["remote_user"] = $this->getVar( "REMOTE_USER",  "-");
		$this->VARS["remote_ident"] = $this->getVar( "REMOTE_IDENT",  "-");
		$this->VARS["server_port"] = $this->getVar( "SERVER_PORT", 80);
		if($this->VARS["server_port"]!=80) {
		    $this->VARS["server_port"] =  ":" . $this->VARS["server_port"];
		} else {
		    $this->VARS["server_port"] =  "";
		}
		
		$this->VARS["server_name"] = $this->getVar( "SERVER_NAME",  "-");
		$this->VARS["referer"] = $this->getVar( "HTTP_REFERER",  "-");
		$this->VARS["user_agent"] = $this->getVar( "HTTP_USER_AGENT",  "");
	}
	
	function getStringResult( $aVars, $sSep=" | " )
	{
		reset($this->VARS);
		reset($aVars);
		$sRes = "";
		while( list( $key, $value ) = each( $aVars ) )
		{
			$sRes .= $this->VARS[$value].$sSep;
		}
		
		$sRes = substr( $sRes, 0, strlen($sRes) - strlen( $sSep ) )."\n";
		return $sRes;
	}
	
	function logMessage( $sMessage, $nMessageCode = LOG_CODE_DEFAULT, $sCustomField = "", $nPriority = LOG_MSG_LOG )
	{
		$this->LOG_MESSAGES[] = array( 
										LOG_PRIORITY=>$nPriority, 
										LOG_TIME=>time(), 
										LOG_CODE=>$nMessageCode,
										LOG_MESSAGE=>$sMessage,
										LOG_CUSTOM=>$sCustomField
									);
		$this->LOG_PRIORITIES[ $nPriority ] = 1;
	}
	
	function getLoggedPriorityPresent( $nPriority )
	{
		if( isset( $this->LOG_PRIORITIES[ $nPriority ] ) && ( $this->LOG_PRIORITIES[ $nPriority ] == 1 ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//will return an associative array with keys the custom field and with content the message based on the message priority
	function getFilteredAssocMessagesByPriority( $nPriority )
	{
		$aResult = array();

		reset( $this->LOG_MESSAGES );
		while( list( $nKey, $aMessage ) = each( $this->LOG_MESSAGES ) )
		{
			if( $nPriority & $aMessage[ LOG_PRIORITY ] )
			{
				$aResult[ strtolower( $aMessage[ LOG_CUSTOM ] ) ] = $aMessage[ LOG_MESSAGE ];
			}
		}
		return $aResult;
	}
	
	function getFilteredMessagesAsString( $nFilter, $bShort = false )
	{
		reset( $this->LOG_MESSAGES );
		$sRes = "";
		while( list( $nKey, $aMessage ) = each( $this->LOG_MESSAGES ) )
		{
			reset( $aMessage );
			if( $nFilter & $aMessage[0] )
			{
				if( !$bShort )
				{
					$sMessageLine = $this->CS_FILTER;
					$sMessageLine = str_replace( "PRIORITY", $this->priorityToString( $aMessage[0] ), $sMessageLine );
					$sMessageLine = str_replace( "DATE", date( "d.m.Y H:i:s", $aMessage[1] ), $sMessageLine );
					$sMessageLine = str_replace( "MESSAGE", $aMessage[2], $sMessageLine );
					$sRes .= $sMessageLine;
				}
				else
				{
					$sRes .= 	$aMessage[2]."\n";
				}
			}
		}
		
		return $sRes;
	}

	function saveMessages( $sFileName, $nFilter = LOG_MSG_ALL )
	{
		$hFP = fopen( $sFileName, "a+" );
		if( ( !$hFP ) || empty( $hFP ) ) 
		{
			$this->error("[$filename] error opening table");
			return;
		}
		$sMessages = $this->getFilteredMessagesAsString( $nFilter );
		fwrite( $hFP, $sMessages );
	}
	
	function error( $sError )
	{
		echo "Critical Log Error: ".$sError;
	}
}
?>