<?php
defined('TYPO3') or die();

/**
 * 
 *	Fügt das Feld "exif" etc. zum sys_file hinzu
 *
 */
$newSysFileColumns = [
    'exif' => [
        'exclude' => 1,
        'label' => 'EXIF-Daten',
        'config' => [
			'type' => 'text',
			'rows' => 5,
		],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file', $newSysFileColumns);
