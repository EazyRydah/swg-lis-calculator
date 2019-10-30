<?php

namespace App\Models;

use PDO;

/**
 * Calculator model
 *
 * PHP version 7.0
 */
class Calculator extends \Core\Model
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
     * Calculate new Calculator with the current propertxy values 
     * 
     * @return void
    * */ 
    public function calculate()
    {
        $this->validate();

        if (empty($this->errors)) {

            // Gebäude & Ladeinfrastruktur
            $this->anschlussLeistungLis = $this->hausanschluss - $this->gebäudelast;

            $this->anzahlStellplätzeStatisch = floor($this->wirkleistungsfaktor * $this->ladeleistungLis);

            // Fahrzeug & Fahrzeugverhalten
            $this->tagesfahrleistung = round($this->jahresfahrleistung / $this->anzahlTage, 1);

            $this->nachladeBedarfTag = round($this->tagesfahrleistung * $this->energieverbrauchFahrzeug / 100, 1);

            // Ladezeit
            $this->nachladeBedarfZeit = round(( $this->nachladeBedarfTag / $this->energieverbrauchFahrzeug ) + $this->zusatzZeitLadeverlust, 1);

            $this->anzahlNachladungen = round($this->ladezeitraum / ( $this->nachladeBedarfZeit + $this->fahrzeugwechselZeit ),1);

            // Dynamisches Lastmanagement
            $this->anzahlStellplätzeDynamisch = floor($this->anzahlNachladungen * $this->anzahlStellplätzeStatisch * 1 / $this->nutzfaktor);

            $this->isExportable = true;

            return true;

        }

        return false;
    }

    /**
     * Validate the Calculator input, adding validation errors to errors array
     * 
     * @return void
    */  
    protected function validate()
    {
        if ($this->ort == '' ) {
            $this->errors[] = 'Ort der Beratung angeben';
        }

        if ($this->objektBezeichnung == '' ) {
            $this->errors[] = 'Objekt-Bezeichnung angeben';
        }

        if ($this->objektStandort == '' ) {
            $this->errors[] = 'Objekt-Standort angeben';
        }

        if ($this->hausanschluss == '' ) {
            $this->errors[] = 'Hausanschluss angeben';
        }

        if ($this->gebäudelast == '' ) {
            $this->errors[] = 'Gebäudelast angeben';
        }

        if ($this->wirkleistungsfaktor == '' ) {
            $this->errors[] = 'Wirkleistungsfaktor angeben';
        }

        if ($this->ladeleistungLis == '' ) {
            $this->errors[] = 'Ladeleistung der Ladeinfrastruktur angeben';
        }

        if ($this->jahresfahrleistung == '' ) {
            $this->errors[] = 'Jahresfahrleistung angeben';
        }

        if ($this->anzahlTage == '' ) {
            $this->errors[] = 'Anzahl Tage angeben';
        }

        if ($this->energieverbrauchFahrzeug == '' ) {
            $this->errors[] = 'Energieverbrauch des Fahrzeugs angeben';
        }

        if ($this->ladeleistungFahrzeug == '' ) {
            $this->errors[] = 'Ladeleistung des Fahrzeugs angeben';
        }

        if ($this->zusatzZeitLadeverlust == '' ) {
            $this->errors[] = 'Zusatzzeit für Ladeverluste angeben';
        }

        if ($this->ladezeitraum == '' ) {
            $this->errors[] = 'Ladezeitraum angeben';
        }

        if ($this->fahrzeugwechselZeit == '' ) {
            $this->errors[] = 'Zeit zum Fahrzeugwechsel';
        }

        if ($this->nutzfaktor == '' ) {
            $this->errors[] = 'Nutzfaktor angeben';
        }

    }
}

