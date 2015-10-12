<?php

namespace App\Controller;

use Cake\Core\Configure;

class DownloadsController extends AppController {

    public function main($country = Null, $file = Null) {

        // Construct the path to the requested file
        $filepath = Configure::read("DataDirectory") . DS . ucfirst($country) . DS . $file;

        // Set the response type
        $this->response->type('application/octet-stream');
        // Return the file and force the download
        // A 404 NotFound exception is thrown if file does not exist
        $this->response->file($filepath, ['download' => true]);
        return $this->response;
    }

}

?>
