<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */


Router::defaultRouteClass(DashedRoute::class);

Router::extensions(['json']);
Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Home', 'action' => 'index', 'prefix' => 'user'], ['_name' => 'userTop']);
    $routes->connect('/logout', ['controller' => 'Home', 'action' => 'logout', 'prefix' => 'user'], ['_name' => 'logout']);

    $routes->connect('/admin', ['controller' => 'Admin', 'action' => 'index', 'prefix' => 'admin']);
    $routes->connect('/admin/logout', ['controller' => 'Admin', 'action' => 'logout', 'prefix' => 'admin']);

    $routes->connect('/view-reacts/:slug/**', ['controller' => 'ViewReacts', 'action' => 'index'])
           ->setPass(['slug']);

    $routes->connect('/view-tests/:slug/**', ['controller' => 'viewTests', 'action' => 'index'])
           ->setPass(['slug']);

    $routes->connect('/view/:slug/**', ['controller' => 'views', 'action' => 'index'])
           ->setPass(['slug']);

    $routes->connect('/content/:id', ['controller' => 'content', 'action' => 'index'])
           ->setPass(['id']);

    $routes->connect('/content/machine/:id', ['controller' => 'content', 'action' => 'machine'])
           ->setPass(['id']);

    $routes->connect('/material/:action', ['controller' => 'material']);
    $routes->connect('/material/:action/:id', ['controller' => 'material'])
            ->setPass(['id']);

    $routes->connect('/:controller/', ['action' => 'index', 'prefix' => 'user']);
    $routes->connect('/:controller/:action', ['prefix' => 'user']);
    $routes->connect('/:controller/:action/*', ['prefix' => 'user']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});

Router::prefix('admin', function ($routes) {
  
    $routes->fallbacks('DashedRoute');
});

Router::prefix('user', function ($routes) {

    // user
    $routes->connect('/*', ['controller' => 'Home', 'action' => 'topRedirect', 'prefix' => 'user']);
  
    $routes->fallbacks('DashedRoute');
});


Router::prefix('v1', function ($routes) {
  
    $routes->fallbacks('DashedRoute');
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */

