<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;

/**
 * PDF controller
 * 
 * PHP version 7.0
*/  
class PDF extends \Core\Controller
{
    /**
     * Require the user to be an admin before giving access to all methods in the controller
     * @return void
    */ 
    protected function before()
    {
        $this->requireAdmin();
    }

    /**
     * Upload PDF
     * 
     * @return void
     *   
    */  
    public function uploadAction()
    {
        View::renderTemplate('PDF/upload.html');
    }

    /**
     * Upload PDF
     * 
     * @return void
     *   
    */  
    public function createAction()
    {
        var_dump($_FILES);

        if (empty($_FILES)) {
            throw new Exception('Invalid upload');
        }

        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                throw new \Exception("No file uploaded.");
                break;

          /*   case UPLOAD_ERR_INI_SIZE:
                throw new \Exception("File is too large (from the server settings)");
                break; */

            default:
                throw new \Exception("An Error occured.");
        }

        if ($_FILES['file']['size'] > 5000000) {
            throw new \Exception("File is too large.");
        }

        $mime_types = ['application/pdf'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['file']['tmp_name']);

        if ( ! in_array($mime_type, $mime_types)) {

            throw new \Exception("Invalid file type.");

        }



        // Upload file
        $pathinfo = pathinfo($_FILES['file']['name']);

       /*  $base = $pathinfo['filename'];

        $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);

        $filename = $base . '.' . $pathinfo['extension']; */

        $filename = 'statistik-anhang' . '.' .  $pathinfo['extension'];

        $destination = "./uploads/$filename";

        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            
            echo "File uploaded successfully";

        } else {

            throw new \Exception('Unable to move uploaded file');

        }
    }

    


}