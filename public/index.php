<?php
// define driver
define('DRIVER_FILE', $_SERVER['SCRIPT_FILENAME']);
define('DRIVER_URI', $_SERVER['SCRIPT_NAME']);
define('DRIVER_DIR', __DIR__);

// DEFAULTS
define('DEFAULT_ENTRY', 'html');
define('DEFAULT_LANG', 'en_US');
define('DEFAULT_REGION', 'guest');
define('DEFAULT_MODULE', 'home');
define('DEFAULT_ACTION', 'index');

// point to common file
require_once dirname(DRIVER_DIR) . '/common.php';

// config file
define('CONFIG_FILE', VAR_CONFIGS_DIR . '/app.php'); 

use icore\Utilities;
use icore\Application;

// begin timer
Utilities::timer();
Application::run();

