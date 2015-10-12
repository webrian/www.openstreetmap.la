<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Controller\Component\CookieComponent;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Network\Session;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{

    /**
     * Associative array of two-letter language keys to three-letter langague
     * keys.
     * @var array
     */
    protected $_languages = array(
        'lo' => 'lo_LA',
        'en' => 'en_US'
    );
    protected $_languageCode = null;
    protected $_languageCookie = '__LANG__';
    protected $_cookieIsEncrypted = false;
    protected $_expiration = '30 Days';
    
    public function initialize() {
        parent::initialize();
        $this->loadComponent('Cookie', ["encryption" => $this->_cookieIsEncrypted]);
    }


    public function beforeFilter( Event $event ) {
    
        // Extract the language and set it to the view
        $lang = $this->_extractLanguage();
        $this->set('lang', $lang);
        // Store it as a class property as well
        $this->_languageCode = $lang;
        
        // Write the language to a cookie
        $this->Cookie->write([$this->_languageCookie => $lang]);

        // Set the language to the session and Configuration
        $this->request->session()->write('Config.language', $this->_languages[$lang]);
        Configure::write('Config.language', $this->_languages[$lang]);
        I18n::locale($this->_languages[$lang]);
    }

    /**
     * Displays a view
     *
     * @return void|\Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display()
    {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }
    
    public function main($tab = 'map') {
    
        //$this->Cookie
//        echo var_dump($this->request->session()->read("Config.language"));

        // Check first if the requested tab is valid (should always be true...)
        if (in_array($tab, array('map', 'edit', 'downloads', 'about'))) {
            $this->set('tab', $tab);
        } else {
            throw new NotFoundException();
        }

        $geohash = new Geohash();

        // Checks if a Cookie is set. Expects whitespace separated longitude
        // latitude and zoom level.
        $initLocations = explode(" ", $this->Cookie->read('__LOCATION__'));

        // If a Cookie is set, take the coordinates from the cookie, else
        // set some default values
        if (isset($initLocations[0]) && $initLocations[0] != NULL) {
            $lng = $initLocations[0];
        } else {
            $lng = 103.75;
        }
        if (isset($initLocations[1]) && $initLocations[1] != NULL) {
            $lat = $initLocations[1];
        } else {
            $lat = 18.25;
        }
        if (isset($initLocations[2]) && $initLocations[2] != NULL) {
            $zoom = $initLocations[2];
        } else {
            $zoom = 6;
        }

        // Check if a search place is requested indicated with parameter q
        if (isset($this->request->query['q'])) {
            $places = TableRegistry::get('Places');
            // Get the search string
            $search = $this->request->query('q');

            // Query the database
            $results = $places->find()
                           ->where(["name ~*" => $search])
                           ->limit(1)
                           ->all();

            if ($results->count() > 0) {
                $res = $results->first();
                $lng = $mlng = $res['lon'];
                $lat = $mlat = $res['lat'];
                $zoom = 14;
            }
        }

        // Check first if marker position is set but no center coordinates
        if (isset($this->request->query['mlat']) && !isset($this->request->query['lat'])) {
            $mlat = $this->request->query['mlat'];
            // Set the center coordinates to the marker
            $lat = $this->request->query['mlat'];
        } elseif (isset($this->request->query['mlat']) && isset($this->request->query['lat'])) {
            $mlat = $this->request->query['mlat'];
            // Set the center coordinates to the marker
            $lat = $this->request->query['lat'];
        } else {
            if (isset($this->request->query['lat'])) {
                $lat = $this->request->query['lat'];
            }
        }
        $this->set('lat', $lat);

        // Check first if marker position is set but no center coordinates
        if (isset($this->request->query['mlon']) && !isset($this->request->query['lon'])) {
            $mlng = $this->request->query['mlon'];
            // Set the center coordinates to the marker
            $lng = $this->request->query['mlon'];
        } elseif (isset($this->request->query['mlon']) && isset($this->request->query['lon'])) {
            $mlng = $this->request->query['mlon'];
            // Set the center coordinates to the marker
            $lng = $this->request->query['lon'];
        } elseif (isset($this->request->query['lon'])) {
            $lng = $this->request->query['lon'];
        }
        $this->set('lng', $lng);

        // Do anyway the zoom
        if (isset($this->request->query['zoom'])) {
            $zoom = $this->request->query['zoom'];
        }
        $this->set('zoom', $zoom);

        if (isset($mlng)) {
            $this->set('mlng', $mlng);
        }
        if (isset($mlat)) {
            $this->set('mlat', $mlat);
        }

        // Check also routing start and destination
        if (isset($this->request->query['start'])) {
            $start = $this->request->query['start'];
            $startCoords = $geohash->decode($start);
            $this->set('startCoords', $startCoords);
        }
        if (isset($this->request->query['dest'])) {
            $dest = $this->request->query['dest'];
            $destCoords = $geohash->decode($dest);
            $this->set('destCoords', $destCoords);
        }
        if (isset($this->request->query['via'])) {
            $via = $this->request->query['via'];
            $vias = explode(',', $via);
            $viaCoords = array();
            foreach ($vias as $v) {
                array_push($viaCoords, $geohash->decode($v));
            }
            $this->set('viaCoords', $viaCoords);
        }

        // Get the current url
        $hereUrl = $this->request->here;

        // Append a slash at the end of the current url if it does not end with
        // one.
        if (!eregi("\/$", $hereUrl)) {
            $hereUrl .= '/';
        }

        $hereUrl .= '?lang=' . $this->_languageCode;

    }
    
    /**
     * Extract the requested language. The language is decided in the following
     * order:
     * 1. valid parameter lang set in GET request
     * 2. valid parameter __LANG__ set in cookie
     * 3. lao is preferred language
     * @return String Two-letter language identifier
     */
    protected function _extractLanguage() {
        // Default lanuage is always Lao
        $lang = 'lo';

        // Set the cookie name
        //$this->Cookie->name = '_osm_la';
        //echo $this->response->cookie("_osm_la");
        
        // First check if the language parameter is set in the URL. The URL
        // parameter has first priority.
        $paramLang = $this->request->query("lang");
        if (isset($paramLang)) {
            // Check if the URL parameter is a valid language identifier
            if (array_key_exists($paramLang, $this->_languages)) {
                // Set the language to the URL parameter
                $lang = $paramLang;
            }
        } else if ($this->Cookie->read($this->_languageCookie) != null) {
            // Check if a cookie is set and set its value as language. A Cookie
            // has second priority
            $cookieValue = $this->Cookie->read($this->_languageCookie);
            // Check if the URL parameter is a valid language identifier
            if (array_key_exists($cookieValue, $this->_languages)) {
                // Set the language to the Cookie value
                $lang = $cookieValue;
            }
        }

        // If neither the lang parameter nor a cookie is set, set and return
        // Lao as language.
        //$this->log(var_dump($this));
        //$this->response->cookie($this->_languageCookie => $lang ]);
        return $lang;
    }
    
    public function privacy()
    {
        $this->response->type("text/plain");
        return;
    }
}
