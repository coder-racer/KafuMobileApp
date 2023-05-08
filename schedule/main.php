
<?php
$pdf = '1.pdf';
include_once 'alt_autoload.php';
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile('1.pdf');


$text = $pdf->getObjects();

echo '<pre>';
var_dump( $text);
echo '</pre>';