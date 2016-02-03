<?php
/**
Header file that sets up some general php functions for running a script as both
command line and from the web. This should be moved to its own repo to be included in other scripts
**/

// Require and setup the logger class
require_once 'clilogger.class.php';

// Setup the cli logger
$logger = new \rp\phpcli\Logger();

set_error_handler(array($logger, 'errorHandler'));
set_exception_handler(array($logger, 'exceptionHandler'));

$logger->setTimeZone(new \DateTimeZone('America/Denver'));

// Install POSIX signal handlers
if (extension_loaded('pcntl')) {
    declare(ticks=100);
    pcntl_signal(SIGTERM, function($signo) { exit; });
    pcntl_signal(SIGINT, function($signo) { exit; });
}

// Disable php script limit for these types of scripts
set_time_limit(0);

// Check if this script is running from the command line
if (php_sapi_name() == 'cli') {
    define("PHP_CLI", true);
} else {
    define("PHP_CLI", false);
}

// On script end, add new lines
function cli_shutdown() {
    global $logger;

    $logger->log($logger::NOTICE, 'Shutting down');
    $logger->logBreak();
}
register_shutdown_function('cli_shutdown');

// General setup
if (PHP_CLI) {
    // Send errors to stderr
    ini_set('display_errors', 'stderr');
    error_reporting(-1);

    // Pull out command line parameters and put in in to $_GET
    foreach ($argv as $arg) {
        $e = explode("=", $arg);
        if (count($e) == 2) {
            $_GET[$e[0]] = $e[1];
        } else {
            $_GET[$e[0]] = 0;
        }
    }
} else {
    // Disable browser caching
    header('Content-type: text/html; charset=utf-8');
    echo "\n<pre>";
}

// Default way to load variables from the env or set a default
function load_var($name, $default_value = null) {
    if (!isset($_GET[$name]) && getenv($name) !== false) {
        $_GET[$name] = getenv($name);
    }

    if (!isset($_GET[$name]) && $default_value !== null) {
        $_GET[$name] = $default_value;
    }

    return isset($_GET[$name]);
}

// Set default env's from the environment
load_var('server_env');
load_var('db_env');
load_var('db_env_name');

// Tell the logger to log debug message
load_var('debug');
if (isset($_GET['debug'])) {
    $logger->setLogLevel(0);
}

load_var('loglevel');
if (isset($_GET['loglevel'])) {
    $logger->setLogLevel($_GET['loglevel']);
}

load_var('errlevel');
if (isset($_GET['errlevel'])) {
    $logger->setErrLevel($_GET['errlevel']);
}
