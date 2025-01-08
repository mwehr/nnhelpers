<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/**
 * Diverse Methoden, um mit Arrays zu arbeiten wie mergen, bereinigen oder leere Werte zu entfernen. 
 * Methoden, um ein Value eines assoziativen Arrays als Key zu verwenden.
 */
class Arrays extends \ArrayObject {

	/**
	 * 	@var mixed
	 */
	protected $initialArgument;

	/**
	 * 	Array konstruieren
	 */
	public function __construct ( $array = null ) {
		$this->initialArgument = $array;
		if (!is_array($array)) $array = [];
		parent::__construct( $array );
		return $this;
	}

	/**
	 * Ein assoziatives Array rekursiv mit einem anderen Array mergen.
	 * 
	 * `$addKeys` => wenn `false` werden nur Keys überschrieben, die auch in `$arr1` existieren 
	 * `$includeEmptyValues` => wenn `true` werden auch leere Values in `$arr1` übernommen
	 * `$enableUnsetFeature` => wenn `true`, kann `__UNSET` als Wert in `$arr2` verwendet werden, um eine Wert in `$arr1` zu löschen  
	 * 
	 * ```
	 * $mergedArray = \nn\t3::Arrays( $arr1 )->merge( $arr2, $addKeys, $includeEmptyValues, $enableUnsetFeature );
	 * $mergedArray = \nn\t3::Arrays( $arr1 )->merge( $arr2 );
	 * $mergedArray = \nn\t3::Arrays()->merge( $arr1, $arr2 );
	 * ```
	 * @return array
	 */
    public function merge() {
		$defaultArgs = [
			'arr1' 		=> [], 
			'arr2' 		=> [], 
			'addKeys' 	=> true, 
			'includeEmptyValues' => false, 
			'enableUnsetFeature' => true,
		];

		$args = func_get_args();
		if ($this->initialArgument !== null && !is_array($args[1] ?? '')) {
			array_unshift($args, $this->initialArgument );
		}

		foreach ($defaultArgs as $k=>$v) {
			$val = array_shift( $args );
			if ($val == null) $val = $v;
			${$k} = $val;
		}

		ArrayUtility::mergeRecursiveWithOverrule($arr1, $arr2, $addKeys, $includeEmptyValues, $enableUnsetFeature );
		return $arr1;
	}
	
	/**
	 * 	Leere Werte aus einem Array entfernen.
	 * 
	 * 	```
	 *	$clean = \nn\t3::Arrays( $arr1 )->removeEmpty();
	 *	```
	 * 
	 * 	@return array
	 */
    public function removeEmpty() {
		return $this->merge( [], $this->toArray() );
	}

	/**
	 * 	Einen String – oder Array – am Trennzeichen splitten, leere Elemente entfernen
	 * 	Funktioniert mit Strings und Arrays.
	 *	```
	 *	\nn\t3::Arrays('1,,2,3')->trimExplode();			// [1,2,3]
	 *	\nn\t3::Arrays('1,,2,3')->trimExplode( false );		// [1,'',2,3]
	 *	\nn\t3::Arrays('1|2|3')->trimExplode('|');			// [1,2,3]
	 *	\nn\t3::Arrays('1|2||3')->trimExplode('|', false);	// [1,2,'',3]
	 *	\nn\t3::Arrays('1|2,3')->trimExplode(['|', ',']);	// [1,2,3]
	 *	\nn\t3::Arrays(['1','','2','3'])->trimExplode();	// [1,2,3]
	 *	```
	 * 	@return array
	 */
    public function trimExplode( $delimiter = ',', $removeEmpty = true ) {
		$arr = $this->initialArgument !== null ? $this->initialArgument : (array) $this;
		
		if ($delimiter === false || $delimiter === true) {
			$delimiter = ',';
			$removeEmpty = $delimiter;
		}

		$firstDelimiter = is_array($delimiter) ? $delimiter[0] : $delimiter;

		if (is_array($arr)) $arr = join($firstDelimiter, $arr);
		if (is_array($delimiter)) {
			foreach ($delimiter as $d) {
				$arr = str_replace( $d, $firstDelimiter, $arr);
			}
			$delimiter = $firstDelimiter;
		}
		$arr = GeneralUtility::trimExplode( $delimiter, $arr, $removeEmpty );
		return $arr;
	}
	
	/**
	 * 	Einen String – oder Array – am Trennzeichen splitten, nicht numerische 
	 * 	und leere Elemente entfernen
	 * 	```
	 *	\nn\t3::Arrays('1,a,b,2,3')->intExplode();		// [1,2,3]
	 *	\nn\t3::Arrays(['1','a','2','3'])->intExplode();	// [1,2,3]
	 *	```
	 * 	@return array
	 */
    public function intExplode( $delimiter = ',' ) {
		$finals = [];
		if ($arr = $this->trimExplode($delimiter)) {
			foreach ($arr as $k=>$v) {
				if (is_numeric($v)) $finals[] = $v;
			}
		}
		return $finals;
	}

	/**
	 * 	Als Key des Arrays ein Feld im Array verwenden, z.B. um eine Liste zu bekommen,
	 * 	deren Key immer die UID des assoziativen Arrays ist:
	 *
	 * 	__Beispiel:__
	 * 	```
	 *	$arr = [['uid'=>'1', 'title'=>'Titel A'], ['uid'=>'2', 'title'=>'Titel B']];
	 *	\nn\t3::Arrays($arr)->key('uid');			// ['1'=>['uid'=>'1', 'title'=>'Titel A'], '2'=>['uid'=>'2', 'title'=>'Titel B']]
	 *	\nn\t3::Arrays($arr)->key('uid', 'title');	// ['1'=>'Titel A', '2'=>'Titel B']
	 *	```
	 * 	@return array
	 */
    public function key( $key = 'uid', $value = false ) {

		$arr = (array) $this;
		$values = $value === false ? array_values($arr) : array_column( $arr, $value );
		$combinedArray = array_combine( array_column($arr, $key), $values );

		$this->exchangeArray($combinedArray);
		return $this;
	}
	
	/**
	 * 	Assoziatives Array auf bestimmte Elemente reduzieren / destillieren:
	 *
	 * 	```
	 *	\nn\t3::Arrays( $objArr )->key('uid')->pluck('title');					// ['1'=>'Titel A', '2'=>'Titel B']
	 *	\nn\t3::Arrays( $objArr )->key('uid')->pluck(['title', 'bodytext']);	// ['1'=>['title'=>'Titel A', 'bodytext'=>'Inhalt'], '2'=>...]
	 *	\nn\t3::Arrays( ['uid'=>1, 'pid'=>2] )->pluck(['uid'], true);			// ['uid'=>1]
	 *	```
	 * 	@return array
	 */
    public function pluck( $keys = null, $isSingleObject = false ) {

		$arr = (array) $this;

		$pluckedArray = [];
		if ($getSingleKey = is_string($keys)) {
			$keys = [$keys];			
		}

		if ($isSingleObject) {
			$arr = [$arr];
		}

		foreach ($keys as $key) {
			foreach ($arr as $n=>$v) {
				if ($getSingleKey) {
					$pluckedArray[$n] = $v[$key] ?? '';
				} else {
					if (!isset($pluckedArray[$n])) $pluckedArray[$n] = [];
					$pluckedArray[$n][$key] = $v[$key] ?? '';
				}
			}
		}
		
		if ($isSingleObject) {
			$pluckedArray = array_pop($pluckedArray);
		}

		$this->exchangeArray($pluckedArray);
		return $this;
	}


	/**
	 * 	Gibt dieses Array-Object als "normales" Array zurück.
	 * 	```
	 *	\nn\t3::Arrays( $objArr )->key('uid')->toArray();
	 * 	```
	 * 	@return array
	 */
	public function toArray () {
		return (array) $this;
	}
	
	/**
	 * 	Gibt das erste Element des Arrays zurück, ohne array_shift()
	 * 	```
	 *	\nn\t3::Arrays( $objArr )->first();
	 * 	```
	 * 	@return array
	 */
	public function first () {
		$arr = (array) $this;
		if (!$arr) return false;
		foreach ($arr as $k=>$v) return $v;
	}
}