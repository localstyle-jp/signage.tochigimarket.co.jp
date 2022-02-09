#!/usr/bin/php -q
<?php
// Check platform requirements
// require dirname(__DIR__) . '/config/requirements.php';
// require dirname(__DIR__) . '/vendor/autoload.php';
require '/home/test-signage/apps/web/app/config/requirements.php';
require '/home/test-signage/vendor/autoload.php';

use App\Application;
use Cake\Console\CommandRunner;

define('CAKE_CORE_INCLUDE_PATH', '/home/test-signage/vendor' . DS . 'cakephp' . DS . 'cakephp');
// Build the runner with an application and root executable name.
// $runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake');
$runner = new CommandRunner(new Application('/home/test-signage/apps/web/app/config'), 'cake');
exit($runner->run($argv));
