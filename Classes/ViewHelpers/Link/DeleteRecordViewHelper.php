<?php
namespace Nng\Nnhelpers\ViewHelpers\Link;

use Nng\Nnhelpers\ViewHelpers\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper;

/**
 * Link zum Löschen eines Datensatzes für ein Backend-Modul generieren.
 * ```
 * <nnt3:link.deleteRecord uid="{item.uid}" data="{ajax:1}" table="tx_myext_domain_model_entry">
 * 	<i class="fas fa-trash"></i>
 * </nnt3:link.deleteRecord>
 * ```
 */
class DeleteRecordViewHelper extends AbstractViewHelper {
	
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('parameter', 'string', 'Parameter für Typolink', false);
		$this->registerArgument('table', 'string', 'DB Tabellen-Name', true);
		$this->registerArgument('uid', 'string', 'UID in Tabelle', true);
		$this->registerArgument('data', 'array', 'Data-Attribut', false, []);
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) 
	{
		$args = ['table', 'uid', 'data'];
		foreach ($args as $arg) {
			$$arg = $arguments[$arg] ?? '';
		}
		
		$uriBuilder = \nn\t3::injectClass( \TYPO3\CMS\Backend\Routing\UriBuilder::class );

		// Routing: siehe Configuration/Backend/AjaxRequests.php
		$uri = $uriBuilder->buildUriFromRoute( 'nnt3_record_processing', [
			'redirect' 	=> \nn\t3::Request()->getUri(),
		]);

		// &cmd[tx_tablename_xx][10][delete]=1
		$req = [
			'cmd' 		=> [$table=>[$uid=>['delete' => 1]]],
		];

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