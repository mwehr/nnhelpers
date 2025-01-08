<?php

defined('TYPO3') or die();

call_user_func(
	function( $extKey )
	{

		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey);
		
		require_once($extPath . 'Classes/nnhelpers.php');

		require_once($extPath . 'Classes/aliases.php');
		
		// Diese Felder in der RootlineUtility::get() auch holen
		\nn\t3::Registry()->rootLineFields(['backend_layout']);

		// Globalen Namespace {nh:...} registrieren für ViewHelper
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['nnt3'] = ['Nng\\Nnhelpers\\ViewHelpers'];

		// -----------------------------------------------------------------------------------
		// Caching framework

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey] = [
			'frontend'  => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
			'backend'   => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
			'options'   => ['defaultLifeTime'=>3600*24],
			'groups'    => ['pages'],
		];

		// Hook, der beim Löschen des Cache im Backend aufgerufen wird
		\nn\t3::Registry()->clearCacheHook( \Nng\Nnhelpers\Hooks\ClearCacheHook::class . '->postProcessClearCache' );

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1595967235] = [
			'nodeName' => 'nnt3_flex',
			'priority' => '70',
			'class' => \Nng\Nnhelpers\Hooks\FlexFormElement::class,
		];

		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][] 
			= \Nng\Nnhelpers\Hooks\FlexFormHook::class;

	},
'nnhelpers' );

