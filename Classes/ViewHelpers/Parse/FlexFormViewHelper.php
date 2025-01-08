<?php

namespace Nng\Nnhelpers\ViewHelpers\Parse;

use Nng\Nnhelpers\Helpers\JsonHelper;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Parsed ein FlexForm (XML) und macht daraus ein Array.
 * 
 * Praktisch, falls man einen rohen Datensatz der Tabelle `tt_content` vor sich hat und an einen Wert aus dem FlexForm in `pi_flexform` braucht.
 * ```
 * {row.pi_flexform->nnt3:parse.flexForm()->f:debug()}
 * ```
 * @return array
 */
class FlexFormViewHelper extends AbstractViewHelper {

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
		$this->registerArgument('xml', 'mixed', 'Raw XML, das in ein Object umgewandelt werden soll', false);
	}
	
	/**
	 * @return string
	 */
	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
		$args = ['xml'];
		foreach ($args as $arg) ${$arg} = $arguments[$arg] ?: '';

		if (!$xml) $xml = $renderChildrenClosure();
		return \nn\t3::Flexform()->parse( $xml );
	}


}