<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Network\Session;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

class LangController extends AppController {

    public function beforeFilter( Event $event ) {
        $this->autoRender = false;
        
        Configure::write('Config.language', $this->request->session()->read('Config.language'));
        I18n::locale($this->request->session()->read('Config.language'));
    }

    public function index() {
        $msg = "Ext.namespace('Ext.ux');";

        $translation = array(
            // Tab titles
            'Map' => __('Map'),
            'Edit' => __('Edit'),
            'Downloads' => __('Downloads'),
            'About this page' => __('About this page'),
            // Left column
            'Get directions' => __('Get directions'),
            'Search village, hotel, restaurant, shop, etc.' => __('Search village, hotel, restaurant, shop, etc.'),
            'Clear' => __('Clear'),
            'Search village, point of interest, etc.' => __('Search village, point of interest, etc.'),
            'Distance' => __('Distance'),
            '{0} km' => __('{0} km'),
            'Duration' => __('Duration'),
            '{1} h {2} mins' => __('{1} h {2} mins'),
            'Add start' => __('Add start'),
            'Double click to remove' => __('Double click to remove'),
            'No route found' => __('No route found'),
            'Server is currently unreachable or its answer is invalid.' => __('Server is currently unreachable or its answer is invalid.'),
            'Not found' => __('Not found'),
            'Click to add new via point' => __('Click to add new via point'),
            'Routing powered by ' => __('Routing powered by '),
            // Tooltips
            'Add destination' => __('Add destination'),
            'Drag to change route or double click to remove' => __('Drag to change route or double click to remove'),
            'Loading...' => __('Loading...'),
            'Satellite' => __('Satellite'),
            // Buttons
            'Lao' => __('Lao'),
            'English' => __('English'),
            'Permanent link to current map view' => __('Permanent link to current map view'),
            // Main content
            'edit the map' => __('edit the map'),
            'Link' => __('Link'),
            'Language' => __('Language')
        );

        // using getKey
        $msg .= "Ext.ux.ts=new Ext.util.MixedCollection();";
        $msg .= "Ext.ux.ts.tr=function(string){";
        $msg .= "return this.containsKey(string) ? this.get(string) : string;};";
        $msg .= "Ext.ux.ts.addAll(";
        $msg .= json_encode($translation);
        $msg .= ");";

        $this->response->type('javascript');
        $this->response->body($msg);
        return $this->response;
    }

}
