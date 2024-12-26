#!/usr/bin/php -q
<?php
// Check platform requirements
require dirname(__DIR__) . '/config/requirements.php';
require dirname(dirname(__DIR__)) . '/../../vendor/autoload.php';

use App\Application;
use Cake\Console\CommandRunner;

define('CAKE_CORE_INCLUDE_PATH', realpath(dirname(dirname(__DIR__)) . '/../../vendor' . DS . 'cakephp' . DS . 'cakephp'));
// Build the runner with an application and root executable name.
$runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake');
exit($runner->run($argv));