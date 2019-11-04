<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;

/**
 * Admin controller
 * 
 * PHP version 7.0
*/  
class Admin extends \Core\Controller
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
     * Calculator index
     * 
     * @return void
    */ 
    public function indexAction()
    {

        $users = User::getAll();
            
        View::renderTemplate('Admin/index.html', [
            'users' => $users
        ]);     
        
    }

    /**
     * Upload PDF
     * 
     * @return void
     *   
    */  
    public function uploadPDFAction()
    {
        View::renderTemplate('PDF/upload.html');
    }
}