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

// Set default env's from the environment
if (getenv('server_env') !== false) {
    $_GET['server_env'] = getenv('server_env');
}

if (getenv('db_env') !== false) {
    $_GET['db_env'] = getenv('db_env');
}

if (getenv('db_env_name') !== false) {
    $_GET['db_env_name'] = getenv('db_env_name');
}

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

// Tell the logger to log debug message
if (isset($_GET['debug'])) {
    $logger->setLogLevel(0);
}

if (isset($_GET['loglevel'])) {
    $logger->setLogLevel($_GET['loglevel']);
}

if (isset($_GET['errlevel'])) {
    $logger->setErrLevel($_GET['errlevel']);
}

function load_var($name, $default = null) {
    if (!isset($_GET[$name]) && getenv($name) !== false) {
        $_GET[$name] = getenv($name);
    }

    if (isset($_GET[$name])) {
        return true;
    } elseif ($default !== null) {
        $_GET[$name] = $default;
        return true;
    }
    return false;
}

// On script end, add new lines
function cli_shutdown() {
    global $logger;

    $logger->log($logger::NOTICE, 'Shutting down');
    $logger->logBreak();
}
register_shutdown_function('cli_shutdown');
