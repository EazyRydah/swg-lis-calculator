<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Calculator controller
 * 
 * PHP version 7.0
*/  
class Calculator extends Authenticated
{
  
    /**
     * Calculator index
     * 
     * @return void
    */ 
    public function indexAction()
    {
        View::renderTemplate('Calculator/index.html');
    }
}