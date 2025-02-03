<?php
require 'vendor/autoload.php'; // If using Composer
// require __DIR__ . '/dompdf/autoload.inc.php'; // If installed manually

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml('<h1>DOMPDF is Working!</h1>');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("test.pdf", ["Attachment" => false]); // Opens in browser
?>
