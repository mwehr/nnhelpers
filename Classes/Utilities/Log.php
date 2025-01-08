<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Log in die Tabelle `sys_log`
 */
class Log implements SingletonInterface 
{
	/**
	 * 	Schreibt einen Eintrag in die Tabelle `sys_log`.
	 * 	Der severity-Level kann angegeben werden, z.B. `info`, `warning` oder `error`
	 *	```
	 *	\nn\t3::Log()->log( 'extname', 'Alles übel.', ['nix'=>'gut'], 'error' );
	 *	\nn\t3::Log()->log( 'extname', 'Alles schön.' );
	 *	```
	 * 	@return mixed
	 */
	public function log( $extName = 'nnhelpers', $message = null, $data = [], $severity = 'info' ) {

		if (is_array($message)) $message = join(" · ", $message);

		$severity = strtoupper( $severity );
		$logLevel = constant( "\TYPO3\CMS\Core\Log\LogLevel::$severity" );

		$type = $severity == 'ERROR' ? 5 : 4;	// 4 = type: EXTENSION

		// Die Core-Methode ist schön, allerdings nur, wenn man wirklich diese Flexibiltät braucht.
		// Leider sind die Log-Einträge mit dem Core DatabaseWriter nicht im Backend sichtbar.
		// Wir wollen nur einen einfach Eintrag in sys_log haben und nutzen einen simplen INSERT
		/*
		$logger = GeneralUtility::makeInstance( LogManager::class )->getLogger( __CLASS__ );
		$logger->log( $logLevel, $message, $params );
		*/

		\nn\t3::Db()->insert('sys_log', [
			'details' 		=> "[{$extName}] {$message} " . ($data ? print_r( $data, true ) : ''),
			'action' 		=> $data['action'] ?? 0,
			'level'			=> $logLevel,
			'type'			=> $type,
			'log_data'		=> serialize($data),
			'error'			=> $severity == 'ERROR' ? 1 : 0,
			'tstamp'		=> time(),
			'IP'			=> $_SERVER['REMOTE_ADDR'] ?? '',
		]);
	}


	/**
	 *  Eine Warnung in die Tabelle sys_log schreiben.
	 * 	Kurzschreibweise für \nn\t3::Log()->log(..., 'error');
	 *	```
	 * 	\nn\t3::Log()->error( 'extname', 'Text', ['die'=>'daten'] );
	 *	```
	 * 	return void
	 */
	public function error( $extName = '', $message = '', $data = []) {
		$this->log( $extName, $message, $data, 'error' );
	}
	

	/**
	 *  Eine Info in die Tabelle sys_log schreiben.
	 * 	Kurzschreibweise für \nn\t3::Log()->log(..., 'info');
	 *	```
	 * 	\nn\t3::Log()->error( 'extname', 'Text', ['die'=>'daten'] );
	 *	```
	 * 	return void
	 */
	public function info( $extName = '', $message = '', $data = []) {
		$this->log( $extName, $message, $data, 'info' );
	}

}