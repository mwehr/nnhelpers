<?php

namespace  Nng\Nnhelpers\ViewHelpers\Ts;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Wert aus der PageTSconfig holen.
 * 
 * Einfacher und direkter Zugriff aus dem Fluid-Template heraus - unabhängig von der Extension, die das Template rendert.
 * ```
 * {nnt3:ts.page(path:'pfad.zum.page.config')}
 * {nnt3:ts.page(path:'pfad.zum.page', key:'config')}
 * {nnt3:ts.page(path:'pfad.zum.page.{dynamicKey}.whatever')}
 * ```
 * @return mixed
 */
class PageViewHelper extends AbstractViewHelper {

	use CompileWithRenderStatic;

	protected $escapeOutput = false;

	public function initializeArguments() {
	   $this->registerArgument('key', 'string', 'Key, der geholt werden soll', false, '');
	   $this->registerArgument('path', 'string', 'Pfad zum Typoscript', false, '');
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
		$key = $arguments['key'];
		$ts = \nn\t3::Settings()->getPageConfig( $arguments['path'] );
		return $key ? ($ts[$key] ?? '') : $ts;
	}

}