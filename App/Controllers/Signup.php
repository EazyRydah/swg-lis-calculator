<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

/** 
 * Signup controller
 * 
 * PHP version 7.0
 * */  
class Signup extends \Core\Controller
{
    /**
     * Require the user to be an admin before giving access to all methods in the controller
     * 
     * @return void
    */ 
    protected function before()
    {
        $this->requireAdmin();
    }

    /**
     * show the signup page
     * 
     * @return void
    */  
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    /**
     * Sign up a new user
     * 
     * @return void 
    */  
    public function createAction()
    {

        $user = new User($_POST);

        if ($user->save()) {
            
            $this->redirect('/admin/show-users');

        } else {

            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);

        }
    }

    /**
     * Delete existing user
     * 
     * @param $id The user ID
     * 
     * @return void 
    */  
    public function deleteAction()
    {

        $user = User::findByID($this->route_params['id']);

        View::renderTemplate('Signup/delete.html', [
            'user' => $user
        ]);
       
    }

    /**
     * Confirm user deletion
     * 
     * @return void
    */ 
    public function confirmDeletionAction()
    {
        $user = User::findByID($this->route_params['id']);

        if ($user->id == $_SESSION['user_id']) {
            Flash::addMessage('Der Administrator kann nicht gelöscht werden.');
            $this->redirect('/admin/show-users');

        } else {
            $user->delete();
        }

        $this->redirect('/admin/show-users');
    }

   
    /**
     * Show the signup success page
     * 
     * @return void 
     * */  
    public function successAction()
    {
        $this->redirect('/admin');
    }

}