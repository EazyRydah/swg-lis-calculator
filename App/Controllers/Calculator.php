<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Calculation;

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

    /**
     * Calculate the result
     * 
     * @return void
     *  */ 
    public function calculateAction()
    {
        $calculation = new Calculation($_POST);

        if ($calculation->calculate()) {

            View::renderTemplate('Calculator/index.html', [
                'calculation' => $calculation
            ]);

        } else {

            View::renderTemplate('Calculator/index.html', [
                'calculation' => $calculation
            ]);
            
        }
    }
}