<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Calculator;
use \App\PDF;

/**
 * Calculation controller
 * 
 * PHP version 7.0
*/  
class Calculation extends Authenticated
{

    /**
     * Calculation index
     * 
     * @return void
    */ 
    public function indexAction()
    {
        View::renderTemplate('Calculation/index.html');
    }

    /**
     * Calculate the result
     * 
     * @return void
    */ 
    public function calculateAction()
    {
        $calculation = new Calculator($_POST);

        if ($calculation->calculate()) {

            View::renderTemplate('Calculation/index.html', [
                'calculation' => $calculation
            ]);

        } else {

            View::renderTemplate('Calculation/index.html', [
                'calculation' => $calculation
            ]);

        }
    }

    /**
     * Exports the current calculation as PDF document
     * 
     * @return void
    */  
    public function exportAction()
    {

        $calculation = new Calculator($_POST);

        $html = PDF::getHTML($calculation);

        PDF::createFromHTML($html, 'beratungsprotokoll-ladeinfrastruktur.pdf');
                
    }
}