<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\Resource\Folder;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Resource\Driver\DriverRegistry;


/**
 * Alles rund um Storages
 */
class Storage extends \TYPO3\CMS\Core\Resource\StorageRepository {
/*
	public function __construct(
		private readonly StorageRepository $storageRepository
		EventDispatcherInterface $eventDispatcher, 
		DriverRegistry $driverRegistry)
    {
		parent::__construct($eventDispatcher, $driverRegistry);
//		$storage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Resource\StorageRepository::class );
    }
*/
	/**
	 * 	Löscht den StorageRowCache
	 *	```
	 * 	\nn\t3::Storage()->clearStorageRowCache();
	 *	```
	 * 	@return void
	 */
	// clearStorageRowCache
	public function clearStorageRowCache () {
		$this->storageRowCache = NULL;
		$this->initializeLocalCache();
	}
	
	/**
	 *	Gibt den \Folder-Object für einen Zielordner (oder Datei) innerhalb einer Storage zurück.
	 *	Legt Ordner an, falls er noch nicht existiert
	 *	
	 *	Beispiele:
	 *	```
	 *	\nn\t3::Storage()->getFolder( 'fileadmin/test/beispiel.txt' );
	 *	\nn\t3::Storage()->getFolder( 'fileadmin/test/' );
	 *			==>	gibt \Folder-Object für den Ordner 'test/' zurück	
	 *	```
	 *	@return Folder
	 */
	// getFolderInStorage
	public function getFolder( $file, $storage = null ) {
		
		$storage = $storage ?: \nn\t3::File()->getStorage( $file );
		if (!$storage) return false;

		$storageConfiguration = $storage->getConfiguration();
		
		$dirname = \nn\t3::File()->getFolder($file);
		$folderPathInStorage = substr($dirname, strlen($storageConfiguration['basePath']));
		
		// Ordner existiert bereits
		if ($storage->hasFolder($folderPathInStorage)) return $storage->getFolder( $folderPathInStorage );

		// Ordner muss angelegt werden
		return $storage->createFolder($folderPathInStorage);
	}

	/**
	 * 	Im Controller: Aktuelle StoragePid für ein PlugIn holen.
	 * 	Alias zu `\nn\t3::Settings()->getStoragePid()`
	 *	```
	 *	\nn\t3::Storage()->getPid();
	 *	\nn\t3::Storage()->getPid('news');
	 *	```
	 *	@return string
	 */
	public function getPid ( $extName = null ) {
		return \nn\t3::Settings()->getStoragePid( $extName );
	}

}
