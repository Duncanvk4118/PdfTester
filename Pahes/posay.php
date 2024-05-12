<?php
chdir(__DIR__);

include '../Classes/pdf.php';
include '../Classes/database.php';

$data = new Database();
$sql = "SELECT * FROM `Users`";
$result = $data->requestSQL($sql);

if ($result !== false && !empty($result)) {
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
    $pdf = new TCPDF();

    foreach ($result as $row) {
        $naam = $row['naam'];
        $totaal = $row['totaal'];
        $correct = $row['correct'];
        $term = $row['term'];
        $toetsid = 1;

        $pdfMaker = new pdfMaker($naam, 1, $totaal, $correct, "template", $term);
        $html = $pdfMaker->makeHTML("template");

        $pdf->AddPage();
        $pdf->writeHTML($html);
    }
    $pdf->Output('result-test' . $toetsid . '.pdf', 'I');
}
?>