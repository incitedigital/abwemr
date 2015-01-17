<?php
//no  cache headers 
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
	
// Start sessions
if (!isset($_SESSION)) {
    session_start();
}

if (!function_exists('ini_get_bool')) {
	function ini_get_bool($setting) {
		$my_boolean = ini_get($setting);

		if ((int)$my_boolean > 0 ) {
			$my_boolean = true;
		} else {
			$my_lowered_boolean = strtolower($my_boolean);

			if ($my_lowered_boolean === "true" || $my_lowered_boolean === "on" || $my_lowered_boolean === "yes") {
				$my_boolean = true;
			} else {
				$my_boolean = false;
			}
		}

		return $my_boolean;
	}
}


$data = array(
	'totalBytes' => 0,
	'uploadedBytes' => 0,
	'status' => 0,
	'lastFile' => '',
	'lastError' => '',
	'files' => array(), // array('name', 'status', 'error')
	'errors' => array() // array('description')
);

if (isset($_GET['UploadId']) && !empty($_GET['UploadId'])) {
	if (ini_get_bool('session.upload_progress.enabled')) {
		// PHP 5.4 upload progress
		$k = ini_get('session.upload_progress.prefix').$_GET['UploadId'];
		if (isset($_SESSION[$k])) {
			// parse data http://php.net/manual/en/session.upload-progress.php
			$data['totalBytes'] = (int)$_SESSION[$k]['content_length'];
			$data['uploadedBytes'] = (int)$_SESSION[$k]['bytes_processed'];
			$data['status'] = $_SESSION[$k]['done'] ? 3 : 1;
			foreach ($_SESSION[$k]['files'] as $file) {
				array_push($data['files'], array(
					'name' => $file['name'],
					'status' => $file['done'] ? 3 : 1,
					'error' => ''
				));
			}
			$lastFile = end($_SESSION[$k]['files']);
			$data['lastFile'] = $lastFile['name'];
		}
		/*
		array(
		 "start_time" => 1234567890,   // The request time
		 "content_length" => 57343257, // POST content length
		 "bytes_processed" => 453489,  // Amount of bytes received and processed
		 "done" => false,              // true when the POST handler has finished, successfully or not
		 "files" => array(
		  0 => array(
		   "field_name" => "file1",       // Name of the <input/> field
		   // The following 3 elements equals those in $_FILES
		   "name" => "foo.avi",
		   "tmp_name" => "/tmp/phpxxxxxx",
		   "error" => 0,
		   "done" => true,                // True when the POST handler has finished handling this file
		   "start_time" => 1234567890,    // When this file has started to be processed
		   "bytes_processed" => 57343250, // Number of bytes received and processed for this file
		  ),
		  // An other file, not finished uploading, in the same request
		  1 => array(
		   "field_name" => "file2",
		   "name" => "bar.avi",
		   "tmp_name" => NULL,
		   "error" => 0,
		   "done" => false,
		   "start_time" => 1234567899,
		   "bytes_processed" => 54554,
		  ),
		 )
		);
		*/
	} elseif (function_exists('uploadprogress_get_info')) {
		// Upload progress extension installed
		$status = uploadprogress_get_info($_GET['UploadId']);
		$data['totalBytes'] = (int)$status['bytes_total'];
		$data['uploadedBytes'] = (int)$status['bytes_uploaded'];
		$data['status'] = 1;
		$data['lastFile'] = $status['filename'];
		array_push($data['files'], array(
					'name' => $data['lastFile'],
					'status' => $data['status'],
					'error' => ''
				));
	} elseif (ini_get_bool('apc.rfc1867')) {
		// APC with upload progress
		$status = apc_fetch(ini_get('apc.rfc1867_prefix').$_GET['UploadId']);
		$data['totalBytes'] = (int)$status['total'];
		$data['uploadedBytes'] = (int)$status['current'];
		$data['status'] = $status['done'] ? 3 : 1;
		$data['lastFile'] = $status['filename'];
		/*
		Array
		(
			[total] => 1142543
			[current] => 1142543
			[rate] => 1828068.8
			[filename] => test
			[name] => file
			[temp_filename] => /tmp/php8F
			[cancel_upload] => 0
			[done] => 1
		)
		*/
	} else {
		header('Content-Type: text/xml; Charset=UTF-8');
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		printf('<progressStatus totalBytes="0" uploadedBytes="0" status="0" lastFile="" lastError="Upload progress not supported">');
		printf('<errors>Upload progress not supported</errors>');
		printf('</progressStatus>');
		exit();

	}
}

header('Content-Type: text/xml; Charset=UTF-8');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

printf('<progressStatus totalBytes="%s" uploadedBytes="%s" status="%s" lastFile="%s" lastError="">',
	   $data['totalBytes'], $data['uploadedBytes'], $data['status'], $data['lastFile']);
printf('<files>');
foreach ($data['files'] as $file) {
	printf('<file name="%s" status="%s" error="" />', $file['name'], $file['status']);
}
printf('</files>');
printf('<errors></errors>');
printf('</progressStatus>');

?>