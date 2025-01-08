<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Exception;

/**
 * Fehler und Exceptions ausgeben
 */
class Errors implements SingletonInterface {
   
	/**
	 * Eine Typo3-Exception werfen mit Backtrace
	 * ```
	 * \nn\t3::Errors()->Exception('Damn', 1234);
	 * ```
	 * Ist ein Alias zu:
	 * ```
	 * \nn\t3::Exception('Damn', 1234);
	 * ```
	 * @return void
	 */
    public function Exception ( $message, $code = null ) {
		if (!$code) $code = time();
		throw new Exception( $message, $code );
	}
	
	/**
	 * Einen Error werfen mit Backtrace
	 * ```
	 * \nn\t3::Errors()->Error('Damn', 1234);
	 * ```
	 * Ist ein Alias zu:
	 * ```
	 * \nn\t3::Error('Damn', 1234);
	 * ```
	 * @return void
	 */
    public function Error ( $message, $code = null ) {
		if (!$code) $code = time();
		throw new \Error( $message, $code );
	}

}