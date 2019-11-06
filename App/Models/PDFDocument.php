<?php

namespace App\Models;

use \App\Auth;
use TCPDF;
use setasign\Fpdi\Tcpdf\fpdi;

/**
 * PDFDocument model
 *
 * PHP version 7.0
 */
class PDFDocument extends \Core\Model
{
     /**
     * Error messages
     * 
     * @var array
     *  */ 
    public $errors = [];

    /**
     * Class constructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     *  */  
    public function __construct($data = [])
    {
        foreach($data as $key => $value) {
            $this->$key = $value;
        };

    }

    /**
     * Upload validated PDF file into public uploads folder
     * 
     * @return boolean True on success, false otherwise
     * 
    */ 
    public function upload()
    {
        $this->validate();

        if (empty($this->errors)) {

            // Upload file
            $pathinfo = pathinfo($this->file['name']);

            $filename = 'statistik-anhang' . '.' .  $pathinfo['extension'];

            $destination = "./uploads/$filename";

            if (move_uploaded_file($this->file['tmp_name'], $destination)) {
                
                return true;

            } else {

                throw new \Exception('Unable to move uploaded file');

                return false;

            }

        }

        return false;

    }

    /**
     * Validate $_FILES array for PDF upload and set errors if any
     * 
     * @return void
     * 
    */ 
    protected function validate() 
    {

        if ( isset($this->file) ) {

            switch ($this->file['error']) {

                case UPLOAD_ERR_OK:
                    break;

                case UPLOAD_ERR_NO_FILE:
                    $this->errors[] = "No file uploaded.";
                    break;

                case UPLOAD_ERR_INI_SIZE:
                    $this->errors[] = "File is too large.";
                    break;

                default:
                    $this->errors[] = 'An error occured.';
                    break;
            }

            // size validation
            if ($this->file['size'] > 5000000) {

                $this->errors[] = "File is too large.";
                
            }

            // filetype validation 
            if (empty($this->errors)) {

                $mime_types = ['application/pdf'];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
        
                $mime_type = finfo_file($finfo, $this->file['tmp_name']);  

                if ( ! in_array($mime_type, $mime_types)) {
    
                    $this->errors[] = "Invalid file type.";
                }   
            }
            
        } else {

            $this->errors[] = "Invalid Upload.";

        }
    }

    /**
     * Check if PDF attachments exists in uploads folder
     * 
     * @return boolean True if attachment exists, false otherwise
     * 
     *  */ 
    public static function attachmentExists() 
    {
        $destination =  "./uploads/statistik-anhang.pdf";

        if (file_exists($destination)) {
            return true;
        }

        return false;
    }

    /**
     * Delete PDF attachment in uploads folder
     * 
     * @return void 
    */ 
    public static function deleteAttachment()
    {
        unlink("./uploads/statistik-anhang.pdf");
    }

    /**
     * Create HTML from given calculation object
     * 
     * @param object $calculation The mixed calculation object
     * 
     * @return string $html The Html code
     * 
     *  */ 
    public static function getHtmlFromCalculation($calculation)
    {
        $headerLogo1 = '<img src="/img/swg_climate_change.png">';
        $headerLogo2 = '<img src="/img/swg_logo.png">';
    
        $date = date("d.m.Y");
         
        $html = '
        <table cellpadding="2" cellspacing="0" style="width: 100%; ">
    
            <tr>
            <td>'. nl2br(trim($headerLogo1)) .'</td>
            <td style="text-align:right">'. nl2br(trim($headerLogo2)) .'</td>
            </tr>
    
        </table>
     
        <br><br><br>
        <h2>Beratungsprotokoll: Ladeinfrastrukturausbau</h2>
        <br><br><br>
        
    
        <table cellpadding="2" cellspacing="0" style="width: 100%; margin-top:100px;">
            <br>
            <tr>
                <td><span style="font-weight: bold">Datum: </span> <span>'. $date .'</span></td>
                <td><span style="font-weight: bold">Ort: </span> <span>'. $calculation->ort .'</span></td>
            </tr>
            <br>
            <tr>
                <td><span style="font-weight: bold">Objekt-Bezeichnung: </span> <span>'. $calculation->objektBezeichnung . '</span></td>
                <td><span style="font-weight: bold">Objekt-Standort: </span> <span>'. $calculation->objektStandort .'</span></td>
            </tr>
    
        </table>
        <br>
    
        <hr>
    
        <h4>Gebäude & Ladeinfrastruktur</h4>
        <p>Bei einem verfügbaren Haussanschluss mit '. $calculation->hausanschluss .' kW und einer Gebäudelast von '. $calculation->gebäudelast .' kW verbleiben '. $calculation->anschlussLeistungLis .' kW Anschlussleistung für Ladeinfrastruktur.
            <br>Unter Annahme eines Wirkleistungsfaktors von '. $calculation->wirkleistungsfaktor .', können bei einer verfügbaren AC-Ladeleistung von '. $calculation->ladeleistungLis .' kW,  '. $calculation->anzahlStellplätzeStatisch .' Stellplätze versorgt werden.</p>
        
        <h4>Fahrzeug & Fahrverhalten</h4>
        <p>Bei einer jährlichen Fahrleistung von '. $calculation->jahresfahrleistung .' km, verteilt auf '. $calculation->anzahlTage .' Tage (z.B. Werktage), ergibt sich eine tägliche Fahrleistung von '. $calculation->tagesfahrleistung .' km. 
        <br>Unter Annahme eines Energieverbrauchs von '. $calculation->energieverbrauchFahrzeug .' kWh/100km ergibt sich daraus ein täglicher Nachladebedarf von '. $calculation->nachladeBedarfTag .' kWh.</p>
    
        <h4>Ladezeit</h4>
        <p>Unter Anbetracht einer fahrzeugseitig maximalen Ladeleistung von '. $calculation->ladeleistungFahrzeug .' kW und einer Zusatzzeit für Ladeverstlust von '. $calculation->zusatzZeitLadeverlust .' h, erfolgt die Nachladung des täglichen Bedarfs eines E-PKW in '. $calculation->nachladeBedarfZeit .' h.
        <br>Innerhalb eines verfügbaren Zeitraumes von '. $calculation->ladezeitraum .' h, sind bei einer Zeit zum Fahrzeugwechsel '. $calculation->fahrzeugwechselZeit .' h demnach '. $calculation->anzahlNachladungen .' Nachladungen möglich.</p>
    
        <h4>Dynamisches Lastmanagement</h4>
        <p>Unter Annahme eines Nutzfaktors von '. $calculation->nutzfaktor .' (z.B. privat 0,7, gewerblich 0,9) können mit der verfügbaren LIS-Anschlussleistung unter Einsatz eines dynamischen Lastmanagements
        '. $calculation->anzahlStellplätzeDynamisch .' Stellplätze parallel versorgt werden.</p>
        <br>
        <p><span style="font-weight: bold">Hinweis: </span> <em>Alle Angaben beruhen auf erfahrungsbasierten Annahmen und dienen lediglich zur Orientierung bei der Auslegung von Ladeinfrastruktur.</em></p>
       
        <p style="font-weight: bold">Mit freundlichen Grüßen</p>
        <br><br>i.A. '.Auth::getUser()->name . '<br><br>Ihr E-Mobilitätsteam der Stadtwerke Göttingen';

        return $html;

    }

    /**
     * Export PDF Document to browser
     * 
     * @param $pdfName The Name of the exported pdf document
     * @param $html The Html that gets written to the pdf file
     * 
     * @return void
     *  */ 
    public static function exportToBrowser($pdfName, $html)
    {
       
        $pdf = new FPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Stadtwerke Göttingen AG');
        $pdf->SetTitle('Beratungsprotokoll: Ladeinfrastruktur');
        $pdf->SetSubject('Beratungsprotokoll: Ladeinfrastruktur');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Header und Footer Informationen
        $pdf->setHeaderFont(false);
        $pdf->setFooterFont(false);

        // Auswahl des Font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Auswahl der Margins
        $pdf->SetMargins(20, 25, 19);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Automatisches Autobreak der Seiten
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Image Scale 
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Schriftart
        $pdf->SetFont('dejavusans', '', 10);

        // Neue Seite 
        $pdf->AddPage();  
        // Write HTML to new page
        $pdf->writeHTML($html, true, false, true, false, '');

        // Add Attachment if exists
        
        if (self::attachmentExists()) {

            $pageCount = $pdf->setSourceFile('./uploads/statistik-anhang.pdf');
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $pageId = $pdf->importPage($i, '/MediaBox');
                $pdf->AddPage();
                $pdf->useTemplate($pageId);
            }
            
        }
           
        //Variante 1: PDF direkt an den Browser senden:
        $pdf->Output($pdfName, 'I');
             
    }
}

