<?php

/**
 *  Route zum Verarbeiten von Backend-Module-Links,
 *  z.B. zum Verstecken / Löschen von Datensätzen über ein
 *  eigenes Backend-Modul.
 * 
 *  siehe Backend-ViewHelper, z.B. 
 *  {nnt3:link.hideRecord()}
 * 
 */
return [
    'nnt3_record_processing' => [
        'path' => '/record/process',
        'target' => TYPO3\CMS\Backend\Controller\SimpleDataHandlerController::class . '::processAjaxRequest'
    ],
];