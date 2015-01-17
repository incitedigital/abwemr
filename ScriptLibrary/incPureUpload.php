<?php
// --- Pure PHP File Upload -----------------------------------------------------
// Copyright 2012-2014 (c) DMXzone.com
//
// Authors: Patrick Woldberg
// Version: 3.1.0
// ------------------------------------------------------------------------------
// uncomment for debug
//$DMX_debug = true;

define('PU_VERSION', '3.1.0');
define('PU_CONFLICT_OVERWRITE', 'over');
define('PU_CONFLICT_IGNORE', 'skip');
define('PU_CONFLICT_UNIQUE', 'uniq');
define('PU_CONFLICT_ERROR', 'error');
define('PU_STORE_FILE', 'file'); // filename only (file.ext)
define('PU_STORE_ABSURL', 'path'); // absolute url (/folder/file.ext)
define('PU_STORE_SYSTEM', 'sys'); // system path (windows: C:\\folder\file.ext, linux: /folder/file.ext)

// Start sessions
if (!isset($_SESSION)) {
    session_start();
}

// Store for localization
$PU_l10n = array();
foreach (glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'l10n'.DIRECTORY_SEPARATOR.'PureUpload_*.php') as $file) {
    // load all localization files
    include_once($file);
}

$DMX_uploadAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  if (!preg_match("/GP_upload=true/i", $_SERVER['QUERY_STRING'])) {
		$DMX_uploadAction .= "?".$_SERVER['QUERY_STRING']."&GP_upload=true";
	} else {
		$DMX_uploadAction .= "?".$_SERVER['QUERY_STRING'];
	}
} else {
  $DMX_uploadAction .= "?"."GP_upload=true";
}
$GP_uploadAction = $DMX_uploadAction;

abstract class PU_Base
{
    protected $upload;

    protected $path = '';
    protected $maxFileSize = 0;
    protected $allowedExtensions = '';
    protected $nameConflict = PU_CONFLICT_OVERWRITE;
    protected $storeType = PU_STORE_FILE;
    protected $nameToLower = FALSE;
    protected $required = FALSE;

    protected $minWidth = 0;
    protected $maxWidth = 0;
    protected $minHeight = 0;
    protected $maxHeight = 0;
    protected $saveWidth = '';
    protected $saveHeight = '';

    protected $redirectUrl = '';

    protected $oldProps = array();
    protected $newProps = array();

    protected function __construct(&$upload) {
        $this->upload =& $upload;
    }

    public function __get($name) {
        $name = str_replace($this->oldProps, $this->newProps, $name);

        if (method_exists($this, 'get'.$name)) {
            return call_user_func(array($this, 'get'.$name));
        } else {
            // throw error?
            $this->showWarning('Trying to get non-existing property %1', $name);
            return NULL;
        }
    }

    public function __set($name, $val) {
        $name = str_replace($this->oldProps, $this->newProps, $name);

        if (method_exists($this, 'set'.$name)) {
            Debugger::log('%s::%s => <b>%s</b>', get_class($this), $name, $val);
            call_user_func(array($this, 'set'.$name), $val);
        } else {
            // throw error?
            Debugger::error('%s::%s => <b>%s</b>', get_class($this), $name, $val);
            $this->upload->showWarning('Trying to set non-existing property %1', $name);
        }
    }

	public function __isset($name) {
		$name = str_replace($this->oldProps, $this->newProps, $name);
        if (method_exists($this, 'get'.$name)) {
			$local_name = $this->$name;
            return (false === empty($local_name));
        } else {
            return null;
        }
    }

    // Path (only support for relative and site absolute paths)

    public function getPath() {
        return $this->path;
    }

    public function setPath($val) {
        $val = str_replace('\\', '/', $val);
        if (strlen($val) > 1) {
            $val = preg_replace('%\/$%', '', $val);
        }
        if ($val == '') {
            $val = '.';
        }

        $this->path = $val;
    }


    public function getRedirectUrl() {
        return $this->redirectUrl;
    }

    public function setRedirectUrl($val) {
        $this->redirectUrl = $val;
    }


    // Max File Size

    public function getMaxFileSize() {
        return $this->maxFileSize;
    }

    public function setMaxFileSize($val) {
        $this->maxFileSize = $this->getBytesValue($val);

        $systemMax = $this->getBytesValue(ini_get('upload_max_filesize'));

        if ($this->maxFileSize > $systemMax) {
            $this->showWarning('upload_max_filesize', $val, ini_get('post_max_size'));
            $this->maxFileSize = $systemMax;
        }
    }

    // Allowed Extensions

    public function getAllowedExtensions() {
        return $this->allowedExtensions;
    }

    public function setAllowedExtensions($val) {
        $this->allowedExtensions = $val;
    }

    // Name To Lower

    public function getNameToLower() {
        return $this->nameToLower;
    }

    public function setNameToLower($val) {
        $this->nameToLower = $val;
    }

    // Required

    public function getRequired() {
        return $this->required;
    }

    public function setRequired($val) {
        $this->required = $val;
    }

    // Name Conflict

    public function getNameConflict() {
        return $this->nameConflict;
    }

    public function setNameConflict($val) {
        $this->nameConflict = $val;
    }

    // Store Type

    public function getStoreType() {
        return $this->storeType;
    }

    public function setStoreType($val) {
        $this->storeType = $val;
    }

    // Min and Max Image Dimensions

    public function getMinWidth() {
        return $this->minWidth;
    }

    public function setMinWidth($val) {
        $this->minWidth = intval($val);
    }

    public function getMaxWidth() {
        return $this->maxWidth;
    }

    public function setMaxWidth($val) {
        $this->maxWidth = intval($val);
    }

    public function getMinHeight() {
        return $this->minHeight;
    }

    public function setMinHeight($val) {
        $this->minHeight = intval($val);
    }

    public function getMaxHeight() {
        return $this->maxHeight;
    }

    public function setMaxHeight($val) {
        $this->maxHeight = intval($val);
    }

    public function setSaveWidth($val) {
        $this->saveWidth = $val;
    }

    public function setSaveHeight($val) {
        $this->saveHeight = $val;
    }

    public function getSaveWidth() {
		return $this->saveWidth;
    }

    public function getSaveHeight() {
		return $this->saveHeight;
    }

    protected function showWarning($err) {
        global $PU_l10n;

        if (isset($PU_l10n[$this->upload->locale][$err])) {
            $err = $PU_l10n[$this->upload->locale][$err];
        }

        for ($i = 1; $i < func_num_args(); $i++) {
			$func_args = func_get_arg($i);
            $err = str_replace('%'.$i, $func_args, $err);
        }

        Debugger::warn($err);

        print '<br><b>Upload Warning</b>: '.$err.'<br>';
    }

    protected function showError($err) {
        global $PU_l10n;

        if (isset($PU_l10n[$this->upload->locale][$err])) {
            $err = $PU_l10n[$this->upload->locale][$err];
        }

        for ($i = 1; $i < func_num_args(); $i++) {
			$func_args = func_get_arg($i);
            $err = str_replace('%'.$i, $func_args, $err);
        }

        Debugger::error($err);

        exit('<h1>Upload Error</h1><b>'.$err.'</b>');
    }

    protected function getBytesValue($size) {
        preg_match('/(\d+)(\w)?/', $size, $m);
        $val = intval($m[1]);

        if (isset($m[2])) {
            switch (strtoupper($m[2])) {
                case 'G' : $val *= 1024;
                case 'M' : $val *= 1024;
                case 'K' : $val *= 1024;
            }
        }

        return $val;
    }

    protected function getBytesString($size) {
        if ($size < 1024) {
            return $size;
        }
        if ($size < (1024*1024)) {
            return round($size / 1024).'K';
        }
        if ($size < (1024*1024*1024)) {
            return round($size / (1024*1024), 2).'M';
        }
        return round($size / (1024*1024*1024), 2).'G';
    }
}

class PureFileUpload extends PU_Base
{
    private $done = FALSE;
    private $locale = 'en';

    private $haltOnErrors = TRUE;
    private $raiseErrors = FALSE;
    private $maxFormSize = 0;

    private $progressId = '';
    private $progressBar = '';
	private $progressUrl = '';
    private $progressWidth = 300;
    private $progressHeight = 100;

    protected $oldProps = array('requireUpload', 'extensions','maxSize');
    protected $newProps = array('required', 'allowedExtensions','maxFormSize');

    private $files = array();

    // Constructor

    public function __construct($version = NULL) {
        parent::__construct($this);

        Debugger::init();

        Debugger::log('<b>Pure PHP Upload version %s running on PHP %s</b>', PU_VERSION, PHP_VERSION);

        if (isset($version)) {
            $this->checkVersion($version);
        }

        $this->progressId = isset($_GET['UploadId']) ? $_GET['UploadId'] : uniqid();
		$this->progressUrl = $this->getScriptLibrary().'PU_Progress.php&UploadId='.$this->progressId;
    }

    // Relative path to ScriptLibrary

    public function getScriptLibrary() {
        $path1 = trim(str_replace('\\', '/', getcwd()), '/');
        $path2 = trim(str_replace('\\', '/', dirname(__FILE__)), '/');
        while (substr_count($path1, '//')) $path1 = str_replace('//', '/', $path1);
        while (substr_count($path2, '//')) $path2 = str_replace('//', '/', $path2);

        //create arrays
        $arr1 = explode('/', $path1);
        if ($arr1 == array('')) $arr1 = array();
        $arr2 = explode('/', $path2);
        if ($arr2 == array('')) $arr2 = array();
        $size1 = count($arr1);
        $size2 = count($arr2);

        //now the hard part :-p
        $path='';
        for($i=0; $i<min($size1,$size2); $i++)
        {
            if ($arr1[$i] == $arr2[$i]) continue;
            else $path = '../'.$path.$arr2[$i].'/';
        }
        if ($size1 > $size2)
            for ($i = $size2; $i < $size1; $i++)
                $path = '../'.$path;
        else if ($size2 > $size1)
            for ($i = $size1; $i < $size2; $i++)
                $path .= $arr2[$i].'/';

        return $path;
    }

    // Version

    public function getVersion() {
        return PU_VERSION;
    }

    public function checkVersion($version) {
        if (version_compare(PU_VERSION, $version, '<')) {
            $this->showWarning('version %1 was requested, version %2 was found.', $version, PU_VERSION);
        }
    }

    // Done

    public function getDone() {
        return $this->done;
    }

    // UploadId

    public function getUploadId() {
        return $this->progressId;
    }

    // Locale (language)

    public function getLocale() {
        return $this->locale;
    }

    public function setLocale($val) {
        global $PU_l10n;

        if (isset($PU_l10n[$val])) {
            $this->locale = $val;
        } else {
            $this->showWarning('locale not found!');
        }
    }

    // Timeout

    public function setTimeout($val) {
        @set_time_limit($val);
    }

    // Max Form Size

    public function getMaxFormSize() {
        return $this->maxFormSize;
    }

    public function setMaxFormSize($val) {
        $this->maxFormSize = $this->getBytesValue($val);

        $systemMax = $this->getBytesValue(ini_get('post_max_size'));

        if ($this->maxFormSize > $systemMax) {
            $this->showWarning('post_max_size', $val, ini_get('post_max_size'));
            $this->maxFormSize = $systemMax;
        }
    }

    // Progress params

    public function getProgressField() {
        $name = 'UPLOAD_IDENTIFIER';
        if (ini_get_bool('session.upload_progress.enabled')) {
            $name = ini_get("session.upload_progress.name");
        } elseif (ini_get_bool('apc.rfc1867')) {
            $name = ini_get("apc.rfc1867_name");
        }
        return sprintf('<input type="hidden" name="%s" value="%s" />', $name, $this->progressId);
    }

    public function setProgressBar($val) {
        $this->progressBar = $val;
    }

    public function setProgressWidth($val) {
        $this->progressWidth = intval($val);
    }

    public function setProgressHeight($val) {
        $this->progressHeight = intval($val);
    }

    // Our uploaded file collection

    public function files($field) {
        if (!isset($this->files[$field])) {
            $this->files[$field] = new FileField($this, $field);
        }

        return $this->files[$field];
    }

    // Backward compatibility with version 2

    public function setFormName($val) {
        // do nothing with this
    }

    public function setSizeLimit($val) {
        $this->maxFormSize = $val * 1024;
    }

    public function getUploadedFiles() {
        return $this->files;
    }

    public function createUniqName($filename) {
        $path = realpath($this->path).DIRECTORY_SEPARATOR;
        $name = substr($filename, 0, strrpos($filename, '.'));
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $num = 0;

        Debugger::log('Creating a unique name.');

        while (++$num) {
            $filename = $name.'_'.$num.'.'.$ext;

            if (!file_exists($path.$filename)) {
                return $filename;
            }

            if ($num == 100) {
                $this->showError('Can\'t create unique name.');
            }
        }
    }

    // Javascript Code Generation

    public function generateScriptCode() {
        global $PU_l10n;

        return sprintf("var PU3_ERR_EXTENSION = '%s';\nvar PU3_ERR_REQUIRED = '%s';", $PU_l10n[$this->locale]['extension'], $PU_l10n[$this->locale]['required']);
    }

    public function getSubmitCode() {
        $req = $this->required ? 'true' : 'false';
        $str = sprintf("validateForm(this, '%s', %s);", $this->allowedExtensions, $req);

        if ($this->progressBar <> '') {
            $str .= sprintf("showProgressWindow('%s?ProgressUrl=%s', %s, %s, this);", $this->progressBar, $this->progressUrl, $this->progressWidth, $this->progressHeight);
        }

        return $str;
    }

    public function getValidateCode() {
        $req = $this->required ? 'true' : 'false';
        return sprintf("validateFile(this, '%s', %s)", $this->allowedExtensions, $req);
    }

    // Progress the files

    public function doUpload($saveFiles = TRUE) {
        // Ignore if there was not form post
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            Debugger::log('No form post detected');
            return;
        }

        // total form size was larger than php allows (collections are not filled)
        if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $this->showError('Posted data is larger than php can handle!');
        }

        // validate submitted form size
        if ($this->maxFormSize > 0 && $_SERVER['CONTENT_LENGTH'] > $this->maxFormSize) {
            $this->showError('size', $this->getBytesString($_SERVER['CONTENT_LENGTH']), $this->getBytesString($this->maxFormSize));
        }

        // loop through all uploaded files
        array_walk($_FILES, array($this, 'validateField'));

        // Here is the place for future add-on code

        // Save the files to disk
        if ($saveFiles) {
            foreach ($this->files as $file) {
                $file->save();
            }
        }

        $this->done = TRUE;

		// Recreate the redirectURL
		if ($this->redirectUrl != '' && !isset($_POST['FlashUpload'])) {
		  header(sprintf("Location: %s", $this->redirectUrl));
		}
    }

    // Validate File Field

    private function validateField($upload, $field) {
        $file = $this->files($field);

        $file->update();

        // Do we have an error? ignore the no file uploaded error
        if ($upload['error'] != UPLOAD_ERR_OK && $upload['error'] != UPLOAD_ERR_NO_FILE) {
            switch ($upload['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $this->showError('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
                case UPLOAD_ERR_FORM_SIZE:
                    $this->showError('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
                case UPLOAD_ERR_PARTIAL:
                    $this->showError('The uploaded file was only partially uploaded.');
                case UPLOAD_ERR_NO_FILE:
                    $this->showError('No file was uploaded.');
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->showError('Missing a temporary folder.');
                case UPLOAD_ERR_CANT_WRITE:
                    $this->showError('Failed to write file to disk.');
                case UPLOAD_ERR_EXTENSION:
                    $this->showError('A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.');
            }
            $this->showError('ERRORCODE %1', $upload['error']);
        }

        // Check if fileupload was required
        if ($file->required && $upload['error'] == UPLOAD_ERR_NO_FILE) {
            $this->showError('required', $field);
        }

		// No file? nothing more to validate
        if ($upload['error'] == UPLOAD_ERR_NO_FILE) {
            return;
        }

        if (!is_uploaded_file($_FILES[$field]['tmp_name'])) {
            $this->showError('Possible file upload attack: %s', $_FILES[$field]['tmp_name']);
        }

        // Check filesize
        if ($file->maxFileSize > 0 && $upload['size'] > $file->maxFileSize) {
            $this->showError('size', $this->getBytesString($upload['size']), $this->getBytesString($file->maxFileSize));
        }

        // Check if file extension is allowed
        if ($file->allowedExtensions != '') {
            $allowed = FALSE;

            foreach (explode(',', $file->allowedExtensions) as $ext) {
                Debugger::log('Check extension %s with %s', strtoupper($file->extension), strtoupper($ext));

                if (strtoupper($file->extension) == strtoupper($ext)) {
                    $allowed = TRUE;
                    break;
                }
            }

            if (!$allowed) {
                $this->showError('extension', strtoupper($file->extension), strtoupper($file->allowedExtensions));
            }
        }

        // Check image dimensions
        if ($file->width > 0 && $file->height > 0) {
            if (($file->minWidth > 0 && $file->width < $file->minWidth) || ($file->minHeight > 0 && $file->height < $file->minHeight)) {
                $this->showError('smallSize', $file->width, $file->height, $file->minWidth, $file->minHeight);
            }

            if (($file->maxWidth > 0 && $file->width > $file->maxWidth) || ($file->maxHeight > 0 && $file->height > $file->maxHeight)) {
                $this->showError('bigSize', $file->width, $file->height, $file->maxWidth, $file->maxHeight);
            }
        }

        $this->files[$field] = $file;
    }

    // Add-on support

    var $addOns = array();

    // Register addons and put them in an array
    public function registerAddOn(&$addOn) {
        array_push($this->addOns, $addOn);
    }

    public function failUpload() {
        // Call cleanup method from each registered addon
        foreach ($this->addOns as $addOn) {
            $addOn->cleanUp();
        }

        // Check if some files are already uploaded
        foreach ($this->files as $file) {
            $path = $file->path.'/'.$file->filename;
            if (is_file($path)) {
                @chmod($path, 0666);
                @unlink($path);
            }
        }
    }
}

class FileField extends PU_Base
{
    private $field;
    private $tempfile;
    private $filename;
    private $type;
    private $size;
    private $value;
    private $width = 0;
    private $height = 0;

    private $illegalCharacters = '!@$&*()?:[]"<>\'^`|={}\\/,;';

    protected $oldProps = array('fileSize', 'imageWidth', 'imageHeight','filePath');
    protected $newProps = array('size', 'width', 'height','path');

    public function __construct(&$upload, $field) {
        parent::__construct($upload);

        $this->field = $field;

        $sync = array(
            'path',
            'maxFileSize',
            'allowedExtensions',
            'nameConflict',
            'storeType',
            'required',
            'minWidth',
            'maxWidth',
            'minHeight',
            'maxHeight'
        );

        array_walk($sync, array($this, 'sync'));
    }

    public function getValidateCode() {
        $req = $this->required ? 'true' : 'false';
        return sprintf("validateFile(this, '%s', %s)", $this->allowedExtensions, $req);
    }


    public function update() {
        if (isset($_FILES[$this->field])) {
            $this->setFilename($_FILES[$this->field]['name']);
            $this->tempfile = $_FILES[$this->field]['tmp_name'];
            $this->type = $_FILES[$this->field]['type'];
            $this->size = $_FILES[$this->field]['size'];

            if (($size = @getimagesize($this->tempfile)) !== FALSE) {
                Debugger::log('Detected image dimensions: %sx%s', $size[0], $size[1]);
                $this->width = $size[0];
                $this->height = $size[1];
            }

            $this->updatePost();
        }

        if ($this->saveWidth != '') {
            $_POST[$this->saveWidth] = $this->width;
        }

        if ($this->saveHeight != '') {
            $_POST[$this->saveHeight] = $this->height;
        }
    }

    public function updatePost() {
        $_POST[$this->field] = '';

        if ($this->filename != '') {
            switch ($this->storeType) {
                case PU_STORE_ABSURL:
                    $pos = strrpos($_SERVER['REQUEST_URI'], '/');
                    $this->value = $this->url_remove_dot_segments(substr($_SERVER['REQUEST_URI'], 0, $pos).'/'.$this->path).'/'.$this->filename;
                    break;
                case PU_STORE_FILE:
                    $this->value = $this->filename;
                    break;
                case PU_STORE_SYSTEM:
                    $this->value = realpath($this->path).DIRECTORY_SEPARATOR.$this->filename;
                    break;
            }
            $_POST[$this->field] = $this->value;
        }
    }


    private function url_remove_dot_segments( $path ) {
        // multi-byte character explode
        $inSegs  = preg_split( '!/!u', $path );
        $outSegs = array( );
        foreach ( $inSegs as $seg )
        {
            if ( $seg == '' || $seg == '.')
                continue;
            if ( $seg == '..' )
                array_pop( $outSegs );
            else
                array_push( $outSegs, $seg );
        }
        $outPath = implode( '/', $outSegs );
        if ( $path[0] == '/' )
            $outPath = '/' . $outPath;
        // compare last multi-byte character against '/'
        if ( $outPath != '/' &&
            (mb_strlen($path)-1) == mb_strrpos( $path, '/', 0, 'UTF-8' ) )
            $outPath .= '/';
        return $outPath;
    }

    public function getField() {
        return $this->field;
    }

    public function getValue() {
        return $this->value;
    }


    public function setNameToLower($val) {
        parent::setNameToLower($val);

        if ($val && isset($this->filename)) {
            $this->filename = $this->cleanFilename($this->filename);
        }
    }

    public function setFilename($val) {
        $this->filename = $this->cleanFilename($val);
		$this->updatePost();
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getTempFile() {
        return $this->tempfile;
    }

    public function getName() {
        return substr($this->filename, 0, strrpos($this->filename, '.'));
    }

    public function getExtension() {
        return substr($this->filename, strrpos($this->filename, '.') + 1);
    }

    public function getSize() {
        return $this->size;
    }

    public function getType() {
        return $this->type;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

	public function setImageSize($width, $height) {
	    $this->width = $width;
	    $this->height = $height;
    	//$this->upload->files[$this->field] = $this;
	}

    public function save() {
        if ($this->filename != '') {
            if (!is_dir($this->path)) {
                Debugger::log('Path %s doesn\'t exist or is no directory', $this->path);

                if (!@mkdir($this->path, 0777, TRUE)) {
                    $this->showError('permission', $this->path);
                } else {
                    Debugger::log('Path %s created', $this->path);
                }
            }

            if ($realPath = realpath($this->path)) {
                if (file_exists($realPath.DIRECTORY_SEPARATOR.$this->filename)) {
                    Debugger::warn('File %s already exists', $this->filename);

                    switch ($this->nameConflict) {
                        case PU_CONFLICT_ERROR:
                            $this->showError('exists', $this->filename);
                        case PU_CONFLICT_IGNORE:
                            return;
                        case PU_CONFLICT_UNIQUE:
                            $this->createUniqFilename();
                    }
                }

                if (move_uploaded_file($this->tempfile, $realPath.DIRECTORY_SEPARATOR.$this->filename)) {
                    Debugger::log('Save file %s', $realPath.DIRECTORY_SEPARATOR.$this->filename);
                    @chmod($destination, 0644);
                } else {
                    $this->showError('writePerm', $this->filename, $this->path);
                }
            } else {
                $this->showError('incorrect path', $this->path);
            }
        }
    }

    private function createUniqFilename() {
        $path = realpath($this->path).DIRECTORY_SEPARATOR;
        $num = 0;

        Debugger::log('Creating a unique name.');

        while (++$num) {
            $filename = $this->name.'_'.$num.'.'.$this->extension;

            if (!file_exists($path.$filename)) {
                $this->setFilename($filename);
                return;
            }

            if ($num == 100) {
                $this->showError('Can\'t create unique name.');
            }
        }
    }

    private function cleanFilename($name) {
        $newname = $name;

        // remove illegal characters
        $newname = preg_replace('%['.preg_quote($this->illegalCharacters).']%i', '', $newname);
        // remove control characters
        $newname = preg_replace('%[\0-\31\177]%i', '', $newname);
        // replace spaces with underscore
        $newname = preg_replace('%\s+%i', '_', $newname);
        // make lowercase if required
        if ($this->nameToLower) $newname = strtolower($newname);

        if ($name != $newname) {
            Debugger::log('Filename changed from %s to %s', $name, $newname);
        }

        return $newname;
    }

    private function sync($prop) {
        @call_user_func(array($this, 'set'.$prop), call_user_func(array($this->upload, 'get'.$prop)));
    }
}

abstract class PureUploadAddon
{
    protected $upload;

    public function PureUploadAddon(&$upload) {
        $this->upload =& $upload;
    }

    public function cleanUp() {
    }
}


class Debugger
{
    public static $console = FALSE;

    public static function init() {
        if (isset($_GET['debug']) && $_GET['debug'] == 'console') {
            self::$console = TRUE;
        }
    }

    public static function log($str) {
        global $DMX_debug;

        $args = self::prepare_args(func_get_args());

        if ($DMX_debug) {
            vprintf($str.'<br>', $args);
        }

        if (self::$console) {
            vprintf(self::prepare_console($str), $args);
        }
    }

    public static function warn($str) {
        $args = self::prepare_args(func_get_args());

        if (self::$console) {
            vprintf(self::prepare_console($str, 'warn'), $args);
        }
    }

    public static function error($str) {
        $args = self::prepare_args(func_get_args());

        if (self::$console) {
            vprintf(self::prepare_console($str, 'error'), $args);
        }
    }

    private static function prepare_args($args) {
        array_shift($args);

        if (self::$console) {
            $args = array_map(array('self', 'prepare_string'), $args);
        }

        return $args;
    }

    private static function prepare_string($str) {
        if (is_bool($str)) {
            $str = $str === FALSE ? 'false' : 'true';
        }

        return addslashes(strip_tags($str));
    }

    private static function prepare_console($str, $type = 'log') {
        return '<script type="text/javascript">console && console.'.$type.' && console.'.$type.'("'.self::prepare_string($str).'");</script>';
    }
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
?>