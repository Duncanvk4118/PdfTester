<?php

require __DIR__ . "/../vendor/autoload.php";

use Dompdf\Dompdf;

class pdfMaker {
    // Variabelen
    public $naam = "";
    public $toetsid = null;

    private $term;
    public $totaal = 0;
    public $correct = 0;
    public $score = null;
    public $grade = null;
    public $color = null;
    public $template;

    function __construct($naam, $toetsid, $totaal, $correct, $template = "template" , $term = null) {
        $this->naam = $naam;
        $this->toetsid = $toetsid;
        $this->totaal = $totaal;
        $this->correct = $correct;
        $this->template = $template;
        $this->term = $term;

        $this->calculate();
    }

    private function calculate() {
        if ($this->totaal != 0) {
            $this->score = ($this->correct / $this->totaal) * 100;
            $this->grade = (($this->correct / $this->totaal) * 9) + $this->term;

            if ($this->grade < 5.5 && $this->grade >= 0) {
                $this->color = "red";
            } 
            elseif($this->grade < 0) {
                $this->grade = 0;
                $this->color = "red";
            } 
            elseif ($this->grade >= 5.5) {
                $this->color = "green";
            } 
            else {
                $this->color = "green";
                $this->grade = 10;
            }

            if ($this->score > 100) {
                $this->score = 100;
            } 
            elseif($this->score < 0) {
                $this->score = 0;
            }
        } else {
            $this->score = 0;
            $this->grade = 0;
            $this->color = "red";
        }
    }

    function makeHTML($template) {
        // Zet de hoofdmap
        chdir(__DIR__);

        // Checkt of de template een string is
        if (!is_string($template))
        {
            return "Geen geldige Template!";
        }

        // Controleerd of er een werkelijke file is binnengekomen
        if ($template === false)
        {
            return "Er is een fout bij het inladen voorgekomen.";
        }

        // Zoekt de file op
        $html = file_get_contents("../Files/" . $template . ".html");


        $html = str_replace("{{ naam }}", htmlspecialchars($this->naam ?? ''), $html);
        $html = str_replace("{{ id }}", htmlspecialchars($this->toetsid ?? ''), $html);
        $html = str_replace("{{ term }}", htmlspecialchars($this->term ?? ''), $html);
        $html = str_replace("{{ totaal }}", htmlspecialchars($this->totaal ?? ''), $html);
        $html = str_replace("{{ correct }}", htmlspecialchars($this->correct ?? ''), $html);
        $html = str_replace("{{ score }}", htmlspecialchars($this->score ?? ''), $html);
        $html = str_replace("{{ grade }}", htmlspecialchars(round($this->grade ?? 0, 1)), $html);
        $html = str_replace("{{ color }}", htmlspecialchars($this->color ?? ''), $html);
        
        return $html;
    }

    function createPDF() {
        $html = $this->makeHTML($this->template);

        $pdf = new Dompdf;
        $pdf->loadHTML($html);
        $pdf->render();

        $pdf->addInfo("Title", "Inscape");
        $pdf->addInfo("Subject", "Test Result");

        $pdf->stream("Result.pdf", ["Attachment" => 0]);
    }
}

?>
