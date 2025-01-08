<?php
namespace Nng\Nnhelpers\ViewHelpers\Link;

use Nng\Nnhelpers\ViewHelpers\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper;

/**
 * Link zum Editieren eines Datensatzes in einem Backend-Modul generieren.
 * ```
 * <nnt3:link.editRecord uid="{item.uid}" data="{ajax:1}" table="tx_myext_domain_model_entry" returnUrl="...">
 * 	<i class="fas fa-eye"></i>
 * </nnt3:link.editRecord>
 * ```
 * Alternativ kann auch der Core-ViewHelper genutzt werden:
 * ```
 * {namespace be=TYPO3\CMS\Backend\ViewHelpers}
 * <be:link.editRecord uid="42" table="a_table" returnUrl="foo/bar" />
 * ```
 */
class EditRecordViewHelper extends AbstractViewHelper {
	
	/**
     * @var bool
     */
    protected $escapeOutput = false;

	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('parameter', 'string', 'Parameter für Typolink', false);
		$this->registerArgument('table', 'string', 'DB Tabellen-Name', true);
		$this->registerArgument('returnUrl', 'string', 'Nach Schließen des Editors: Zu welcher URL zurückkehren?', false);
		$this->registerArgument('uid', 'string', 'UID in Tabelle', true);
		$this->registerArgument('data', 'array', 'additional data attributes', false, []);

        $this->registerArgument('additionalParams', 'string', 'stdWrap.typolink additionalParams', false, '');
		$this->registerArgument('additionalAttributes', 'array', 'Additional tag attributes to be added directly to the resulting HTML tag', false, []);
        $this->registerArgument('language', 'string', 'link to a specific language - defaults to the current language, use a language ID or "current" to enforce a specific language', false);
        $this->registerArgument('addQueryString', 'string', 'If set, the current query parameters will be kept in the URL. If set to "untrusted", then ALL query parameters will be added. Be aware, that this might lead to problems when the generated link is cached.', false, false);
        $this->registerArgument('addQueryStringExclude', 'string', 'Define parameters to be excluded from the query string (only active if addQueryString is set)', false, '');
        $this->registerArgument('absolute', 'bool', 'Ensure the resulting URL is an absolute URL', false, false);
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) 
	{
		$args = ['table', 'uid', 'returnUrl', 'data'];

		foreach ($args as $arg) {
			$$arg = $arguments[$arg] ?? '';
		}

		foreach (($data ?: []) as $k=>$v) {
			$arguments['additionalAttributes']["data-{$k}"] = (string) $v;
		}

		if (!$returnUrl) {
			$returnUrl = \nn\t3::Request()->getUri();
		}

		$uriBuilder = \nn\t3::injectClass( \TYPO3\CMS\Backend\Routing\UriBuilder::class );

		$uri = $uriBuilder->buildUriFromRoute( 'record_edit', [
			'returnUrl'	=> $returnUrl,
			'edit' 		=> [$table=>[$uid=>'edit']]
		]);
		
		$arguments['parameter'] = $uri;

		return TypolinkViewHelper::renderStatic( $arguments, $renderChildrenClosure, $renderingContext );
	}
	
}