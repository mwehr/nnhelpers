<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Konvertieren von Arrays zu Models, Models zu JSONs, Arrays zu ObjectStorages,
 * Hex-Farben zu RGB und vieles mehr, was irgendwie mit Konvertieren von Dingen
 * zu tun hat.
 */
class Convert implements SingletonInterface {

	/**
	 * 	@var mixed
	 */
	protected $initialArgument;

	/**
	 * 	Klasse konstruieren.
	 */
	public function __construct ( $obj = null ) {
		$this->initialArgument = $obj;
		return $this;
	}

	/**
	 * Konvertiert ein Model in ein Array
	 * Alias zu \nn\t3::Obj()->toArray();
	 * 
	 * Bei Memory-Problemen wegen Rekursionen: Max-Tiefe angebenen!
	 * ```
	 * \nn\t3::Convert($model)->toArray(2);
	 * \nn\t3::Convert($model)->toArray();		=> ['uid'=>1, 'title'=>'Beispiel', ...]
	 * ```
	 * @return array
	 */
    public function toArray( $obj = null, $depth = 3 ) {
		if (is_int($obj)) $depth = $obj;
		$obj = $this->initialArgument !== null ? $this->initialArgument : $obj;
		return \nn\t3::Obj()->toArray($obj, $depth);
	}
	
	/**
	 * 	Konvertiert ein Model in ein JSON
	 *	```
	 *	\nn\t3::Convert($model)->toJson()		=> ['uid'=>1, 'title'=>'Beispiel', ...]
	 *	```
	 * 	@return array
	 */
    public function toJson( $obj = null, $depth = 3 ) {
		return json_encode( $this->toArray($obj, $depth) );
	}
	
	/**
	 * 	Konvertiert etwas in eine ObjectStorage
	 *	```
	 *	\nn\t3::Convert($something)->toObjectStorage()
	 *	\nn\t3::Convert($something)->toObjectStorage( \My\Child\Type::class )
	 *
	 *	\nn\t3::Convert()->toObjectStorage([['uid'=>1], ['uid'=>2], ...], \My\Child\Type::class )
	 *	\nn\t3::Convert()->toObjectStorage([1, 2, ...], \My\Child\Type::class )
	 *	```
	 * 	@return ObjectStorage
	 */
    public function toObjectStorage( $obj = null, $childType = null ) {

		$childType = $this->initialArgument !== null ? $obj : $childType;
		$persistenceManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
		$obj = $this->initialArgument !== null ? $this->initialArgument : $obj;

		$objectStorage = GeneralUtility::makeInstance( ObjectStorage::class );
		if ($childRepository = ($childType ? \nn\t3::Db()->getRepositoryForModel($childType) : false)) {
			\nn\t3::Db()->ignoreEnableFields($childRepository);
		}

		if (is_a($obj, QueryResultInterface::class) || is_array($obj)) {
			foreach($obj as $item) {				
				if (!$childType || is_a($item, $childType)) {
					$objectStorage->attach( $item );					
				} else {

					$uid = is_numeric($item) ? $item : \nn\t3::Obj()->get($item, 'uid');
					if ($uid) {
						if ($childType == \Nng\Nnhelpers\Domain\Model\FileReference::class) {
							$childType = \TYPO3\CMS\Extbase\Domain\Model\FileReference::class;
						}
						// @returns \TYPO3\CMS\Extbase\Domain\Model\FileReference
						$child = $persistenceManager->getObjectByIdentifier($uid, $childType, false);
						$objectStorage->attach( $child );
					} else {
						$child = GeneralUtility::makeInstance( $childType );
						$objectStorage->attach( $child );
					}
				}
			}
		}
		return $objectStorage;
	}
	
	/**
	 * 	Konvertiert ein Array in ein Model.
	 * 
	 * ```
	 * \nn\t3::Convert($array)->toModel( \Nng\Model\Name::class )		=> \Nng\Model\Name
	 * ```
	 * Kann auch automatisch FileReferences erzeugen.
	 * In diesem Bespiel wird ein neues Model des Typs `\Nng\Model\Name` erzeugt und
	 * danach in der Datenbank persistiert. Das Feld `falMedia` ist eine ObjectStorage
	 * mit `FileReferences`. Die FileReferences werden automatisch erzeugt!
	 * ```
	 * $data = [
	 * 	'pid' => 6,
	 * 	'title' => 'Neuer Datensatz',
	 * 	'description' => 'Der Text',
	 * 	'falMedia' => [
	 * 		['title'=>'Bild 1', 'publicUrl'=>'fileadmin/_tests/5e505e6b6143a.jpg'],
	 * 		['title'=>'Bild 2', 'publicUrl'=>'fileadmin/_tests/5e505fbf5d3dd.jpg'],
	 * 		['title'=>'Bild 3', 'publicUrl'=>'fileadmin/_tests/5e505f435061e.jpg'],
	 * 	]
	 * ];
	 * $newModel = \nn\t3::Convert( $data )->toModel( \Nng\Model\Name::class );
	 * $modelRepository->add( $newModel );
	 * \nn\t3::Db()->persistAll();
	 * ```
	 * 
	 * Beispiel: Aus einem Array einen News-Model erzeugen:
	 * ```
	 * $entry = [
	 * 	'pid' 			=> 12,
	 * 	'title'			=> 'News-Titel',
	 * 	'description'	=> '<p>Meine News</p>',
	 * 	'falMedia'		=> [['publicUrl' => 'fileadmin/bild.jpg', 'title'=>'Bild'], ...],
	 * 	'categories'	=> [1, 2]
	 * ];
	 * $model = \nn\t3::Convert( $entry )->toModel( \GeorgRinger\News\Domain\Model\News::class );
	 * $newsRepository->add( $model );
	 * \nn\t3::Db()->persistAll();
	 * ```
	 *
	 *	__Hinweis__
	 * 	Um ein bereits existierendes Model mit Daten aus einem Array zu aktualisieren gibt
	 *	es die Methode `$updatedModel = \nn\t3::Obj( $prevModel )->merge( $data );`
	 *
	 * 	@return mixed
	 */
    public function toModel( $className = null, $parentModel = null ) {

		$arr = $this->initialArgument;

		$model = GeneralUtility::makeInstance( $className );
		return \nn\t3::Obj($model)->merge( $arr );

		# ToDo: Prüfen, warum der DataMapper hier nicht funktioniert. Model wird nicht persistiert!
		# $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
		# return $dataMapper->map($model, [$arr]);
	}
	
	/**
	 * Konvertiert eine Liste in eine `ObjectStorage` mit `SysCategory`
	 * ```
	 * Noch nicht implementiert!
	 * ```
	 * @return ObjectStorage
	 */
	public function toSysCategories() {
		$input = $this->initialArgument;
	}

	/**
	 * 	Konvertiert ein `\TYPO3\CMS\Core\Resource\FileReference` (oder seine `uid`)
	 * 	in eine `\TYPO3\CMS\Extbase\Domain\Model\FileReference`
	 *	```
	 *	\nn\t3::Convert( $input )->toFileReference()	=> \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 *	```
	 *	@param $input	Kann `\TYPO3\CMS\Core\Resource\FileReference` oder `uid` davon sein
	 * 	@return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
    public function toFileReference() {
		$input = $this->initialArgument;

		if (is_a( $input, \TYPO3\CMS\Core\Resource\FileReference::class )) {
			$falFileReference = $input;
		} else if (is_numeric($input)) {
			$falFileRepository = GeneralUtility::makeInstance( \TYPO3\CMS\Core\Resource\FileRepository::class ); 
			$falFileReference = $falFileRepository->findFileReferenceByUid( $input );
		}
		
		$sysFileReference = GeneralUtility::makeInstance( \TYPO3\CMS\Extbase\Domain\Model\FileReference::class );
		$sysFileReference->setOriginalResource($falFileReference);

		return $sysFileReference;
	}

	/**
	 *	Konvertiert einen Farbwert in ein anderes Zahlenformat
	 *	```
	 *	\nn\t3::Convert('#ff6600')->toRGB();	// -> 255,128,0
	 *	```
	 *	@return string
	 */
	public function toRGB() {
		$input = $this->initialArgument;
		$isHex = substr($input, 0, 1) == '#' || strlen($input) == 6;
		if ($isHex) {
			$input = str_replace('#', '', $input);
			$rgb = sscanf($input, "%02x%02x%02x");
			return join(',', $rgb);
		}
		return '';
	}

	/**
	 * Konvertiert eine für Menschen lesbare Angabe von Bytes/Megabytes in einen Byte-Integer.
	 * Extrem tolerant, was Leerzeichen, Groß/Klein-Schreibung und Kommas statt Punkten angeht.
	 * ```
	 * \nn\t3::Convert('1M')->toBytes();	// -> 1048576
	 * \nn\t3::Convert('1 MB')->toBytes();	// -> 1048576
	 * \nn\t3::Convert('1kb')->toBytes();	// -> 1024
	 * \nn\t3::Convert('1,5kb')->toBytes();	// -> 1024
	 * \nn\t3::Convert('1.5Gb')->toBytes();	// -> 1610612736
	 * ```
	 * Für den umgekehrten Weg (Bytes zu menschenlesbarer Schreibweise wie 1024 -> 1kb) gibt
	 * es einen praktischen Fluid ViewHelper im Core:
	 * ```
	 * {fileSize->f:format.bytes()}
	 * ```
	 * @return integer
	 */
	public function toBytes() {
		$input = strtoupper($this->initialArgument);

		$units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		$input = str_replace(',', '.', $input);
		if (substr($input, -1) == 'M') $input .= 'B';

		$number = substr($input, 0, -2);
		$suffix = substr($input,-2);

		if(is_numeric(substr($suffix, 0, 1))) {
			return preg_replace('/[^\d]/', '', $input);
		}
		$exponent = array_flip($units)[$suffix] ?? null;
		if($exponent === null) {
			return null;
		}
		return $number * (1024 ** $exponent);
	}

	/**
	 * Konvertiert (normalisiert) einen String zu UTF-8
	 * ```
	 * \nn\t3::Convert('äöü')->toUTF8();
	 * ```
	 * @return string
	 */
	public function toUTF8() {
		$input = $this->initialArgument;
		$input = html_entity_decode($input);
		if (function_exists('iconv')) {
			$input = iconv('ISO-8859-1', 'UTF-8', $input);
		}
		return $input;
	}
	
	/**
	 * Konvertiert (normalisiert) einen String zu ISO-8859-1
	 * ```
	 * \nn\t3::Convert('äöü')->toIso();
	 * ```
	 * @return string
	 */
	public function toIso() {
		$input = $this->initialArgument;
		$input = html_entity_decode($input);
		if (function_exists('iconv')) {
			$input = iconv('UTF-8', 'ISO-8859-1', $input);
		}
		return $input;
	}
}