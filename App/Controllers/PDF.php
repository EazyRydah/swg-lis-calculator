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
class Pdf extends \Core\Controller
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
        View::renderTemplate('Pdf/new.html');
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

            $this->redirect('/admin/show-pdf');

        } else {

            View::renderTemplate('Pdf/index.html', [
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

            $this->redirect('/admin/show-pdf');

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

            Flash::addMessage('Änderung gespeichert');

            $this->redirect('/admin/show-pdf');

        } else {

            Flash::addMessage('Kein Anhang vorhanden', Flash::INFO);

            $this->redirect('/admin/show-pdf');

        }
    }    

}