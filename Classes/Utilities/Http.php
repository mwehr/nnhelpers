<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Einfache Weiterleitungen machen, URLs bauen
 */
class Http implements SingletonInterface {
   
	/**
	 * Zu einer Seite weiterleiten
	 * ```
	 * \nn\t3::Http()->redirect( 'https://www.99grad.de' );
	 * \nn\t3::Http()->redirect( 10 );										// => path/to/pageId10
	 * \nn\t3::Http()->redirect( 10, ['test'=>'123'] );						// => path/to/pageId10&test=123
	 * \nn\t3::Http()->redirect( 10, ['test'=>'123'], 'tx_myext_plugin' );	// => path/to/pageId10&tx_myext_plugin[test]=123
	 * ```
	 * @return void
	 */
    public function redirect ( $pidOrUrl = null, $vars = [], $varsPrefix = '' ) {

		if (!$varsPrefix) unset($vars['id']);

		if ($varsPrefix) {
			$tmp = [$varsPrefix=>[]];
			foreach ($vars as $k=>$v) $tmp[$varsPrefix][$k] = $v;
			$vars = $tmp;
		}
		
		$link = $this->buildUri( $pidOrUrl, $vars, true );
		header('Location: '.$link); 
		exit();
	}

	/**
	 * URI bauen, funktioniert im Frontend und Backend-Context.
	 * Berücksichtigt realURL
	 * ```
	 * \nn\t3::Http()->buildUri( 123 );
	 * \nn\t3::Http()->buildUri( 123, ['test'=>1], true );
	 * ```
	 * @return string
	 */
	public function buildUri ( $pageUid, $vars = [], $absolute = false ) {

		// Keine pid übergeben? Dann selbst ermitteln zu aktueller Seite
		if (!$pageUid) $pageUid = \nn\t3::Page()->getPid();

		// String statt pid übergeben? Dann Request per PHP bauen
		if (!is_numeric($pageUid)) {
			
			// relativer Pfad übergeben z.B. `/link/zu/seite`
			if (strpos($pageUid, 'http') === false) {
				$pageUid = \nn\t3::Environment()->getBaseURL() . ltrim( $pageUid, '/' );
			}

			$parsedUrl = parse_url($pageUid);

			parse_str($parsedUrl['query'] ?? '', $parsedParams);
			if (!$parsedParams) $parsedParams = [];

			ArrayUtility::mergeRecursiveWithOverrule( $parsedParams, $vars );
			$reqStr = $parsedParams ? http_build_query( $parsedParams ) : false;

			$path = $parsedUrl['path'] ?: '/';
			
			$port = $parsedUrl['port'] ?? false;
			if ($port) $port = ":{$port}";

			return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$port}{$path}" . ($reqStr ? '?'.$reqStr : '');
		}


		// Frontend initialisieren, falls nicht vorhanden
		if (!\nn\t3::Environment()->isFrontend()) {
			\nn\t3::Tsfe()->get();
		}

		$request = $GLOBALS['TYPO3_REQUEST'] ?? new \TYPO3\CMS\Core\Http\ServerRequest();
		$cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
		$cObj->setRequest( $request );
		
		$uri = $cObj->typolink_URL([
			'parameter' => $pageUid,
			'linkAccessRestrictedPages' => 1,
			'additionalParams' => GeneralUtility::implodeArrayForUrl(NULL, $vars),
		]);

		// setAbsoluteUri( TRUE ) geht nicht immer...
		if ($absolute) {
			$uri = GeneralUtility::locationHeaderUrl($uri);
		}

		return $uri;
	}

}