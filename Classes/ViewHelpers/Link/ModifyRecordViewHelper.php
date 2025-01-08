<?php
namespace Nng\Nnhelpers\ViewHelpers\Link;

use Nng\Nnhelpers\ViewHelpers\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper;

/**
 * Link zum Ändern von bestimmten Feldern eines Datensatzes in einem Backend-Modul generieren.
 * 
 * Beispiele: Das Feld "locked" auf 1 setzen
 * ```
 * <nnt3:link.modifyRecord update="{locked:1}" uid="{item.uid}" table="tx_myext_domain_model_entry">
 * 	<i class="fas fa-eye"></i>
 * </nnt3:link.modifyRecord>
 * ```
 */
class ModifyRecordViewHelper extends AbstractViewHelper {
	
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('parameter', 'string', 'Parameter für Typolink', false);
		$this->registerArgument('table', 'string', 'DB Tabellen-Name', true);
		$this->registerArgument('uid', 'string', 'UID in Tabelle', true);
		$this->registerArgument('update', 'array', 'Felder und Werte, die geupdated werden sollen', false, []);
		$this->registerArgument('data', 'array', 'Data-Attribut', false, []);
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) 
	{
		$args = ['table', 'uid', 'data', 'update'];
		foreach ($args as $arg) {
			$$arg = $arguments[$arg] ?? '';
		}

		$uriBuilder = \nn\t3::injectClass( \TYPO3\CMS\Backend\Routing\UriBuilder::class );

		// Routing: siehe Configuration/Backend/AjaxRequests.php
		$uri = $uriBuilder->buildUriFromRoute( 'nnt3_record_processing', [
			'redirect' 	=> \nn\t3::Request()->getUri(),
		]);

		// &data[tx_tablename_xx][10][hidden]=1
		$req = ['data'=>[$table=>[$uid=>$update ?: []]]];

		// data="{ajax:1}" in additionalAttributes für TypolinkViewHelper konvertieren.
		$dataAttr = [];
		foreach ($data as $k=>$v) {
			$dataAttr["data-{$k}"] = (string) $v;
		}

		$arguments['parameter'] = $uri . '&' . urldecode(http_build_query( $req ));
		$arguments['additionalAttributes'] = array_merge( $arguments['additionalAttributes'] ?? [], $dataAttr );
		
		return TypolinkViewHelper::renderStatic( $arguments, $renderChildrenClosure, $renderingContext );
	}
	
}