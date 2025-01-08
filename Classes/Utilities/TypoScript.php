<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Backend\Configuration\TsConfigParser;

/**
 * Methoden zum Parsen und Konvertieren von TypoScript
 */
class TypoScript implements SingletonInterface {

	/**
	 * 	TypoScript 'name.'-Syntax in normales Array umwandeln.
	 * 	Erleichtert den Zugriff
	 *	```
	 *	\nn\t3::TypoScript()->convertToPlainArray(['example'=>'test', 'example.'=>'here']);
	 *	```
	 * 	@return array
	 */
    public function convertToPlainArray ($ts) {
		if (!$ts || !is_array($ts)) return [];
		$typoscriptService = \nn\t3::injectClass( TypoScriptService::class );
		return $typoscriptService->convertTypoScriptArrayToPlainArray($ts);
	}
	
	/**
	 * 	Wandelt einen Text in ein TypoScript-Array um.
	 *	```
	 *	\nn\t3::TypoScript()->fromString( 'lib.test { beispiel = 10 }' );	=> ['lib'=>['test'=>['beispiel'=>10]]]
	 *	\nn\t3::TypoScript()->fromString( 'lib.test { beispiel = 10 }', $mergeSetup );
	 *	```
	 * 	@return array
	 */
    public function fromString ( $str = '', $overrideSetup = [] ) {
		if (!trim($str)) return $overrideSetup;
		if (!$overrideSetup) {
			$parser = \nn\t3::injectClass( TypoScriptParser::class );
			$parser->parse($str);
		} else {
			$parser = \nn\t3::injectClass( TsConfigParser::class );
			$parser->setup = $overrideSetup;
			$parser->parse($str);
		}
		return $this->convertToPlainArray($parser->setup);
	}

	/**
	 * Page-Config hinzufügen
	 * Alias zu `\nn\t3::Registry()->addPageConfig( $str );`
	 * 
	 * ```
	 * \nn\t3::TypoScript()->addPageConfig( 'test.was = 10' );
	 * \nn\t3::TypoScript()->addPageConfig( '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:extname/Configuration/TypoScript/page.txt">' );
	 * \nn\t3::TypoScript()->addPageConfig( '@import "EXT:extname/Configuration/TypoScript/page.ts"' );
	 * ```
	 * @return void
	 */
	public function addPageConfig( $str = '' ) {
		\nn\t3::Registry()->addPageConfig( $str );
	}
}