<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * 	Methoden, zum Lesen und Schreiben in den Typo3 Cache.
 * 	Nutzt das Caching-Framework von Typo3, siehe `EXT:nnhelpers/ext_localconf.php` für Details
 */
class Cache extends \Nng\Nnhelpers\Singleton {
	
	/**
	 * @var \TYPO3\CMS\Core\Cache\CacheManager
	 */
	protected $cacheManager;

	/**
	 * Injections
	 * 
	 */
	public function __construct() {
		$this->cacheManager = GeneralUtility::makeInstance( \TYPO3\CMS\Core\Cache\CacheManager::class );
	}

	/**
	 * Lädt Inhalt des Typo3-Caches anhand eines Identifiers.
	 * Der Identifier ist ein beliebiger String oder ein Array, der den Cache eindeutif Identifiziert.
	 * ```
	 * \nn\t3::Cache()->get('myid');
	 * \nn\t3::Cache()->get(['pid'=>1, 'uid'=>'7']);
	 * \nn\t3::Cache()->get(['func'=>__METHOD__, 'uid'=>'17']);
	 * \nn\t3::Cache()->get([__METHOD__=>$this->request->getArguments()]);
	 * ```
	 * 
	 * @param mixed $identifier	String oder Array zum Identifizieren des Cache
	 * @param mixed $useRamCache	temporärer Cache in $GLOBALS statt Caching-Framework
	 * 
	 * @return mixed
	 */
	public function get( $identifier = '', $useRamCache = false ) {
		$identifier = self::getIdentifier( $identifier );

		// Ram-Cache verwenden? Einfache globale.
		if ($useRamCache && ($cache = $GLOBALS['nnhelpers_cache'][$identifier] ?? false)) {
			return $cache;
		}
		
		$cacheUtility = $this->cacheManager->getCache('nnhelpers');
		if ($data = $cacheUtility->get($identifier)) {
			$data = json_decode( $cacheUtility->get($identifier), true );
			if ($data['content'] && $data['expires'] < time()) return false;
			return $data['content'] ?: false;
		}
		return false;
	}

	/**
	 * Schreibt einen Eintrag in den Typo3-Cache.
	 * Der Identifier ist ein beliebiger String oder ein Array, der den Cache eindeutif Identifiziert.
	 * ```
	 * // Klassische Anwendung im Controller: Cache holen und setzen
	 * if ($cache = \nn\t3::Cache()->get('myid')) return $cache;
	 * ...
	 * $cache = $this->view->render();
	 * return \nn\t3::Cache()->set('myid', $cache);
	 * ```
	 * 
	 * ```
	 * // RAM-Cache verwenden? TRUE als dritter Parameter setzen
	 * \nn\t3::Cache()->set('myid', $dataToCache, true);
	 * 
	 * // Dauer des Cache auf 60 Minuten festlegen
	 * \nn\t3::Cache()->set('myid', $dataToCache, 3600);
	 * 
	 * // Als key kann auch ein Array angegeben werden
	 * \nn\t3::Cache()->set(['pid'=>1, 'uid'=>'7'], $html);
	 * ````
	 * @param mixed $indentifier	String oder Array zum Identifizieren des Cache
	 * @param mixed $data			Daten, die in den Cache geschrieben werden sollen. (String oder Array)
	 * @param mixed $useRamCache	`true`: temporärer Cache in $GLOBALS statt Caching-Framework. 
	 * 								`integer`: Wie viele Sekunden cachen?
	 *
	 * @return mixed
	 */
	public function set( $identifier = '', $data = null, $useRamCache = false ) {
		$identifier = self::getIdentifier( $identifier );
		$lifetime = 86400;
		
		if ($useRamCache === true) {
			if (!isset($GLOBALS['nnhelpers_cache'])) {
				$GLOBALS['nnhelpers_cache'] = [];
			}
			return $GLOBALS['nnhelpers_cache'][$identifier] = $data;
		} else if ( $useRamCache !== false ) {
			$lifetime = intval($useRamCache);
		}

		$expires = time() + $lifetime;

		$cacheUtility = $this->cacheManager->getCache('nnhelpers');
		$serializedData = json_encode(['content'=>$data, 'expires'=>$expires]);
		$cacheUtility->set($identifier, $serializedData, [], $lifetime);
		return $data;
	}

	/**
	 * Wandelt übergebenen Cache-Identifier in brauchbaren String um.
	 * Kann auch ein Array als Identifier verarbeiten.
	 * 
	 * @param mixed $indentifier
	 * @return string
	 */
	public static function getIdentifier( $identifier = null ) {
		if (is_array($identifier)) {
			$identifier = json_encode($identifier);
		}
		return md5($identifier);
	}
	
	/**
	 * Löscht den Seiten-Cache. Alias zu `\nn\t3::Page()->clearCache()`
	 * ```
	 * \nn\t3::Cache()->clearPageCache( 17 );		// Seiten-Cache für pid=17 löschen
	 * \nn\t3::Cache()->clearPageCache();			// Cache ALLER Seiten löschen
	 * ```
	 *
	 * @param mixed $pid 	pid der Seite, deren Cache gelöscht werden soll oder leer lassen für alle Seite
	 * @return void
	 */
	public function clearPageCache( $pid = null ) {
		return \nn\t3::Page()->clearCache( $pid );
	}
	
	/**
	 * Löscht Caches.
	 * Wird ein `identifier` angegeben, dann werden nur die Caches des spezifischen
	 * identifiers gelöscht – sonst ALLE Caches aller Extensions und Seiten.
	 * 
	 * - RAM-Caches
	 * - CachingFramework-Caches, die per `\nn\t3::Cache()->set()` gesetzt wurde
	 * - Datei-Caches, die per `\nn\t3::Cache()->write()` gesetzt wurde
	 * 
	 * ```
	 * // ALLE Caches löschen – auch die Caches anderer Extensions, der Seiten etc.
	 * \nn\t3::Cache()->clear();
	 * 
	 * // Nur die Caches mit einem bestimmten Identifier löschen
	 * \nn\t3::Cache()->clear('nnhelpers');
	 * ```
	 * 
	 * @param string $identifier
	 * @return void
	 */
	public function clear( $identifier = null ) {
		
		if (!$identifier) {
			// ALLE TYPO3 Caches löschen, der über das CachingFramework registriert wurde
			$this->cacheManager->flushCaches();
		} else {
			// Spezifischen Cache löschen
			if ($cacheUtility = $this->cacheManager->getCache( $identifier )) {
				$cacheUtility->flush();
			}
		}

		if (!$identifier || $identifier == 'nnhelpers') {
			// RAM Cache löschen
			$GLOBALS['nnhelpers_cache'] = [];
	
			// File-Cache löschen
			$files = \nn\t3::Environment()->getVarPath() . "/cache/code/nnhelpers/*.php";
			foreach (glob($files) as $file) {
				unlink( $file );
			}
		}
	}
	
	/**
	 * Statischen Datei-Cache schreiben.
	 * 
	 * Schreibt eine PHP-Datei, die per `$cache = require('...')` geladen werden kann.
	 * 
	 * Angelehnt an viele Core-Funktionen und Extensions (z.B. mask), die statische PHP-Dateien
	 * ins Filesystem legen, um performancelastige Prozesse wie Klassenpfade, Annotation-Parsing etc.
	 * besser zu cachen. Nutzt bewußt __nicht__ die Core-Funktionen, um jeglichen Overhead zu
	 * vermeiden und größtmögliche Kompatibilität bei Core-Updates zu gewährleisten.
	 * 
	 * ```
	 * $cache = ['a'=>1, 'b'=>2];
	 * $identifier = 'myid';
	 * 
	 * \nn\t3::Cache()->write( $identifier, $cache );
	 * $read = \nn\t3::Cache()->read( $identifier );
	 * ```
	 * Das Beispiel oben generiert eine PHP-Datei mit diesem Inhalt:
	 * ```
	 * <?php
	 * return ['_' => ['a'=>1, 'b'=>2]];
	 * ```
	 *
	 * @return string|array
	 */
	public function write( $identifier, $cache ) 
	{
		$this->set( $identifier, $cache, true );

		$identifier = self::getIdentifier( $identifier );
		$phpCode = '<?php return ' . var_export(['_' => $cache], true) . ';';

		$path = \nn\t3::Environment()->getVarPath() . "cache/code/nnhelpers/{$identifier}.php";
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFileToTypo3tempDir( $path, $phpCode );

		return $cache;
	}

	/**
	 * Statischen Datei-Cache lesen.
	 * 
	 * Liest die PHP-Datei, die per `\nn\t3::Cache()->write()` geschrieben wurde.
	 * ```
	 * $cache = \nn\t3::Cache()->read( $identifier );
	 * ```
	 * Die PHP-Datei ist ein ausführbares PHP-Script mit dem `return`-Befehl. 
	 * Sie speichert den Cache-Inhalt in einem Array.
	 * ```
	 * <?php
	 * 	return ['_'=>...];
	 * ```
	 * 
	 * @return string|array
	 */
	public function read( $identifier ) {

		if ($cache = $this->get( $identifier, true )) return $cache;
		$identifier = self::getIdentifier( $identifier );
		
		$path = \nn\t3::Environment()->getVarPath() . "/cache/code/nnhelpers/{$identifier}.php";
		
		if (!file_exists($path)) {
			return null;
		}

		$cache = require( $path );
		return $cache['_'];
	}

	
}