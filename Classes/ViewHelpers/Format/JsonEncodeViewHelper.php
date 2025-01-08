<?php

namespace Nng\Nnhelpers\ViewHelpers\Format;

use Nng\Nnhelpers\Helpers\JsonHelper;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Wandelt ein Array oder Object ins JSON-Format um.
 * ```
 * {some.object->nnt3:format.jsonEncode()}
 * ```
 * @return string
 */
class JsonEncodeViewHelper extends AbstractViewHelper {

	use CompileWithRenderStatic;

	/**
	 * @var boolean
	 */
	protected $escapeChildren = false;

	/**
	 * @var boolean
	 */
	protected $escapeOutput = false;
	
	
	/**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('str', 'mixed', 'Raw String, Array oder JS-Object, das in ein JSON umgewandelt werden soll', false);
	}
	
	/**
	 * @return string
	 */
	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
		$args = ['str'];
		foreach ($args as $arg) ${$arg} = $arguments[$arg] ?: '';

		if (!$str) $str = $renderChildrenClosure();
		
		// Array wurde übergeben: Direkt in JSON kodieren
		if (is_array($str)) {
			return json_encode($str);
		}

		// JavaScript Object (z.B. aus TypoScript-Setup) in JSON konvertieren
		$json = new JsonHelper();
		$data = $json->decode( trim($str) );
		if ($data === null) return $str;

		return json_encode($data);
	}


}