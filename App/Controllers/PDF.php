<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;
use \App\Models\PDFDocument;
use \App\Flash;

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
     * render upload PDF page
     * 
     * @return void
     *   
    */  
    public function newAction()
    {
        View::renderTemplate('PDF/new.html', [
            'attachmentExists' => PDFDocument::attachmentExists()
        ]);
    }

    /**
     * Create new PDF
     * 
     * @return void
     *   
    */  
    public function createAction()
    {
        
        $pdf = new PDFDocument($_FILES);

        if ($pdf->upload()) {

            Flash::addMessage('Änderung gespeichert');

            $this->redirect('/admin');

        } else {

            View::renderTemplate('PDF/new.html', [
                'pdf' => $pdf
            ]);

        }
  
    }

    /**
     * Download existing PDF attachment
     * 
     * @return void
     *   
    */  
    public function downloadAction()
    {
        if (PDFDocument::attachmentExists()) {

            $this->redirect('/uploads/statistik-anhang.pdf');

        } else {

            Flash::addMessage('Kein Anhang vorhanden', Flash::INFO);

            View::renderTemplate('PDF/new.html');

        }
    }

    /**
     * Delete existing PDF attachment
     * 
     * @return void
     *   
    */  
    public function deleteAction()
    {
        if (PDFDocument::attachmentExists()) {

            PDFDocument::deleteAttachment();

            $this->redirect('/pdf/new');

        } else {

            Flash::addMessage('Kein Anhang vorhanden', Flash::INFO);

            View::renderTemplate('PDF/new.html');

        }
    }    

    public function test()
    {
        PDFDocument::test();
    }
}