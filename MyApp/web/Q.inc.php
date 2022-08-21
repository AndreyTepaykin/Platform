<?php

//
// Constants -- you might have to change these
//
if (!defined('APP_DIR')) {
	define ('APP_DIR', dirname(dirname(__FILE__)));
}

//
// Include Q
//
$header = "<html><body style='padding: 10px;'><h1>This is a Qbix project...</h1>\n";
$footer = "</body></html>";
if (!is_dir(APP_DIR)) {
	die("$header\nPlease edit index.php and change APP_DIR to point to your app's directory.\n$footer");
}

if (!defined('Q_DIR')) {
	$paths_filename = realpath(APP_DIR . '/local/paths.json');
	if (file_exists($paths_filename)) {
		$paths = json_decode(file_get_contents($paths_filename), true);
		define('Q_DIR', $paths['platform']);
	} else {
		$paths_filename = realpath(APP_DIR . '/local/paths.php');
		if (file_exists($paths_filename)) {
			include($paths_filename); // for backward compatibility
		} else {
			$basename = basename(APP_DIR);
			die("$header\nGo to $basename/scripts/Q directory and run php configure.php\n$footer");
		}
	}
}

$Q_filename = realpath(Q_DIR.'/Q.php');
if (!file_exists($Q_filename)) {
	$basename = basename(APP_DIR);
	die("$header\nPlease edit $basename/local/paths.php and $basename/local/paths.js to indicate the location of the Q/platform directory\n$footer");
}

include($Q_filename);