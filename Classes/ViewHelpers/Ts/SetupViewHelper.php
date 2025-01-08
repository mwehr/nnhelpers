<?php

namespace  Nng\Nnhelpers\ViewHelpers\Ts;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Wert aus dem TypoScript-Setup holen.
 * 
 * Einfacher und direkter Zugriff aus dem Fluid-Template heraus - unabhängig von der Extension, die das Template rendert.
 * ```
 * {nnt3:ts.setup(path:'pfad.zum.typoscript.setup')}
 * {nnt3:ts.setup(path:'pfad.zum.typoscript', key:'setup')}
 * {nnt3:ts.setup(path:'pfad.zum.typoscript.{dynamicKey}.whatever')}
 * ```
 * @return mixed
 */
class SetupViewHelper extends AbstractViewHelper {

	use CompileWithRenderStatic;

	protected $escapeOutput = false;

	public function initializeArguments() {
	   $this->registerArgument('key', 'string', 'Key, der geholt werden soll', false, '');
	   $this->registerArgument('path', 'string', 'Pfad zum Typoscript', false, '');
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
		$key = $arguments['key'];
		$ts = \nn\t3::Settings()->getFromPath( $arguments['path'] );
		return $key ? ($ts[$key] ?? '') : $ts;
	}

}