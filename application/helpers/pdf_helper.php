<?php

function prep_pdf($orientation = 'portrait', $footer_content = array()) {
    $CI = & get_instance();

    $CI->cezpdf->selectFont(base_url() . '/fonts');

    $all = $CI->cezpdf->openObject();
    $CI->cezpdf->saveState();
    $CI->cezpdf->setStrokeColor(0, 0, 0, 1);
    if ($orientation == 'portrait') {
        $CI->cezpdf->ezSetMargins(50, 80, 50, 50);
        $CI->cezpdf->ezStartPageNumbers(560, 50, 8, '', 'Page {PAGENUM}', 1);
        $CI->cezpdf->line(20, 60, 578, 60);
        $CI->cezpdf->addText(180, 65, 9, 'Dijana melalui Sistem MyBooking pada ' . date('d/m/Y h:i A'));
        $CI->cezpdf->addText(30, 50, 9, $footer_content['agency_name']);
        $CI->cezpdf->addText(30, 40, 9, $footer_content['agency_address']);
        $CI->cezpdf->addText(30, 30, 9, $footer_content['contact']);
    } else {
        $CI->cezpdf->ezStartPageNumbers(750, 28, 8, '', '{PAGENUM}', 1);
        $CI->cezpdf->line(20, 40, 800, 40);
        $CI->cezpdf->addText(50, 32, 8, 'Generated on ' . date('m/d/Y h:i:s a'));
        $CI->cezpdf->addText(50, 22, 8, $footer_content);
    }
    $CI->cezpdf->restoreState();
    $CI->cezpdf->closeObject();
    $CI->cezpdf->addObject($all, 'all');
}

?>