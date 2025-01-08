<?php

namespace Nng\Nnhelpers\Hooks;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Form\AbstractNode;

/**
 * Erlaubt das dynamische Einfügen von FlexForms in andere FlexForms:
 * 
 * ```
 * <sliderOpt>
 *		<TCEforms>
 *			<label>Konfiguration</label>
 *			<config>
 *				<type>nnt3_flex</type>
 *				<path>EXT:nnsite/Configuration/FlexForms/slickslider_options.xml</path>
 *			</config>
 *		</TCEforms>
 *	</sliderOpt>
 * ```
 */
class FlexFormHook {

   /**
	* @param array $dataStructure
	* @param array $identifier
	* @return array
	*/
   public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier): array
   {
		foreach (($dataStructure['sheets'] ?? []) as $sheetName=>$sheet) {
			if (!is_array($sheet['ROOT']['el'] ?? false)) continue;
			foreach ($sheet['ROOT']['el'] as $field=>$conf) {
				if (($conf['TCEforms']['config']['type'] ?? false) == 'nnt3_flex') {
					if ($path = $conf['TCEforms']['config']['path'] ?? false) {
						$path = \nn\t3::File()->resolvePathPrefixes( $path );
						if (\nn\t3::File()->exists($path)) {
							$xml = \nn\t3::File()->read( $path );
							$xmlArray = GeneralUtility::xml2array($xml);
							foreach ($xmlArray['sheets'] as $extSheetName=>$extSheet) {
								foreach ($extSheet['ROOT']['el'] as $extField=>$extConf) {
									if (!$dataStructure['sheets'][$extSheetName]) {
										$dataStructure['sheets'][$extSheetName] = $extSheet;
										$dataStructure['sheets'][$extSheetName]['ROOT']['el'] = [];
									}
									$dataStructure['sheets'][$extSheetName]['ROOT']['el'][$field.'.'.$extField] = $extConf;
								}
							}
						}
					}
				}
			}
		}
		return $dataStructure;
   }

}