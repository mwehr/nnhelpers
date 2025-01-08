<?php
namespace Nng\Nnhelpers\ViewHelpers\FrontendUser;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Frontend-User holen
 * 
 * Gibt ein Array mit den Daten des Frontend-Users zurück, z.B. um Seiten, Mails oder Inhalte zu personalisieren.
 * 
 * ```
 * {nnt3:frontendUser.get(key:'first_name')}
 * {nnt3:frontendUser.get()->f:variable.set(name:'feUser')}
 * ```
 */
class GetViewHelper extends AbstractViewHelper {
	
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('key', 'string', 'Welches Feld holen?', true);
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {

		$args = ['key'];
		foreach ($args as $arg) {
			$$arg = $arguments[$arg] ?? '';
		}

		$frontendUser = \nn\t3::FrontendUser()->get();
		return $key ? $frontendUser[$key] : $frontendUser;
	}
	
}