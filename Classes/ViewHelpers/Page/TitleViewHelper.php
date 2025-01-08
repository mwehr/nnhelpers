<?php
namespace Nng\Nnhelpers\ViewHelpers\Page;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Aktuelle Page-Title setzen.
 * 
 * Andert das `<title>`-Tag der aktuellen Seite.
 * 
 * Funktioniert nicht, wenn `EXT:advancedtitle` aktiviert ist!
 * 
 * ```
 * {nnt3:page.title(title:'Seitentitel')}
 * {entry.title->nnt3:page.title()}
 * ```
 */
class TitleViewHelper extends AbstractViewHelper {

	use CompileWithRenderStatic;

	protected $escapeOutput = false;

	public function initializeArguments() {
	   $this->registerArgument('title', 'string', 'Page-Title', false);
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
		\nn\t3::Page()->setTitle( $arguments['title'] ?: $renderChildrenClosure() );
	}
    
}
