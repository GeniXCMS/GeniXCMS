<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");

if (isset($_GET['token']) && Token::isExist($_GET['token'])) {

	include_once Vendor::path('studio-42/elfinder').'php/elFinderConnector.class.php';
	include_once Vendor::path('studio-42/elfinder').'php/elFinder.class.php';
	include_once Vendor::path('studio-42/elfinder').'php/elFinderVolumeDriver.class.php';
	include_once Vendor::path('studio-42/elfinder').'php/elFinderVolumeLocalFileSystem.class.php';
	// Required for MySQL storage connector
	// include_once Vendor::path('studio-42/elfinder').'php/elFinderVolumeMySQL.class.php';
	// Required for FTP connector support
	// include_once Vendor::path('studio-42/elfinder').'php/elFinderVolumeFTP.class.php';


	/**
	 * Simple function to demonstrate how to control file access using "accessControl" callback.
	 * This method will disable accessing files/folders starting from '.' (dot)
	 *
	 * @param  string  $attr  attribute name (read|write|locked|hidden)
	 * @param  string  $path  file path relative to volume root directory started with directory separator
	 * @return bool|null
	 **/
	function access($attr, $path, $data, $volume) {
		return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
			? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
			:  null;                                    // else elFinder decide it itself
	}


	// set path for specific access
	// admin
	if(User::access(0)){
		$path = '/assets/';
		$allowed = array('image', 'audio', 'video', 'text/plain',
			'text/javascript', 'text/css', 'text/html');
	}elseif(User::access(2)){
		$path = '/assets/';
		$allowed = array('image', 'audio', 'video');
	}else{
		$path = '/assets/media/';
		$allowed = array('image', 'audio', 'video');
	}
	// Documentation for connector options:
	// https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
	$opts = array(
		// 'debug' => true,
		'roots' => array(
			array(
				'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
				'path'          => GX_PATH.$path,         // path to files (REQUIRED)
				'URL'           => Site::$url . $path, // URL to files (REQUIRED)
				'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)
				'uploadAllow' => $allowed,
				'uploadDeny' => array('application'),
				'uploadOrder'=> array( 'allow', 'deny' ),
				'alias'			=> 'Home'
			)
		)
	);

	// run elFinder
	$connector = new elFinderConnector(new elFinder($opts));
	$connector->run();

}
// echo "TOKEN EXIST";
