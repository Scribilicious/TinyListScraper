<?php
include '../src/TinyListScraper.php';

$oScraper = new TinyListScraper\TinyListScraper();

/**
 * Get the complete list of all the results sorted by date
 */
$aResults = $oScraper->get('qr code');

echo '<pre>';
print_r($aResults);
echo '</pre>';

/**
 * Get only the verge results
 */
$aResults = $oScraper->get('qr code', array('verge'));

echo '<pre>';
print_r($aResults);
echo '</pre>';
