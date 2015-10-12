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

use Cake\Network\Exception\BadRequestException;

class PlacesController extends AppController {

    private $itemsPerPage = 15;

    /**
     * Returns an OpenSearch compatible description file. See also the OpenSearch specifications:
     * http://www.opensearch.org/Specifications/OpenSearch/1.1#OpenSearch_description_document
     */
    public function opensearchdescription() {
        // Set the correct content type
        $this->response->type('application/opensearchdescription+xml');
        // and return the default view
        return;
    }

    public function index() {
        // Set the content type to json
        $this->response->type('application/json');
        // Search the places
        $result = $this->searchPlace();
        // Set the result to the view
        $this->set("result", $result);
        // Finally return the default view
        return;
    }

    public function rss() {
        // Set the correct content type
        $this->response->type('application/rss+xml');
        $result = $this->searchPlace();
        $this->set('result', $result);
    }

    /**
     * Returns search suggestions that can be used in browsers.
     */
    public function suggest() {
        // Set the content type to json
        $this->response->type('application/json');
        $result = $this->searchPlace();
        $this->set('result', $result);
    }

    /**
     * Does the actual database query. Returns a result array with an array of
     * matching items and metadata.
     * The result looks like:
     * result: {
     *   'items':[
     *   {'name': name, 'lat': lat, 'lon': lon},
     *   {'name': name, 'lat': lat, 'lon': lon},
     *   ],
     *   'metadata':{
     *   'success': true,
     *   'totalResults': 100,
     *   etc.
     *   }
     * }
     * @return array
     */
    private function searchPlace() {

        $result = array();

        // An array that holds the matched records
        $items = array();
        // An associative array with metadata
        $metadata = array();

        $search = $this->request->query('q');
        if (!isset($search)) {
            throw new BadRequestException();
        }

        // Create the query condition. Use POSIX regular expressions in PostgreSQL,
        // see also: http://www.postgresql.org/docs/9.1/static/functions-matching.html
        $conditions = array("name ~* '$search'");

        // Get the requested page, default is the first page
        $page = 1;
        if (isset($this->request->query['p']) && $this->request->query['p'] > 0) {
            $page = $this->request->query('p');
        }

        // Calculate the offset
        $offset = ($page - 1) * $this->itemsPerPage;

        // Construct the SQL query
        $query = $this->Places->find()
                      ->where(["name ~*" => $search])
                      ->limit($this->itemsPerPage)
                      ->page($page);
        
        // Count the number of total results
        // limit and offset are ignored
        $totalResults = $query->count();
        
        // Use Geohash to encode the coordinates for the JSON output
        $geohash = new Geohash();

        // Loop all records and create an associative array per matching feature
        foreach ($query as $record) {
            $hash = $geohash->encode($record->get('lat'), $record->get('lon'));
            array_push($items, array(
                'name' => $record->get('name'),
                'hash' => $hash,
                'feature' => $record->get('feature'),
                'lat' => $record->get('lat'),
                'lon' => $record->get('lon'))
            );
        }

        // Write the metadata
        $metadata['totalResults'] = $totalResults;
        $metadata['startIndex'] = $offset;
        $metadata['itemsPerPage'] = $this->itemsPerPage;
        $metadata['startPage'] = $page;
        $metadata['success'] = true;
        $metadata['searchTerm'] = $search;
        $metadata['host'] = $this->request->host();
        $metadata['fullUrl'] = "http://" . $this->request->host() . $this->request->here();

        $result['data'] = $items;
        $result['metadata'] = $metadata;

        return $result;
    }

}
