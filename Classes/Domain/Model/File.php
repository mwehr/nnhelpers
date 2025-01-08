<?php
namespace Nng\Nnhelpers\Domain\Model;


class File extends \TYPO3\CMS\Extbase\Domain\Model\File
{
	/**
	 * exif
	 *
	 * @var string
	 */
	protected $exif;

	/**
	 * identifier
	 *
	 * @var string
	 */
	protected $identifier;
	
	/**
	 * @var int
	 */
	protected $storageUid;
	
	
	/**
	 * @return mixed $exif	 
	 */
	public function getExif() {
		if (!$this->exif) return [];
		return json_decode($this->exif, true);
	}

	/**
	 * @param mixed $exif	 
	 * @return void
	 */
	public function setExif($exif) {
		if (is_array($exif)) $exif = json_encode($exif);
		$this->exif = $exif;
	}

	/**
	 * @return int $storageUid	 
	 */
	public function getStorageUid() {
		return $this->storageUid;
	}
	
	/**
	 * @return string $identifier	 
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * @return void	 
	 */
	public function setIdentifier( $str ) {
		$this->identifier = $str;
	}
	
	/**
	 * @return string $filepath	 
	 */
	public function getFilepath() {
		$storage = $this->getStorage();
		$basePath = $storage->getConfiguration()['basePath'];
		return $basePath . ltrim( $this->getIdentifier(), '/' );
	}
	
	/**
	 * @return string $filepath	 
	 */
	public function getPublicUrl() {
		if ($resource = $this->getOriginalResource()) {
			return $resource->getPublicUrl();
		}
		return '';
	}
	
	/**
	 * @return \TYPO3\CMS\Core\Resource\ResourceStorage $storage	 
	 */
	public function getStorage() {
		return \nn\t3::Storage()->findByUid( $this->storageUid );
	}

}
