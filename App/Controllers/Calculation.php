<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Calculator;
use \App\Models\PDFDocument;

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
        if (isset($_SESSION['current_calculation'])) {

            View::renderTemplate('Calculation/index.html', [
                'calculation' => $_SESSION['current_calculation']
            ]);

        } else {

            View::renderTemplate('Calculation/index.html');

        }
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

            $_SESSION['current_calculation'] = $calculation;
            
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

        $html = PDFDocument::getHtmlFromCalculation($calculation);        

        PDFDocument::exportToBrowser('beratungsprotokoll-lis.pdf', $html); 
                
    }
}