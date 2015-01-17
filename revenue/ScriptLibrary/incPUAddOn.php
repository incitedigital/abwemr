<?php
// --- Pure PHP Upload Add On Pack ----------------------------------------------
// Copyright 2003-2012 (c) George Petrov, Patrick Woldberg, www.DMXzone.com
//
// Version: 1.0.10
// ------------------------------------------------------------------------------

// Delete the file before updating the database record
class deleteFileBeforeUpdate extends pureUploadAddon
{
	var $version = "1010";
	var $debugger = false;

	var $pathThumb;
	var $naming;
	var $suffix;

	var $sqldata;
	
	function deleteFileBeforeUpdate(&$upload) {
		global $DMX_debug;
		parent::pureUploadAddon($upload);
		$this->upload->registerAddOn($this);
		$this->debugger = $DMX_debug;
		$this->debug("<br/><font color=\"#009900\"><b>Delete File Before Update version ".$this->version."</b></font><br/><br/>");
	}
	
	// Check if version is uptodate
	function checkVersion($version) {
		$version = str_replace(".", "", $version);
		if ($version > $this->version) {
			$this->error('version');
		}
	}
	
	// The delete function
	function deleteFile() {
		global $HTTP_SERVER_VARS,$HTTP_POST_VARS;

		$this->debug("PHP version(<font color=\"#990000\">".phpversion()."</font>)<br/>");
		$this->debug("naming(<font color=\"#990000\">".$this->naming."</font>)<br/>");
		$this->debug("suffix(<font color=\"#990000\">".$this->suffix."</font>)<br/>");
		$this->debug("pathThumb(<font color=\"#990000\">".$this->pathThumb."</font>)<br/>");

		// Go thrue all files
		foreach ($this->upload->uploadedFiles as $file) {
			if ($file->fileName != "") {
				// Check if database entree exist
				if (isset($this->sqldata[$file->field])) {
					// Get filename from the database
					$fileName = $this->sqldata[$file->field];
					$this->debug("fileName = <font color=\"#000099\"><b>".$fileName."</b></font><br/>");
					// Extract name/extension from filename
					$pos = strrpos($fileName, "/");
					$name = substr($fileName, $pos, strrpos($fileName,".")-$pos);
					// get thumbname created from filename
					if ($this->naming == "suffix") {
						$thumbName = $name.$this->suffix.".jpg";
					} else {
						$thumbName = $this->suffix.$name.".jpg";
					}
					$this->debug("thumbName = <font color=\"#000099\"><b>".$thumbName."</b></font><br/>");
					
					// If storeType is path
					if ($this->upload->storeType == 'path') {
						// Create an absolute path
						$absPath = $_SERVER['PATH_TRANSLATED'];
						$absPath = preg_replace('%\\|//%', '/', $absPath);
						$absPath = preg_replace('%'.$_SERVER['PHP_SELF'].'%', '', $absPath);
						$absPath = $absPath.'/'.substr($fileName, 1, strrpos($fileName, '/'));
						$this->debug("absPath = <font color=\"#000099\"><b>".$absPath."</b></font><br/>");
						
						// Extract filename from the path that was stored in the database
						$fileName = substr($fileName, strrpos($fileName, '/')+1);
						$this->debug("fileName = <font color=\"#000099\"><b>".$fileName."</b></font><br/>");
						$this->debug("file->fileName = <font color=\"#000099\"><b>".$file->fileName."</b></font><br/>");
						
						// Check if file exists and delete the file
						$this->debug("Check if <b>".$absPath.$fileName."</b> exists<br/>");
						if (file_exists($absPath.$fileName) && $fileName <> '' && $file->fileName <> $fileName) {
							$this->debug("Deleting <b>".$absPath.$fileName."</b><br/>");
							if (!@unlink($absPath.$fileName)) {
								$this->error('delete', $absPath.$fileName." - ".$file->field." - ".$_POST[$file->field]);
							}
						}
						// Check if thumbnail exists and delete the thumbnail
						if ($this->pathThumb !== "") {
							$absPath = $this->pathThumb."/";
						}
						$this->debug("thumbnailPath = <font color=\"#000099\"><b>".$absPath."</b></font><br/>");
						$this->debug("Check if <b>".$absPath.$thumbName."</b> exists<br/>");
						if (file_exists($absPath.$thumbName) && $thumbName <> '') {
							$this->debug("Deleting <b>".$absPath.$thumbName."</b><br/>");
							if (!@unlink($absPath.$thumbName)) {
								$this->error('delete', $absPath.$thumbName);
							}
						}
					} else {
						$absPath = $this->upload->path;
						$this->debug("absPath = <font color=\"#000099\"><b>".$absPath."</b></font><br/>");
						$this->debug("fileName = <font color=\"#000099\"><b>".$fileName."</b></font><br/>");
						$this->debug("file->fileName = <font color=\"#000099\"><b>".$file->fileName."</b></font><br/>");						
						// Check if file exists and delete the file
						$this->debug("Check if <b>".$absPath."/".$fileName."</b> exists<br/>");
						if (file_exists($absPath.'/'.$fileName) && $fileName <> '' && $file->fileName <> $fileName) {
							$this->debug("Deleting <b>".$absPath."/".$fileName."</b><br/>");
							if (!@unlink($absPath.'/'.$fileName)) {
								$this->error('delete', $absPath.'/'.$fileName." - ".$_POST[$file->field]);
							}
						}
						// Check if thumbnail exists and delete the thumbnail
						if ($this->pathThumb !== "") {
							$absPath = $this->pathThumb;
						}
						$this->debug("thumbnailPath = <font color=\"#000099\"><b>".$absPath."</b></font><br/>");
						$this->debug("thumbnail = <font color=\"#000099\"><b>".$thumbName."</b></font><br/>");
						$this->debug("Check if <b>".$absPath."/".$thumbName."</b> exists<br/>");
						if (file_exists($absPath.'/'.$thumbName) && $thumbName <> '') {
							$this->debug("Deleting <b>".$absPath."/".$thumbName."</b><br/>");
							if (!@unlink($absPath.'/'.$thumbName)) {
								$this->error('delete', $absPath.'/'.$thumbName);
							}
						}
					}
				}
			}
		}
	}

	// Debugger
	function debug($info) {
		if ($this->debugger) {
			echo "<font face=\"verdana\" size=\"2\">".$info."</font>";
		}
	}
	
	function error($error, $extra="") {
		// Display error
		echo "<b>Error deleting file</b><br/><br/>";

		switch ($error) {
		// Error renaming the file
		case 'delete':
			echo "The delete function generated an error on file ".$extra."<br/>";
			break;
		case 'version':
			echo "Please Upload the latest version of incPUAddOn.php";
			break;
		}
		
		// Allow to go back and stop the script
		echo "Please correct and <a href=\"javascript:history.back(1)\">try again</a>";
		$this->upload->failUpload();
		exit;
	}

	function cleanUp() {
	}
}

// Rename the uploaded files with an mask
class renameUploadedFiles extends pureUploadAddon
{
	var $version = "1010";
	var $debugger = false;

	var $renameMask;
	
	function renameUploadedFiles(&$upload) {
		global $DMX_debug;
		parent::pureUploadAddon($upload);
		$this->upload->registerAddOn($this);
		$this->debugger = $DMX_debug;
		$this->debug("<br/><font color=\"#009900\"><b>Rename Uploaded Files version ".$this->version."</b></font><br/><br/>");
	}
	
	// Check if version is uptodate
	function checkVersion($version) {
		$version = str_replace(".", "", $version);
		if ($version > $this->version) {
			$this->error('version');
		}
	}
	
	// The actual rename function
	function doRename() {
		$this->debug("PHP version(<font color=\"#990000\">".phpversion()."</font>)<br/>");
		$this->debug("renameMask(<font color=\"#990000\">".$this->renameMask."</font>)<br/>");

		// Go thrue all the files
		foreach ($this->upload->uploadedFiles as $file) {
			if ($file->fileName != "") {
				$this->debug("Starting the rename with the file <b>".$file->fileName."</b><br/>");
				// Generate the new filename
				$this->debug("##name## = <font color=\"#000099\"><b>".$file->name."</b></font><br/>");
				$this->debug("##ext## = <font color=\"#000099\"><b>".$file->extension."</b></font><br/>");
				$this->debug("##size## = <font color=\"#000099\"><b>".$file->fileSize."</b></font><br/>");
				$this->debug("##width## = <font color=\"#000099\"><b>".$file->imageWidth."</b></font><br/>");
				$this->debug("##height## = <font color=\"#000099\"><b>".$file->imageHeight."</b></font><br/>");
				$rename = $this->renameMask;
				$rename = preg_replace('/##name##/i', $file->name, $rename);
				$rename = preg_replace('/##ext##/i', $file->extension, $rename);
				$rename = preg_replace('/##size##/i', "".$file->fileSize, $rename);
				$rename = preg_replace('/##width##/i', "".$file->imageWidth, $rename);
				$rename = preg_replace('/##height##/i', "".$file->imageHeight, $rename);
				$this->debug("new fileName = <font color=\"#000099\"><b>".$rename."</b></font><br/>");
	
				// Check if filename exists
				$this->debug("Check if <b>".$this->upload->path."/".$rename."</b> exists<br/>");
				if (file_exists($this->upload->path.'/'.$rename)) {
					// What to do if filename exists
					switch ($this->upload->nameConflict) {
					// Overwrite the file
					case 'over':
						if ($file->fileName != $rename) {
							$this->debug("Overwrite <b>".$this->upload->path."/".$rename."</b><br/>");
							unlink($this->upload->path.'/'.$rename);
							if (!rename($this->upload->path.'/'.$file->fileName, $this->upload->path.'/'.$rename)) {
								$this->error('rename', $rename);
							}
						}
						break;
					// Give error message
					case 'error':
						$this->error('exist', $rename);
						break;
					// Skip renaming and delete the uploaded file
					case 'skip':
						$this->debug("Skip <b>".$this->upload->path."/".$rename."</b><br/>");
						unlink($this->upload->path.'/'.$file->fileName);
						break;
					// Make an unique name
					case 'uniq':
						$this->debug("Create new name for <b>".$this->upload->path."/".$rename."</b><br/>");
						$rename = $this->upload->createUniqName($rename);
						$this->debug("Renaming to <b>".$this->upload->path."/".$rename."</b><br/>");
						if (!rename($this->upload->path.'/'.$file->fileName, $this->upload->path.'/'.$rename)) {
							$this->error('rename', $rename);
						}
						break;
					}
				} else {
					// If filename does not exist
					$this->debug("Renaming to <b>".$this->upload->path."/".$rename."</b><br/>");
					if (!rename($this->upload->path.'/'.$file->fileName, $this->upload->path.'/'.$rename)) {
						$this->error('rename', $rename);
					}
				}
				
				// Update the name in the fileinfo
				$this->debug("Updating FileInfo<br/>");
				$file->setFileName($rename);
			}
		}
	}

	// Debugger
	function debug($info) {
		if ($this->debugger) {
			echo "<font face=\"verdana\" size=\"2\">".$info."</font>";
		}
	}
	
	function error($error, $extra="") {
		// Display error
		echo "<b>Error renaming uploaded file</b><br/><br/>";

		switch ($error) {
		// Error renaming the file
		case 'rename':
			echo "Rename has generated an error with file ".$extra."<br/>";
			break;
		// Error renaming the file
		case 'exist':
			echo "The file ".$extra." does allready exist<br/>";
			break;
		case 'version':
			echo "Please Upload the latest version of incPUAddOn.php";
			break;
		}
		// Allow to go back and stop the script
		echo "Please correct and <a href=\"javascript:history.back(1)\">try again</a>";
		$this->upload->failUpload();
		exit;
	}
	
	function cleanUp() {
	}
}

// Mail the uploaded files
class mailUploadedFiles extends pureUploadAddon
{
	var $version = "1010";
	var $debugger = false;

	var $fromName;
	var $fromEmail;
	var $toName;
	var $toEmail;
	var $bccEmail;
	var $mailType;
	var $subject;
	var $body;
	var $errors;
	var $html;
	var $deleteFiles;
	var $smtpServer;
	var $smtpUserName;
	var $smtpPassword;
	
	// Include other classes
	var $mail;
	
	function mailUploadedFiles(&$upload) {
		global $DMX_debug;
		parent::pureUploadAddon($upload);
		$this->upload->registerAddOn($this);
		include(dirname(__FILE__) . "/htmlMimeMail.php");
		$this->mail = new htmlMimeMail();
		$this->debugger = $DMX_debug;
		$this->debug("<br/><font color=\"#009900\"><b>Mail Uploaded Files version ".$this->version."</b></font><br/><br/>");
	}
	
	// Check if version is uptodate
	function checkVersion($version) {
		$version = str_replace(".", "", $version);
		if ($version > $this->version) {
			$this->error('version');
		}
	}
	
	function sendMail() {
		global $HTTP_POST_VARS, $HTTP_SERVER_VARS;
		
		$this->debug("PHP version(<font color=\"#990000\">".phpversion()."</font>)<br/>");
		$this->debug("fromName(<font color=\"#990000\">".$this->fromName."</font>)<br/>");
		$this->debug("fromEmail(<font color=\"#990000\">".$this->fromEmail."</font>)<br/>");
		$this->debug("toName(<font color=\"#990000\">".$this->toName."</font>)<br/>");
		$this->debug("toEmail(<font color=\"#990000\">".$this->toEmail."</font>)<br/>");
		$this->debug("bccEmail(<font color=\"#990000\">".$this->bccEmail."</font>)<br/>");
		$this->debug("mailType(<font color=\"#990000\">".$this->mailType."</font>)<br/>");
		$this->debug("subject(<font color=\"#990000\">".$this->subject."</font>)<br/>");
		$this->debug("body(<font color=\"#990000\">".$this->body."</font>)<br/>");
		$this->debug("errors(<font color=\"#990000\">".$this->errors."</font>)<br/>");
		$this->debug("html(<font color=\"#990000\">".$this->html."</font>)<br/>");
		$this->debug("deleteFiles(<font color=\"#990000\">".$this->deleteFiles."</font>)<br/>");
		$this->debug("smtpServer(<font color=\"#990000\">".$this->smtpServer."</font>)<br/>");

		// Must body be html or plain text
		if ($this->html) {
			$this->debug("Setting mail up as html<br/>");
			$this->mail->setHtml(nl2br($this->body));
		} else {
			$this->debug("Setting mail up as text<br/>");
			$this->mail->setText($this->body);
		}
		
		// Attach the uploaded files
		foreach ($this->upload->uploadedFiles as $key => $file) {
			if ($file->fileName <> "") {
				if (file_exists($this->upload->path.'/'.$file->fileName)) {
					$this->debug("Adding <b>".$this->upload->path."/".$file->fileName."</b> to the attachments<br/>");
					$attachment = $this->mail->getfile($this->upload->path.'/'.$file->fileName);
					$this->mail->addAttachment($attachment, $file->fileName);
				}
			}
		}
		
		// Set some parameters
		$this->debug("Setup some parameters<br/>");
		$this->mail->setFrom($this->fromName.' <'.$this->fromEmail.'>');
		$this->mail->setBcc($this->bccEmail);
		$this->mail->setSubject($this->subject);
		
		// Set smtpServer (if empty use localhost)
		if ($this->smtpServer <> '') {
			$this->debug("Setup SMTP-server<br/>");
			$auth = ($this->smtpUserName != "") ? true : false;
			$this->mail->setSMTPParams($this->smtpServer, 25, NULL, $auth, $this->smtpUserName, $this->smtpPassword);
		}

		// Mutiple receivers
		if (strchr($this->toEmail,";")) {
			$sendTo = array();
			$toEmails = explode(";", $this->toEmail);
			$toNames = explode(";", $this->toName);
			for ($i=0; $i<count($toEmails); $i++) {
				array_push($sendTo, $toNames[$i].' <'.$toEmails[$i].'>');
			}
		} else {
			$sendTo = array($this->toName.' <'.$this->toEmail.'>');
		}
		
		// Send the email depending on mailType
		$this->debug("Send the mail<br/>");
		if ($this->mailType=='smtp') {
			$result = $this->mail->send($sendTo, 'smtp');
			if (strpos($result, "AUTH command failed")) {
				$this->mail->setSMTPParams($this->smtpServer, 25, NULL, false, $this->smtpUserName, $this->smtpPassword);
				$result = $this->mail->send($sendTo, 'smtp');
		  }
			if (!$result) {
				$this->error('smtp');
			}
		} else {
			$result = $this->mail->send($sendTo);
			if (!$result) {
				$this->error('sendmail');
			}
		}

		if ($this->deleteFiles==true) {
			if (isset($this->upload->uploadedFiles)) {
				if (count($this->upload->uploadedFiles) > 0) {
					foreach ($this->upload->uploadedFiles as $file) {
						if ($file->fileName <> '') {
							if (file_exists($this->upload->path.'/'.$file->fileName)) {
								// Delete the file
								$this->debug("Deleting <b>".$this->upload->path."/".$file->fileName."</b><br/>");
								unlink($this->upload->path.'/'.$file->fileName);
							}
						}
					}
				}
			}
		}
	}
	
	// Debugger
	function debug($info) {
		if ($this->debugger) {
			echo "<font face=\"verdana\" size=\"2\">".$info."</font>";
		}
	}

	function error($error) {
		// Display error
		echo "<b>Error sending email</b><br/><br/>";

		switch ($error) {
		// Error sending email thrue smtp
		case 'smtp':
			foreach ($this->mail->errors as $smtperror) {
				echo $smtperror."<br/>";
			}
			break;
		// Error sending email thrue sendmail
		case 'sendmail';
			echo "Sendmail has generated an error<br/>";
			break;
		case 'version':
			echo "Please Upload the latest version of incPUAddOn.php";
			break;
		}
		
		// Allow to go back and stop the script
		echo "Please correct and <a href=\"javascript:history.back(1)\">try again</a>";
		$this->upload->failUpload();
		exit;
	}
	
	function cleanUp() {
	}
}

?>