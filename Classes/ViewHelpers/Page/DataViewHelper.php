<?php
namespace Nng\Nnhelpers\ViewHelpers\Page;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Vereinfacht den Zugriff auf Daten aus der Tabelle `pages`.
 * ```
 * {nnt3:page.data()}
 * {nnt3:page.data(key:'nnp_contact', slide:1)}
 * {nnt3:page.data(key:'backend_layout_next_level', slide:1, override:'backend_layout')}
 * ```
 * Wichtig, damit `slide` funktioniert: Falls die Tabelle `pages` um ein eigenes Feld erweitert wurde, muss das Feld vorher in der `ext_localconf.php` registriert werden.
 * ```
 * \nn\t3::Registry()->rootLineFields(['logo']);
 * ``` 
 */
class DataViewHelper extends AbstractViewHelper {

	use CompileWithRenderStatic;

	protected $escapeOutput = false;

	public function initializeArguments() {
	   $this->registerArgument('slide', 'boolean', 'Daten von rootline sliden', false, false);
	   $this->registerArgument('key', 'string', 'Feldname', false);
	   $this->registerArgument('override', 'string', 'Feldname Override', false, false);
	}

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
		if (!$arguments['key']) {
			return \nn\t3::Page()->getData();
		}
		return \nn\t3::Page()->getField( $arguments['key'], $arguments['slide'], $arguments['override'] );
	}
    
}
