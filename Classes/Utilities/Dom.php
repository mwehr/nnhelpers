<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Manipulieren von DOM und XML.
 * Noch in Arbeit :)
 * 
 */
class Dom implements SingletonInterface {
   
	/**
	 * 	@var \DOMDocument
	 */
	protected $dom;

	/**
	 * 	@var CssSelectorConverter
	 */
	protected $converter;

	/**
	 * 	Ersetzt Links und Pfade zu Bildern etc. im Quelltext mit absoluter URL
	 * 	z.B. für den Versand von Mails
	 * 	@return string
	 */
	public function absPrefix( $html, $attributes = [], $baseUrl = '' ) {

		if (!$baseUrl) $baseUrl = \nn\t3::Environment()->getBaseUrl();
		if (!$attributes) $attributes = ['href', 'src'];

		$dom = new \DOMDocument();
		@$dom->loadHTML($html);
		$xpath = new \DOMXPath($dom);
		
		foreach ($attributes as $attr) {
			$nodes = $xpath->query('//*[@'.$attr.']');
			foreach ($nodes as $node) {
				if ($val = ltrim($node->getAttribute($attr), '/')) {
					if (strpos($val, ':') === false && $val != '#') {
						$node->setAttribute($attr, $baseUrl . $val);
					}
				}
			}
		}

		return $dom->saveHTML();
	}
}