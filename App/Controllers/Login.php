<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Login controller
 * 
 * PHP version 7.0 */  
class Login extends \Core\Controller
{
    /**
     * Show the login page
     * 
     * @return void
    */  
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * Log in a user
     * 
     * @return void 
    */ 
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        if ($user) {

            Auth::login($user);

            Flash::addMessage('Erfolgreich eingeloggt.');

            $this->redirect(Auth::getReturnToPage());

        } else {

            Flash::addMessage('Fehler mein Einloggen, bitte erneut versuchen.', Flash::WARNING);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email']
            ]);
        }
    }

    /**
     * Log out a user
     * 
     * @return void
    */ 
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a 'log out' flash message and redirect to the homepage. Necessary to use because sesspion gets destroyed when loggin out  
     * 
     * @return void
    */  
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Erfolgreich ausgeloggt.');

        $this->redirect('/');
    }
}