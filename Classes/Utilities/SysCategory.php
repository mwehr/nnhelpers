<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use Nng\Nnhelpers\Domain\Repository\CategoryRepository;

/**
 * Vereinfacht die Arbeit und den Zugriff auf die `sys_category` von Typo3
 */
class SysCategory implements SingletonInterface {

	/**
	 * 	Liste aller sys_categories holen
	 *	```
	 *	\nn\t3::SysCategory()->findAll();
	 *	```
	 * 	@return array
	 */
	public function findAll ( $branchUid = null ) {
		$categoryRepository = \nn\t3::injectClass( CategoryRepository::class );
		$allCategories = $categoryRepository->findAll();
		return $allCategories;
	}
	
	/**
	 * 	Liste aller sys_categories holen, `uid` als Key zurückgeben
	 *	```
	 *	\nn\t3::SysCategory()->findAllByUid();
	 *	```
	 * 	@return array
	 */
	public function findAllByUid ( $branchUid = null ) {
		$allCategories = $this->findAll( $branchUid );
		$allCategoriesByUid = [];
		foreach ($allCategories as $cat) {
			$allCategoriesByUid[$cat->getUid()] = $cat;
		}
		return $allCategoriesByUid;
	}

	/**
	 * sys_categories anhand von uid(s) holen.
	 * ```
	 * \nn\t3::SysCategory()->findByUid( 12 );
	 * \nn\t3::SysCategory()->findByUid( '12,11,5' );
	 * \nn\t3::SysCategory()->findByUid( [12, 11, 5] );
	 * ```
	 * @return array|\TYPO3\CMS\Extbase\Domain\Model\Category
	 */
	public function findByUid( $uidList = null ) {
		$returnFirst = !is_array($uidList) && is_numeric($uidList);
		$uidList = \nn\t3::Arrays($uidList)->intExplode();
		$allCategoriesByUid = $this->findAllByUid();
		$result = [];
		foreach ($uidList as $uid) {
			if ($cat = $allCategoriesByUid[$uid]) {
				$result[$uid] = $cat;
			}
		}
		return $returnFirst ? array_shift($result) : $result;
	}

	/**
	 * Den gesamten SysCategory-Baum (als Array) holen.
	 * Jeder Knotenpunkt hat die Attribute 'parent' und 'children', um
	 * rekursiv durch Baum iterieren zu können.
	 * ```
	 * // Gesamten Baum holen
	 * \nn\t3::SysCategory()->getTree();
	 * 
	 * // Bestimmten Ast des Baums holen
	 * \nn\t3::SysCategory()->getTree( $uid );
	 * 
	 * // Alle Äste des Baums holen, key ist die UID der SysCategory
	 * \nn\t3::SysCategory()->getTree( true );
	 * ```
	 * ToDo: Prüfen, ob Caching sinnvoll ist
	 * 
	 * @return array
	 */
	public function getTree ( $branchUid = null ) {

		// Alle Kategorien laden
		$allCategories = $this->findAll();

		// Array mit uid als Key erstellen
		$categoriesByUid = [0=>['children'=>[]]];
		foreach ($allCategories as $sysCategory) {

			// Object zu Array konvertieren
			$sysCatArray = \nn\t3::Obj()->toArray($sysCategory, 3);

			$sysCatArray['children'] = [];
			$sysCatArray['_parent'] = $sysCatArray['parent'];
			$categoriesByUid[$sysCatArray['uid']] = $sysCatArray;
		}

		// Baum generieren
		foreach ($categoriesByUid as $uid=>$sysCatArray) {
			$parent = $sysCatArray['_parent'];
			if ($parent['uid'] != $uid) {
				$parentUid = $parent ? $parent['uid'] : 0;
				$categoriesByUid[$parentUid]['children'][$uid] = &$categoriesByUid[$uid];
				$categoriesByUid[$uid]['parent'] = $parentUid > 0 ? $categoriesByUid[$parentUid] : false;
				unset($categoriesByUid[$uid]['_parent']);
			}
		}

		// Wurzel
		$root = $categoriesByUid[0]['children'] ?: [];

		// Ganzen Baum – oder nur bestimmten Branch zurückgeben?
		if (!$branchUid) return $root;
		
		// Alle Äste holen
		if ($branchUid === true) {
			return $categoriesByUid;
		}

		// bestimmten Branch holen
		return $root[$branchUid] ?: [];
	}

}
