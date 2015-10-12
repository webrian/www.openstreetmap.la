<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\Router;

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
 * If no call is made to `Router::defaultRouteClass`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass('Route');

Router::scope('/', function ($routes) {

    $routes->connect('/downloads/:country/:file',
                    ['controller' => 'Downloads', 'action' => 'main'],
                    ['pass' => ['country', 'file'],
                     'country' => '[a-zA-Z]+',
                     'file' => '[0-9a-zA-Z\.\-\_]+']);
    $routes->connect('/files/:country/:file',
                    ['controller' => 'Downloads', 'action' => 'main'],
                    ['pass' => ['country', 'file'],
                     'country' => '[a-zA-Z]+',
                     'file' => '[a-zA-Z0-9\.\-\_]+']);

    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'main', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'main', 'map']);
    
    $routes->connect('/map', ['controller' => 'Pages', 'action' => 'main', 'map']);
    
    $routes->connect('/downloads', ['controller' => 'Pages', 'action' => 'main', 'downloads']);
    
    $routes->connect('/edit', ['controller' => 'Pages', 'action' => 'main', 'edit']);

    $routes->connect('/about', ['controller' => 'Pages', 'action' => 'main', 'about']);
    
    $routes->connect('/lang.js', ['controller' => 'Lang', 'action' => 'index']);
    
    // Just to preserve already published URL
    $routes->connect('/files/cambodia', ['controller' => 'sites', 'action' => 'main', 'downloads']);
    
    /**
     * Define the route to the OpenSearch description
     */
    $routes->connect('/places.xml', ['controller' => 'Places', 'action' => 'opensearchdescription']);
    
    /**
     * Set the route to the Landsat 8 subpage
     */
    $routes->connect('/landsat8', ['controller' => 'Pages', 'action' => 'display', 'landsat8']);

    /**
     * Set the route to the topographic map subpage
     */    
    $routes->connect('/topomap', ['controller' => 'Pages', 'action' => 'display', 'topomap']);

    $routes->connect('/privacy', ['controller' => 'Pages', 'action' => 'privacy']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `InflectedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'InflectedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'InflectedRoute']);`
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
    $routes->fallbacks('InflectedRoute');
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
