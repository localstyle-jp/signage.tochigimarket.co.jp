#!/usr/bin/php -q
<?php
 // Bootstrap paths
$APP_DIR = dirname(__DIR__); // apps/web/app
$ROOT_DIR = dirname(dirname(dirname($APP_DIR))); // document root

// Check platform requirements and autoload
require $APP_DIR . '/config/requirements.php';
require $ROOT_DIR . '/vendor/autoload.php';

use App\Application;
use Cake\Console\CommandRunner;

// Build the runner with an application and root executable name.
$runner = new CommandRunner(new Application($APP_DIR . '/config'), 'cake');
exit($runner->run($argv));
