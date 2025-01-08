<?php
namespace Nng\Nnhelpers\ViewHelpers\Encrypt;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Generiert einen Hash aus einem String oder einer Zahl.
 * ```
 * {secret->nnt3:encrypt.hash()}
 * {nnt3:encrypt(value:secret)}
 * ```
 * Hilfreich, falls z.B. eine Mail versendet werden soll mit Bestätigungs-Link.
 * 
 * Die UID des Datensatzes wird zusätzlich als Hash übergeben. Im Controller wird dann überprüft, 
 * ob aus der übergeben `uid` der übergeben `hash` generiert werden kann. 
 * Falls nicht, wurde die `uid` manipuliert.
 * ```
 * <f:link.action action="validate" arguments="{uid:uid, checksum:'{uid->nnt3:encrypt.hash()}'}">
 *   ...
 * </f:link.action>
 * ``` 
 * @return string
 */
class HashViewHelper extends AbstractViewHelper {

	use CompileWithRenderStatic;

	protected $escapeOutput = false;

	public function initializeArguments() {
	   $this->registerArgument('value', 'string', 'Value to be hashed', false);
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
		$value = $arguments['value'] ?: $renderChildrenClosure();
		return \nn\t3::Encrypt()->hash( $value );
	}
    
}
