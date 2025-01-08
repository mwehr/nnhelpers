<?php
namespace Nng\Nnhelpers\ViewHelpers\Link;

use Nng\Nnhelpers\ViewHelpers\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper;

/**
 * Link zum Verstecken / Sichtbar machen eines Datensatzes in einem Backend-Modul generieren.
 * 
 * Link zum Verstecken eines Datensatzes:
 * ```
 * <nnt3:link.hideRecord uid="{item.uid}" data="{ajax:1}" table="tx_myext_domain_model_entry" hidden="1">
 * 	<i class="fas fa-eye"></i>
 * </nnt3:link.hideRecord>
 * ```
 * Link zum show/hide-Toggle eines Datensatzes:
 * ```
 * <nnt3:link.hideRecord uid="{item.uid}" data="{ajax:1}" table="tx_myext_domain_model_entry" visible="{item.hidden}">
 * 	<i class="fas fa-toggle"></i>
 * </nnt3:link.hideRecord>
 * ```
 */
class HideRecordViewHelper extends AbstractViewHelper {
	
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('parameter', 'string', 'Parameter für Typolink', false);
		$this->registerArgument('table', 'string', 'DB Tabellen-Name', true);
		$this->registerArgument('uid', 'string', 'UID in Tabelle', true);
		$this->registerArgument('hidden', 'intval', 'Sichtbar?', false, 1);
		$this->registerArgument('visible', 'intval', 'Invers von sichtbar', false, '');
		$this->registerArgument('data', 'array', 'Data-Attribut', false, []);
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) 
	{
		$args = ['table', 'uid', 'hidden', 'visible', 'data'];
		foreach ($args as $arg) {
			$$arg = $arguments[$arg] ?? '';
		}

		// visible kann statt hidden gesetzt werden, um unkompliziert einen Toggle-Button zu bauen
		if ($visible !== '') {
			$hidden = $visible ? 0 : 1;
		}
		
		$uriBuilder = \nn\t3::injectClass( \TYPO3\CMS\Backend\Routing\UriBuilder::class );

		// Routing: siehe Configuration/Backend/AjaxRequests.php
		$uri = $uriBuilder->buildUriFromRoute( 'nnt3_record_processing', [
			'redirect' 	=> \nn\t3::Request()->getUri(),
		]);
		
		// &data[tx_tablename_xx][10][hidden]=1
		$req = ['data'=>[$table=>[$uid=>['hidden' => ($hidden ? 1 : 0)]]]];

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