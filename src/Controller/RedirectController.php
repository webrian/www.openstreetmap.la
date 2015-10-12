<?php

namespace App\Controller;

use App\Controller\AppController;

class RedirectController extends AppController {

    public function beforeFilter() {
        $this->autoRender = false;
    }

    public function index() {

        if (isset($this->request->query['u'])) {

            $externalUrl = $this->request->query('u');

            $externalDomain = explode('?', $externalUrl);

            $this->redirect($externalUrl, 404, true);
        } else {
            $this->redirect(array('controller' => 'sites', 'action' => 'main'));
        }
    }

}
