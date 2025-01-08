<?php

namespace Nng\Nnhelpers\Utilities;

use nn\t3;
use Nng\Nnhelpers\Domain\Repository\FileReferenceRepository;
use Nng\Nnhelpers\Domain\Repository\FileRepository;
use Nng\Nnhelpers\Domain\Model\FileReference;

use TYPO3\CMS\Core\Resource\FileReference as FalFileReference;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as SysFileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\Index\Indexer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * Methoden zum Erzeugen von sysFile und sysFileReference-Einträgen.
 *
 * Spickzettel:
 * ```
 * \TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage
 *  |
 *  └─ \TYPO3\CMS\Extbase\Domain\Model\FileReference
 * 		... getOriginalResource()
 * 				|
 * 				└─ \TYPO3\CMS\Core\Resource\FileReference
 * 					... getOriginalFile()
 * 							|
 * 							└─ \TYPO3\CMS\Core\Resource\File
 * ```
 */
class Fal implements SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 */
	protected $persistenceManager;

	/**
	 * Konstruieren
	 */
	public function __construct () {
		$this->persistenceManager = \nn\t3::injectClass(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
	}

	/**
	 * Eine Datei zu einem FileReference-Object konvertieren und
	 * an die Property oder ObjectStorage eines Models hängen.
	 * Siehe auch: `\nn\t3::Fal()->setInModel( $member, 'falslideshow', $imagesToSet );` mit dem
	 * Array von mehreren Bildern an eine ObjectStorage gehängt werden können.
	 * ```
	 * \nn\t3::Fal()->attach( $model, $fieldName, $filePath );
	 * \nn\t3::Fal()->attach( $model, 'image', 'fileadmin/user_uploads/image.jpg' );
	 * \nn\t3::Fal()->attach( $model, 'image', ['publicUrl'=>'fileadmin/user_uploads/image.jpg'] );
	 * \nn\t3::Fal()->attach( $model, 'image', ['publicUrl'=>'fileadmin/user_uploads/image.jpg', 'title'=>'Titel...'] );
	 * ```
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|array
	 */
	public function attach ( $model, $field, $itemData = null ) 
	{
		if (!$itemData) return;
		$returnFirstFal = !is_array($itemData);
		$falsCreated = [];

		if ($returnFirstFal) {
			$itemData = [$itemData];
		}

		foreach ($itemData as $item) {
			$fal = $this->createForModel($model, $field, $item);
	
			$propVal = \nn\t3::Obj()->prop($model, $field);
			$isStorage = \nn\t3::Obj()->isStorage( $propVal );
	
			if ($fal) {
				$falsCreated[] = $fal;
				if ($isStorage) {
					$propVal->attach( $fal );
				} else {
					\nn\t3::Obj()->set( $model, $field, $fal );
				}
			}
		}

		return $returnFirstFal ? array_pop($falsCreated) : $falsCreated;;
	}

	/**
	 * Eine Datei zu einem FileReference-Object konvertieren und für `attach()` an ein vorhandenes 
	 * Model und Feld / Property vorbereiten. Die FileReference wird dabei __nicht__ automatisch
	 * an das Model gehängt. Um das FAL direkt in dem Model zu setzen, kann der Helper 
	 * `\nn\t3::Fal()->attach( $model, $field, $itemData )` verwendet werden.
	 * ```
	 * \nn\t3::Fal()->createForModel( $model, $fieldName, $filePath );
	 * \nn\t3::Fal()->createForModel( $model, 'image', 'fileadmin/user_uploads/image.jpg' );
	 * \nn\t3::Fal()->createForModel( $model, 'image', ['publicUrl'=>'fileadmin/user_uploads/image.jpg'] );
	 * \nn\t3::Fal()->createForModel( $model, 'image', ['publicUrl'=>'fileadmin/user_uploads/image.jpg', 'title'=>'Titel...'] );
	 * ```
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	public function createForModel( $model, $field, $itemData = null ) 
	{
		$objHelper = \nn\t3::Obj();
		
		if (is_string($itemData)) {
			$itemData = ['publicUrl'=>$itemData];
		}

		if ($objHelper->isFile($itemData)) {
			return $this->fromFalFile($itemData);
		}

		$filePath = $itemData['publicUrl'];

		if (!$filePath || !\nn\t3::File()->exists($filePath)) {
			\nn\t3::Exception('\nn\t3::Fal()->attach() :: File not found.');
		}

		$propVal = $objHelper->prop($model, $field);
		$isStorage = $objHelper->isStorage( $propVal );
		$table = $objHelper->getTableName( $model );
		$cruser_id = \nn\t3::FrontendUser()->getCurrentUserUid();

		$sorting = $isStorage ? count($propVal) : 0;

		$fal = $this->fromFile([
			'src'           => $filePath,
			'title'			=> $itemData['title'] ?? null,
			'description'	=> $itemData['description'] ?? null,
			'link'			=> $itemData['link'] ?? '',
			'crop'			=> $itemData['crop'] ?? '',
			'sorting'		=> $itemData['sorting'] ?? $sorting,
			'pid'           => $model->getPid(),
			'uid'           => $model->getUid(),
			'table'         => $table,
			'field'         => $field,
			'cruser_id'     => $cruser_id,
		]);

		return $fal;
	}


	/**
	 * Leert eine ObjectStorage in einem Model oder entfernt ein 
	 * einzelnes Object vom Model oder einer ObjectStorage.
	 * Im Beispiel kann `image` eine ObjectStorage oder eine einzelne `FileReference` sein: 
	 * ```
	 * \nn\t3::Fal()->detach( $model, 'image' );
	 * \nn\t3::Fal()->detach( $model, 'image', $singleObjToRemove );
	 * ```
	 * @return void
	 */
	public function detach ( $model, $field, $obj = null ) {
		$propVal = \nn\t3::Obj()->prop($model, $field);
		$isStorage = \nn\t3::Obj()->isStorage( $propVal );
		if ($isStorage) {
			foreach ($propVal->toArray() as $item) {
				if (!$obj || $obj->getUid() == $item->getUid()) {
					$propVal->detach( $item );
				}
			}
		} else if ($propVal) {
			$this->deleteSysFileReference( $propVal );
			\nn\t3::Obj()->set( $model, $field, null, false );
		}
	}
	
	/**
	 * Löscht die physischen Dateien für ein Model (oder ein einzelnes 
	 * Feld des Models) vom Server.
	 * ```
	 * // ALLE Dateien des gesamten Models löschen
	 * \nn\t3::Fal()->deleteForModel( $model );
	 * 
	 * // ALLE Dateien aus dem Feld "images" löschen
	 * \nn\t3::Fal()->deleteForModel( $model, 'images' );
	 * ```
	 * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model
	 * @param string $field
	 * @return void
	 */
	public function deleteForModel ( $model, $field = null ) 
	{
		$tableName = \nn\t3::Obj()->getTableName( $model );
		$modelUid = $model->getUid();
		if (!$tableName || !$modelUid) return;

		$fileReferences = \nn\t3::Db()->findByValues('sys_file_reference', [
			'tablenames' 	=> $tableName,
			'uid_foreign'	=> $modelUid,
		]);

		$props = \nn\t3::Obj()->getProps( $model );

		if ($fileReferences) {
			try {
				$resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
				foreach ($fileReferences as $row) {
					$fieldname = $row['fieldname'];
					if ($field && $fieldname !== $field) {
						continue;
					}
					if ($file = $resourceFactory->getFileObject( $row['uid_local'] )) {
						\nn\t3::Fal()->clearCache( $file );
						$file->delete();
						\nn\t3::Obj()->set( $model, $fieldname, null );
					}
				}
			} catch( \Exception $e ) {}
		}
	}

	/**
	 * Erzeugt ein FileRefence Objekt (Tabelle: `sys_file_reference`) und verknüpft es mit einem Datensatz.
	 * Beispiel: Hochgeladenes JPG soll als FAL an tt_news-Datensatz angehängt werden
	 *
	 * __Parameter:__
	 * 
	 * | key | Beschreibung |
	 * | --- | --- |
	 * | `src` 			| Pfad zur Quelldatei (kann auch http-Link zu YouTube-Video sein)
	 * | `dest`			| Pfad zum Zielordner (optional, falls Datei verschoben/kopiert werden soll)
	 * | `table`		| Ziel-Tabelle, dem die FileReference zugeordnet werden soll (z.B. `tx_myext_domain_model_entry`)
	 * | `title`		| Titel
	 * | `description`  | Beschreibung
	 * | `link`			| Link
	 * | `crop`			| Beschnitt
	 * | `table`		| Ziel-Tabelle, dem die FileReference zugeordnet werden soll (z.B. `tx_myext_domain_model_entry`)
	 * | `sorting`		| (int) Sortierung
	 * | `field`		| Column-Name der Ziel-Tabelle, dem die FileReference zugeordnet werden soll (z.B. `image`)
	 * | `uid`			| (int) uid des Datensatzes in der Zieltabelle (`tx_myext_domain_model_entry.uid`)
	 * | `pid`			| (int) pid des Datensatzes in der Zieltabelle 
	 * | `cruser_id`	| cruser_id des Datensatzes in der Zieltabelle
	 * | `copy`			| src-Datei nicht verschieben sondern kopieren (default: `true`)
	 * | `forceNew`		| Im Zielordner neue Datei erzwingen (sonst wird geprüft, ob bereits Datei existiert) default: `false`
	 * | `single`		| Sicherstellen, dass gleiche FileReferenz nur 1x pro Datensatz verknüpft wird (default: `true`)
	 * 
	 * __Beispiel:__
	 * ```
	 * $fal = \nn\t3::Fal()->fromFile([
	 * 	'src'			=> 'fileadmin/test/bild.jpg',
	 * 	'dest' 			=> 'fileadmin/test/fal/',
	 * 	'pid'			=> 132, 
	 * 	'uid'			=> 5052, 
	 * 	'table'			=> 'tx_myext_domain_model_entry', 
	 * 	'field'			=> 'fallistimage'
	 * ]);
	 * ```
	 *
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */ 	
	public function fromFile ( $params = [] ) {
		
		$params = \nn\t3::Arrays([
			'dest'		=> '',
			'forceNew'	=> false,
			'copy'		=> true,
			'single'	=> true,
		])->merge( $params );

		$fileReferenceRepository = \nn\t3::injectClass( FileReferenceRepository::class );

		$newFile = $this->createFalFile( $params['dest'], $params['src'], $params['copy'], $params['forceNew'] );
		if (!$newFile) return false;

		if ($params['single']) {
			if ($fileReferenceExists = $this->fileReferenceExists( $newFile, $params )) {
				return $fileReferenceExists;
			}
		}

		$fieldname = GeneralUtility::camelCaseToLowerCaseUnderscored($params['field']);

		$entry = [
			'fieldname' 		=> $fieldname,
			'tablenames' 		=> $params['table'],
			'table_local' 		=> 'sys_file',
			'uid_local' 		=> $newFile->getUid(),
			'uid_foreign' 		=> 0,
			'sorting_foreign' 	=> $params['sorting_foreign'] ?? $params['sorting'] ?? time(),
			'pid' 				=> $params['pid'] ?? 0,
			'description' 		=> $params['description'] ?? null,
			'title' 			=> $params['title'] ?? null,
			'link' 				=> $params['link'] ?? '',
			'crop' 				=> $params['crop'] ?? '',
			'tstamp'			=> time(),
			'crdate'			=> time(),
		];

		if ($uid = $params['uid'] ?? null) {
			$entry['uid_foreign'] = $uid;
		}

		$entry = \nn\t3::Db()->insert('sys_file_reference', $entry);

		// @returns \TYPO3\CMS\Extbase\Domain\Model\FileReference
		$persistenceManager = \nn\t3::injectClass(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
		return $persistenceManager->getObjectByIdentifier($entry['uid'], \TYPO3\CMS\Extbase\Domain\Model\FileReference::class, false);

	}
	
	/**
	 * Erzeugt eine sys_file_reference aus einem sys_file
	 * ```
	 * $sysFileRef = \nn\t3::Fal()->fromFalFile( $sysFile );
	 * ```
	 * @param \TYPO3\CMS\Core\Resource\File $sysFile
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	public function fromFalFile( $sysFile = null ) 
	{
		$fal = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
		$falFile = new \TYPO3\CMS\Core\Resource\FileReference([
			'uid_local' => $sysFile->getUid(),
			'uid_foreign' => 0,
			'tablenames' => '',
			'uid' => null,
		]);
		$fal->setOriginalResource( $falFile );
		return $fal;
	}

	/**
	 *	Erzeugt ein \File (FAL) Object (sys_file)
	 *	
	 *	\nn\t3::Fal()->createFalFile( $storageConfig, $srcFile, $keepSrcFile, $forceCreateNew );
	 *
	 *	@param string $storageConfig	Pfad/Ordner, in die FAL-Datei gespeichert werden soll (z.B. 'fileadmin/projektdaten/')
	 *	@param string $srcFile			Quelldatei, die in FAL umgewandelt werden soll  (z.B. 'uploads/tx_nnfesubmit/beispiel.jpg')
	 *									Kann auch URL zu YouTube/Vimeo-Video sein (z.B. https://www.youtube.com/watch?v=7Bb5jXhwnRY)
	 *	@param boolean $keepSrcFile		Quelldatei nur kopieren, nicht verschieben?
	 *	@param boolean $forceCreateNew	Soll immer neue Datei erzeugt werden? Falls nicht, gibt er ggf. bereits existierendes File-Object zurück
	 *
	 *	@return \Nng\Nnhelpers\Domain\Model\File|\TYPO3\CMS\Core\Resource\File|boolean
	 */
	public function createFalFile ( $storageConfig, $srcFile, $keepSrcFile = false, $forceCreateNew = false ) {
		
		$fileRepository = \nn\t3::injectClass( FileRepository::class );

		$isExternalMedia = strpos( $srcFile, 'http://') !== false || strpos( $srcFile, 'https://') !== false;

		if (!$storageConfig) {
			$storageConfig = $isExternalMedia ? 'fileadmin/videos/' : $srcFile;
		}

		// Absoluter Pfad zur Quell-Datei ('/var/www/website/uploads/bild.jpg')
		$absSrcFile = \nn\t3::File()->absPath( $srcFile );

		// Keine externe URL (YouTube...) und Datei existiert nicht? Dann abbrechen!
		if (!$isExternalMedia && !\nn\t3::File()->exists($srcFile)) {
			return false;
		}
		
		// Object, Storage-Model für Zielverzeichnis (z.B. Object für 'fileadmin/' wenn $storageConfig = 'fileadmin/test/was/')
		$storage = \nn\t3::File()->getStorage($storageConfig, true);
		
		// Object, relativer Unterordner innerhalb der Storage, (z.B. Object für 'test/was/' wenn $storageConfig = 'fileadmin/test/was/')
		$subfolderInStorage = \nn\t3::Storage()->getFolder($storageConfig, $storage);

		// String, absoluter Pfad zum Zielverzeichnis
		$absDestFolderPath = \nn\t3::File()->absPath( $subfolderInStorage );

		// Dateiname, ohne Pfad ('fileadmin/test/bild.jpg' => 'bild.jpg')
		$srcFileBaseName = basename($srcFile);

		if (!$forceCreateNew && $storage->hasFileInFolder( $srcFileBaseName, $subfolderInStorage )) {
			$existingFile = $storage->getFileInFolder( $srcFileBaseName, $subfolderInStorage );
			
			// @returns \TYPO3\CMS\Core\Resource\File
			return $existingFile;
		}

		if ($isExternalMedia) {

			// YouTube und Vimeo-Videos: Physische lokale .youtube/.vimeo-Datei anlegen
			$helper = \nn\t3::injectClass( OnlineMediaHelperRegistry::class );

			// \TYPO3\CMS\Core\Resource\File
			$newFileObject = $helper->transformUrlToFile( $srcFile, $subfolderInStorage );

		} else {
			// "Normale" Datei: Datei in Ordner kopieren und FAL erstellen

			// Name der Datei im Zielverzeichnis
			$absTmpName = $absDestFolderPath . $srcFileBaseName;

			// Kopieren
			if ($forceCreateNew) {
				$success = \nn\t3::File()->copy( $absSrcFile, $absTmpName, $forceCreateNew );
				$absTmpName = $success;
			} else {
				if ($keepSrcFile) {
					$success = \nn\t3::File()->copy( $absSrcFile, $absTmpName );
					$absTmpName = $success;
				} else {
					$success = \nn\t3::File()->move( $absSrcFile, $absTmpName );
				}
			}

			if (!$success) return false;
			
			// Nutze die File-Indexer-Funktion, um die temporäre Datei in der Tabelle sys_file einzufügen
			$this->clearCache($absTmpName);

			// String, relativer Pfad der Datei innerhalb der Storage. Ermittelt selbstständig die passende Storage ()
			$relPathInStorage = \nn\t3::File()->getRelativePathInStorage( $absTmpName );

			// File-Object für tmp-Datei holen
			$tmpFileObject = $storage->getFile($relPathInStorage);
			
			if (!$tmpFileObject) return false;

			// $newFileObject = $tmpFileObject->moveTo($subfolderInStorage, $srcFileBaseName, DuplicationBehavior::RENAME);
			$newFileObject = $tmpFileObject;
		}

		if (!$newFileObject) return false;

		// Exif-Daten für Datei ermitteln
		if ($exif = \nn\t3::File()->getExifData( $srcFile )) {
			\nn\t3::Db()->update('sys_file', ['exif'=>json_encode($exif)], $newFileObject->getUid());
		}
		
		// @returns \TYPO3\CMS\Core\Resource\File
		return $newFileObject;
	}
	
	
	/**
	 * Holt ein \File (FAL) Object (`sys_file`)
	 * ```
	 * \nn\t3::Fal()->getFalFile( 'fileadmin/image.jpg' );
	 * ```
	 * @param string $srcFile
	 * @return \TYPO3\CMS\Core\Resource\File|boolean
	 */
	public function getFalFile ( $srcFile ) 
	{
		try {
			$srcFile = \nn\t3::File()->stripPathSite( $srcFile );
			$storage = \nn\t3::File()->getStorage( $srcFile, true );	
			if (!$storage) return false;

			// \TYPO3\CMS\Core\Resource\File
			$storageBasePath = $storage->getConfiguration()['basePath'];

			$file = $storage->getFile( substr( $srcFile, strlen($storageBasePath) ) );
			return $file;

		} catch( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Holt / konvertiert in ein \TYPO3\CMS\Core\Resource\FileReference Object (sys_file_reference)
	 * "Smarte" Variante zu `\TYPO3\CMS\Extbase\Service\ImageService->getImage()`
	 * ```
	 * \nn\t3::Fal()->getImage( 1 );
	 * \nn\t3::Fal()->getImage( 'pfad/zum/bild.jpg' );
	 * \nn\t3::Fal()->getImage( $fileReference );
	 * ```
	 * @param string|\TYPO3\CMS\Extbase\Domain\Model\FileReference $src
	 * @return \TYPO3\CMS\Core\Resource\FileReference|boolean
	 */
	public function getImage ( $src = null ) {
		if (!$src) return null;
		$imageService = \nn\t3::injectClass( ImageService::class );
		$treatIdAsReference = is_numeric($src);
		if (is_string($src) || $treatIdAsReference) {
			return $imageService->getImage( $src, null, $treatIdAsReference );
		}
		return $imageService->getImage( '', $src, false );
	}

	/**
	 * Holt ein SysFile aus der CombinedIdentifier-Schreibweise ('1:/uploads/beispiel.txt').
	 * Falls Datei nicht exisitert wird FALSE zurückgegeben.
	 * ```
	 * \nn\t3::Fal()->getFileObjectFromCombinedIdentifier( '1:/uploads/beispiel.txt' );
	 * ```
	 * @param string $file		Combined Identifier ('1:/uploads/beispiel.txt')
	 * @return File|boolean
	 */	
	public function getFileObjectFromCombinedIdentifier( $file = '' ) {	
		$resourceFactory = GeneralUtility::makeInstance( ResourceFactory::class );
		$storage = $resourceFactory->getStorageObjectFromCombinedIdentifier( $file );
		$parts = \nn\t3::Arrays($file)->trimExplode(':');
		if ($storage->hasFile($parts[1])) return $resourceFactory->getFileObjectFromCombinedIdentifier( $file );
		return false;
	}
	
	/**
	 * Prüft, ob für einen Datensatz bereits eine SysFileReference zum gleichen SysFile exisitert
	 * ```
	 * \nn\t3::Fal()->fileReferenceExists( $sysFile, ['uid_foreign'=>123, 'tablenames'=>'tt_content', 'field'=>'media'] );
	 * ```
	 * @param $sysFile
	 * @param array $params => uid_foreign, tablenames, fieldname
	 * @return FileReference|false
	 */	
	public function fileReferenceExists( $sysFile = null, $params = [] ) {
		$where = [
			'uid_local' 	=> $sysFile->getUid(),
			'uid_foreign' 	=> $params['uid'] ?? '',
			'tablenames' 	=> $params['table'],
			'fieldname' 	=> GeneralUtility::camelCaseToLowerCaseUnderscored($params['field']),
		];

		$ref = \nn\t3::Db()->findByValues( 'sys_file_reference', $where );
		if (!$ref) return [];

		// @returns \TYPO3\CMS\Extbase\Domain\Model\FileReference
		return $this->persistenceManager->getObjectByIdentifier($ref[0]['uid'], \TYPO3\CMS\Extbase\Domain\Model\FileReference::class, false);
	}
	
	/**
	 * Holt eine SysFileReference anhand der uid
	 * Alias zu `\nn\t3::Convert( $uid )->toFileReference()`;
	 * ```
	 * \nn\t3::Fal()->getFileReferenceByUid( 123 );
	 * ```
	 * @param $uid
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */	
	public function getFileReferenceByUid( $uid = null ) {
		return \nn\t3::Convert( $uid )->toFileReference();
	}

	/**
	 * Löscht eine SysFileReference.
	 * Siehe auch `\nn\t3::Fal()->detach( $model, $field );` zum Löschen aus einem Model. 
	 * ```
	 * \nn\t3::Fal()->deleteSysFileReference( 112 );
	 * \nn\t3::Fal()->deleteSysFileReference( \TYPO3\CMS\Extbase\Domain\Model\FileReference );
	 * ```
	 * @param $uidOrFileReference
	 *
	 * @return mixed
	 */	
	public function deleteSysFileReference( $uidOrFileReference = null ) {

		$uid = null;

		if (is_a($uidOrFileReference, \TYPO3\CMS\Extbase\Domain\Model\FileReference::class )) {
			$uid = $uidOrFileReference->getUid();
		} else if (is_numeric($uidOrFileReference)) {
			$uid = $uidOrFileReference;
		}

		if ($uid) {
			
			// ToDo: Ab Typo3 v10 prüfen, ob delete() implementiert wurde
			/*
			$resourceFactory = \nn\t3::injectClass( \TYPO3\CMS\Core\Resource\ResourceFactory::class );
			$fileReferenceObject = $resourceFactory->getFileReferenceObject( $uid );
			$fileReferenceObject->delete();
			*/

			// ToDo: Ab Typo3 v8 prüfen, ob das hier nicht einfacher wäre:
			/*
			$fal = $this->persistenceManager->getObjectByIdentifier($uid, \TYPO3\CMS\Extbase\Domain\Model\FileReference::class, false);
			$this->persistenceManager->remove( $fal );
			*/
			
			\nn\t3::Db()->delete('sys_file_reference', $uid);
		}
	}


	/**
	 * Löscht alle physischen Thumbnail-Dateien, die für ein Bild generiert wurden inkl. 
	 * der Datensätze in der Tabelle `sys_file_processedfile`.
	 * 
	 * Das Ursprungsbild, das als Argument `$path` übergeben wurde, wird dabei nicht gelöscht.
	 * Das Ganze erzwingt das Neugenerieren der Thumbnails für ein Bild, falls sich z.B. das
	 * Quellbild geändert hat aber der Dateiname gleich geblieben ist.
	 * 
	 * Weiterer Anwendungsfall: Dateien auf dem Server bereinigen, weil z.B. sensible, personenbezogene
	 * Daten gelöscht werden sollen inkl. aller generierten Thumbnails.
	 * 
	 * ```
	 * \nn\t3::Fal()->deleteProcessedImages( 'fileadmin/pfad/beispiel.jpg' );
	 * \nn\t3::Fal()->deleteProcessedImages( $sysFileReference );
	 * \nn\t3::Fal()->deleteProcessedImages( $sysFile );
	 * ```
	 * @return mixed
	 */
	public function deleteProcessedImages( $sysFile = '' ) {

		if (is_string($sysFile)) {
			$sysFile = $this->getFalFile( $sysFile );
		} else if (is_a($sysFile, \TYPO3\CMS\Extbase\Domain\Model\FileReference::class, true)) {
			$sysFile = $sysFile->getOriginalResource()->getOriginalFile();
		}
		
		if (!$sysFile) return;

		if ($sysFileUid = $sysFile->getUid()) {
			$rows = \nn\t3::Db()->findByValues('sys_file_processedfile', ['original'=>$sysFileUid]);
			foreach ($rows as $row) {
				\nn\t3::File()->unlink("{$row['storage']}:{$row['identifier']}");
			}
			\nn\t3::Db()->delete('sys_file_processedfile', ['original'=>$sysFileUid]);
		}
	}

	/**
	 * Löscht ein SysFile (Datensatz aus Tabelle `sys_file`) und alle dazugehörigen SysFileReferences.
	 * Eine radikale Art, um ein Bild komplett aus der Indizierung von Typo3 zu nehmen.
	 *
	 * Die physische Datei wird nicht vom Server gelöscht!
	 * Siehe `\nn\t3::File()->unlink()` zum Löschen der physischen Datei.
	 * Siehe `\nn\t3::Fal()->detach( $model, $field );` zum Löschen aus einem Model.
	 * ```
	 * \nn\t3::Fal()->deleteSysFile( 1201 );
	 * \nn\t3::Fal()->deleteSysFile( 'fileadmin/pfad/zum/bild.jpg' );
	 * \nn\t3::Fal()->deleteSysFile( \TYPO3\CMS\Core\Resource\File );
	 * \nn\t3::Fal()->deleteSysFile( \TYPO3\CMS\Core\Resource\FileReference );
	 * ```
	 * 	@param $uidOrObject
	 *
	 * 	@return integer
	 */	
	public function deleteSysFile( $uidOrObject = null ) {

		$resourceFactory = \nn\t3::injectClass( \TYPO3\CMS\Core\Resource\ResourceFactory::class );
		if (!$uidOrObject) return false;

		if (is_string($uidOrObject) && !is_numeric($uidOrObject)) {
			// Pfad wurde übergeben
			$uidOrObject = \nn\t3::File()->relPath( $uidOrObject );
			$storage = \nn\t3::File()->getStorage($uidOrObject, false);
			if (!$storage) return false;
			$basePath = $storage->getConfiguration()['basePath'];
			$filepathInStorage = substr( $uidOrObject, strlen($basePath) );
			$identifier = '/'.ltrim($filepathInStorage, '/');
			$entry = \nn\t3::Db()->findOneByValues('sys_file', [
				'storage' => $storage->getUid(),
				'identifier' => $identifier,
			]);
			if ($entry) {
				$uid = $entry['uid'];
				$uidOrObject = $uid;
			}
		}

		if (is_a($uidOrObject, \TYPO3\CMS\Extbase\Domain\Model\FileReference::class )) {
			/* \TYPO3\CMS\Core\Resource\FileReference */
			$uid = $uidOrObject->getUid();
			$fileReferenceObject = $resourceFactory->getFileReferenceObject( $uid );
			$fileReferenceObject->getOriginalFile()->delete();
		} else if (is_a($uidOrObject, \TYPO3\CMS\Core\Resource\File::class )) {
			/* \TYPO3\CMS\Core\Resource\File */
			$uid = $uidOrObject->getUid();
			$uidOrObject->delete();
		} else if (is_numeric($uidOrObject)) {
			// uid wurde übergeben
			$uid = $uidOrObject;
			\nn\t3::Db()->delete('sys_file', $uidOrObject);
		}

		if ($uid) {
			// Zugehörge Datensätze aus `sys_file_references` löschen
			\nn\t3::Db()->delete('sys_file_reference', ['uid_local' => $uid], true);
		}

		return $uid;
	}

	/**
	 * Löscht ein SysFile und alle dazugehörigen SysFileReferences.
	 * Alias zu `\nn\t3::Fal()->deleteSysFile()`
	 * 
	 * @return integer
	 */
	public function unlink( $uidOrObject = null ) {
		return $this->deleteSysFile( $uidOrObject );
	}

	/**
	 * Erstellt neuen Eintrag in `sys_file`
	 * Sucht in allen `sys_file_storage`-Einträgen, ob der Pfad zum $file bereits als Storage existiert.
	 * Falls nicht, wird ein neuer Storage angelegt.
	 * ```
	 * \nn\t3::Fal()->createSysFile( 'fileadmin/bild.jpg' );
	 * \nn\t3::Fal()->createSysFile( '/var/www/mysite/fileadmin/bild.jpg' );
	 * ```
	 * @return false|\TYPO3\CMS\Core\Resource\File
	 */
	public function createSysFile ( $file, $autoCreateStorage = true ) {
	
		$file = \nn\t3::File()->stripPathSite( $file );

		$storage = \nn\t3::File()->getStorage( $file, $autoCreateStorage );
		if (!$storage) return false;

		$fileRepository = \nn\t3::injectClass( FileRepository::class );
		
		$storageConfiguration = $storage->getConfiguration();
		$storageFolder = $storageConfiguration['basePath'];
		$basename = substr( $file, strlen($storageFolder) );
		
		$sysFile = $storage->getFile($basename);

		// @return \TYPO3\CMS\Core\Resource\File
		$file = GeneralUtility::makeInstance(ResourceFactory::class)->getFileObject($sysFile->getUid());
		return $file;
	}


	/**
	 * Löscht den Cache für die Bildgrößen eines FAL inkl. der umgerechneten Bilder
	 * Wird z.B. der f:image-ViewHelper verwendet, werden alle berechneten Bildgrößen
	 * in der Tabelle sys_file_processedfile gespeichert. Ändert sich das Originalbild,
	 * wird evtl. noch auf ein Bild aus dem Cache zugegriffen.
	 * ```
	 * \nn\t3::Fal()->clearCache( 'fileadmin/file.jpg' );
	 * \nn\t3::Fal()->clearCache( $fileReference );
	 * \nn\t3::Fal()->clearCache( $falFile );
	 * ```
	 * @param $filenameOrSysFile 	FAL oder Pfad (String) zu der Datei
	 * @return void
	 */
	public function clearCache ( $filenameOrSysFile = '' ) 
	{
		if (is_string($filenameOrSysFile)) {
			if ($falFile = $this->getFalFile( $filenameOrSysFile )) {
				$filenameOrSysFile = $falFile;
			}
		}
		$processedFileRepository = \nn\t3::injectClass( ProcessedFileRepository::class );
		if (is_string($filenameOrSysFile)) return;

		if (is_a($filenameOrSysFile, \TYPO3\CMS\Extbase\Domain\Model\File::class)) {
			$filenameOrSysFile = $filenameOrSysFile->getOriginalResource();
		}
		
		if ($processedFiles = $processedFileRepository->findAllByOriginalFile( $filenameOrSysFile )) {
			foreach ($processedFiles as $file) {
				$file->delete( true );
				\nn\t3::Db()->delete('sys_file_processedfile', $file->getUid());
			}
		}
	}

	/**
	 * Update der Angaben in `sys_file_metadata` und `sys_file`
	 * ```
	 * \nn\t3::Fal()->updateMetaData( 'fileadmin/file.jpg' );
	 * \nn\t3::Fal()->updateMetaData( $fileReference );
	 * \nn\t3::Fal()->updateMetaData( $falFile );
	 * ```
	 * @param $filenameOrSysFile 	FAL oder Pfad (String) zu der Datei
	 * @param $data 				Array mit Daten, die geupdated werden sollen.
	 *								Falls leer, werden Bilddaten automatisch gelesen
	 * @return void
	 */
	public function updateMetaData ( $filenameOrSysFile = '', $data = [] ) {

		if (is_string($filenameOrSysFile)) {
			if ($falFile = $this->getFalFile( $filenameOrSysFile )) {
				$filenameOrSysFile = $falFile;
			}
		}
		if (!$data) {
			$data = \nn\t3::File()->getData( $filenameOrSysFile );
		}

		$storage = \nn\t3::File()->getStorage( $filenameOrSysFile );
		$publicUrl = \nn\t3::File()->getPublicUrl( $filenameOrSysFile );
		$destinationFile = GeneralUtility::makeInstance( ResourceFactory::class )->retrieveFileOrFolderObject($publicUrl);
		$indexer = GeneralUtility::makeInstance(Indexer::class, $storage);
		$indexer->updateIndexEntry($destinationFile);
	}


	/**
	 * Ersetzt eine `FileReference` oder `ObjectStorage` in einem Model mit Bildern.
	 * Typischer Anwendungsfall: Ein FAL-Bild soll über ein Upload-Formular im Frontend geändert
	 * werden können.
	 *  
	 * Für jedes Bild wird geprüft, ob bereits eine `FileReference` im Model existiert.
	 * Bestehende FileReferences werden __nicht__ überschrieben, sonst würden evtl. 
	 * Bildunterschriften oder Cropping-Anweisungen verloren gehen!
	 * 
	 * __Achtung!__ Das Model wird automatisch persistiert!
	 * ```
	 * $newModel = new \My\Extension\Domain\Model\Example();
	 * \nn\t3::Fal()->setInModel( $newModel, 'falslideshow', 'path/to/file.jpg' );
	 * echo $newModel->getUid(); // Model wurde persistiert!
	 * ```
	 * 
	 * __Beispiel mit einer einfachen FileReference im Model:__
	 * ```
	 * $imageToSet = 'fileadmin/bilder/portrait.jpg';
	 * \nn\t3::Fal()->setInModel( $member, 'falprofileimage', $imageToSet );
	 *
	 * \nn\t3::Fal()->setInModel( $member, 'falprofileimage', ['publicUrl'=>'01.jpg', 'title'=>'Titel', 'description'=>'...'] );
	 * ```
	 * __Beispiel mit einem einzelnen SysFile:__
	 * ```
	 * $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
	 * $file = $resourceFactory->getFileObjectFromCombinedIdentifier('1:/foo.jpg');
	 * \nn\t3::Fal()->setInModel( $member, 'image', $file );
	 * ```
	 * __Beispiel mit einem einzelnen SysFile, das in eine ObjectStorage soll:__
	 * ```
	 * $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
	 * $file = $resourceFactory->getFileObjectFromCombinedIdentifier('1:/foo.jpg');
	 * \nn\t3::Fal()->setInModel( $member, 'images', [$file] );
	 * ```
	 * __Beispiel mit einem ObjectStorage im Model:__
	 * ```
	 * $imagesToSet = ['fileadmin/bilder/01.jpg', 'fileadmin/bilder/02.jpg', ...];
	 * \nn\t3::Fal()->setInModel( $member, 'falslideshow', $imagesToSet );
	 *
	 * \nn\t3::Fal()->setInModel( $member, 'falslideshow', [['publicUrl'=>'01.jpg'], ['publicUrl'=>'02.jpg']] );

	 * \nn\t3::Fal()->setInModel( $member, 'falvideos', [['publicUrl'=>'https://youtube.com/?watch=zagd61231'], ...] );
	 * ```
	 * __Beispiel mit Videos:__
	 * ```
	 * $videosToSet = ['https://www.youtube.com/watch?v=GwlU_wsT20Q', ...];
	 * \nn\t3::Fal()->setInModel( $member, 'videos', $videosToSet );
	 * ```
	 * 
	 * @param mixed $model				Das Model, das geändert werden soll
	 * @param string $fieldName			Property (Feldname) der ObjectStorage oder FileReference
	 * @param mixed $imagesToAdd		String / Array mit Bildern 
	 * 
	 * @return mixed
	 */
	public function setInModel( $model, $fieldName = '', $imagesToAdd = [] ) {

		if (!$model) \nn\t3::Exception( 'Parameter $model is not a Model' );

		$objHelper = \nn\t3::Obj();
		$repository = \nn\t3::Db()->getRepositoryForModel( $model );

		// Sicher gehen, dass das Model bereits persistiert wurde – ohne uid keine FileReference!
		if (!$model->getUid()) {
			if ($repository) {
				$repository->add( $model );
			}
			\nn\t3::Db()->persistAll();
		}

		$modelUid = $model->getUid();
		if (!$modelUid) return false;

		// Der passende Tabellen-Name in der DB zum Model
		$modelTableName = $objHelper->getTableName( $model );

		// Aktuellen Wert auslesen und ermitteln, ob das Feld eine FileReference oder ObjectStorage ist
		$props = $objHelper->getProps( $model );

		$fieldValue = $objHelper->prop( $model, $fieldName );
		$isObjectStorage = is_a( $props[$fieldName], ObjectStorage::class, true );
		$isSysFileReference = is_a($props[$fieldName], SysFileReference::class, true );

		if ($isObjectStorage && !$fieldValue) {
			$objHelper->set( $model, $fieldName, new ObjectStorage() );
		}

		// Array der bereits bestehenden FileReferences erzeugen mit Pfad zu Bildern als Key
		$existingFileReferencesByPublicUrl = [];
		if ($fieldValue) {
			if (!$isObjectStorage) {
				$fieldValue = [$fieldValue];
			}
			foreach ($fieldValue as $sysFileRef) {
				$publicUrl = $sysFileRef->getOriginalResource()->getPublicUrl();
				$existingFileReferencesByPublicUrl[$publicUrl] = $sysFileRef;
			}
		}

		// Normalisieren der Pfadangaben zu den Bildern. 
		// Aus 'pfad/zum/bild.jpg' wird ['publicUrl'=>'pfad/zum/bild.jpg']
		if (is_string($imagesToAdd)) {
			$imagesToAdd = ['publicUrl'=>$imagesToAdd];
		}

		// Grundsätzlich mit Arrays arbeiten, vereinfacht die Logik unten. 
		// Aus ['publicUrl'=>'pfad/zum/bild.jpg'] wird [['publicUrl'=>'pfad/zum/bild.jpg']]
		if (is_array($imagesToAdd) && isset($imagesToAdd['publicUrl'])) {
			$imagesToAdd = [$imagesToAdd];
		}

		// sysFiles und sysFileReferences erlauben
		if ($objHelper->isFalFile($imagesToAdd) || $objHelper->isFile($imagesToAdd)) {
			$imagesToAdd = [$imagesToAdd];
		}

		// Aus ['01.jpg', '02.jpg', ...] wird [['publicUrl'=>'01.jpg'], ['publicUrl'=>'02.jpg'], ...]
		foreach ($imagesToAdd as $k=>$v) {
			if (is_string($v)) {
				$imagesToAdd[$k] = ['publicUrl'=>$v];
			}
		}

		// Durch die Liste der neuen Bilder gehen ...
		foreach ($imagesToAdd as $n=>$imgObj) {

			// Bereits ein falFile oder eine sysFileReference
			if ($objHelper->isFile($imgObj) || $objHelper->isFalFile($imgObj)) {
				continue;
			}

			$imgToAdd = $imgObj['publicUrl'];
			$publicUrl = \nn\t3::File()->stripPathSite( $imgToAdd );

			// Falls bereits eine FileReference zu dem gleichen Bild existiert, diese verwenden
			$value = $existingFileReferencesByPublicUrl[$publicUrl] ?: $publicUrl;

			// Falls das Bild noch nicht im Model existierte, eine neue FileReference erzeugen
			if (is_string($value)) {
				$falParams = [
					'src'           => $value,
					'title'			=> $imgObj['title'] ?? '',
					'description'	=> $imgObj['description'] ?? '',
					'link'			=> $imgObj['link'] ?? '',
					'crop'			=> $imgObj['crop'] ?? '',
					'pid'           => $model->getPid(),
					'uid'           => $model->getUid(),
					'table'         => $modelTableName,
					'field'         => $fieldName,
				];
				$value = $this->fromFile( $falParams );die('OK');
			}

			// Sollte etwas schief gegangen sein, ist $value == FALSE
			if ($value) {
				$imagesToAdd[$n] = $value;
			}
		}
		
		if ($isSysFileReference) {

			// Feld ist eine SysFileReference (ohne ObjectStorage)
			$objectToSet = $imagesToAdd[0] ?? false;

		} else if ($isObjectStorage) {

			// Feld ist eine ObjectStorage: Neue ObjectStorage zum Ersetzen der bisherigen erzeugen
			$objectToSet = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
			foreach ($imagesToAdd as $n=>$imgToAdd) {
				if ($objHelper->isFile( $imgToAdd )) {
					$imgToAdd = \nn\t3::Fal()->fromFalFile( $imgToAdd );
				}
				$objectToSet->attach( $imgToAdd );
			}

		} else {

			// Feld ist keine ObjectStorage: Also einfach die erste FileReference verwenden. 
			$objectToSet = array_shift($imagesToAdd);
			
			if (!$objectToSet && $existingFileReferencesByPublicUrl) {
				// FileReference soll entfernt werden
				foreach ($existingFileReferencesByPublicUrl as $sysFileRef) {
					$this->deleteSysFileReference( $sysFileRef );
				}
			}			
		}

		// Property im Model aktualisieren
		if ($objectToSet) {
			$objHelper->set( $model, $fieldName, $objectToSet );
		}

		// Model aktualisieren
		if ($repository) {
			$repository->update( $model );
			\nn\t3::Db()->update( $model );
		}

		return $model;
	}


	/**
	 * Die URL zu einer FileReference oder einem FalFile holen.
	 * Alias zu `\nn\t3::File()->getPublicUrl()`.
	 * ```
	 * \nn\t3::Fal()->getFilePath( $fileReference );	// ergibt z.B. 'fileadmin/bilder/01.jpg'
	 * ```
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference|\TYPO3\CMS\Core\Resource\FileReference $falReference
	 * @return string
	 */
	public function getFilePath($falReference) {
		return \nn\t3::File()->getPublicUrl( $falReference );
	}

	/**
	 * Berechnet ein Bild über `maxWidth`, `maxHeight`, `cropVariant` etc.
	 * Gibt URI zum Bild als String zurück. Hilfreich bei der Berechnung von Thumbnails im Backend.
	 * Alias zu `\nn\t3::File()->process()`
	 * ```
	 * \nn\t3::File()->process( 'fileadmin/bilder/portrait.jpg', ['maxWidth'=>200] );
	 * \nn\t3::File()->process( '1:/bilder/portrait.jpg', ['maxWidth'=>200] );
	 * \nn\t3::File()->process( $sysFile, ['maxWidth'=>200] );
	 * \nn\t3::File()->process( $sysFileReference, ['maxWidth'=>200, 'cropVariant'=>'square'] );
	 * ```
	 * @return string
	 */
	public function process ( $fileObj = '', $processing = [] ) {
		return \nn\t3::File()->process( $fileObj, $processing );
	}


	/**
	 * Eine FileReference in ein Array konvertieren.
	 * Enthält publicUrl, title, alternative, crop etc. der FileReference.
	 * Alias zu `\nn\t3::Obj()->toArray( $fileReference );`
	 * ```
	 * \nn\t3::Fal()->toArray( $fileReference );	// ergibt ['publicUrl'=>'fileadmin/...', 'title'=>'...']
	 * ```
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $falReference
	 * @return array
	 */
	public function toArray(\TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference = NULL) {
		return \nn\t3::Obj()->toArray( $fileReference );
	}

}