<?php
namespace Nng\Nnhelpers\Domain\Model;


class Entry extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

	/**
     * media
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $media;
	
	/**
     * categories
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected $categories;

	/**
     * data
     *
     * @var string
     */
	protected $data = '';
	
	/**
     * Initialize categories and media relation
     *
     * @return void
     */
    public function __construct()
    {
        $this->media = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}
	
	/**
     * Sets the media
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $media
     * @return void
     */
    public function setMedia(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $media)
    {
        $this->media = $media;
    }

 	/**
	* Adds a Media
	*
	* @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $media
	* @return void
	*/
	public function addMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $media) {
		$this->media->attach($media);
	}

	/**
	 * Removes a Media
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $media The Category to be removed
	 * @return void
	 */
	public function removeMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $media) {
		$this->media->detach($media);
	}

	/**
	 * Returns the Media
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $media
	 */
	public function getMedia() {
		return $this->media;
	}
	
	/**
	 * Returns the first Media
	 *
	 * @return TYPO3\CMS\Extbase\Domain\Model\FileReference $media
	 */
	public function getFirstMedia() {
		foreach( $this->getMedia() as $media) {
			return $media;
		}
	}


	/**
	 * @return  string
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param   string  $data  data
	 * @return  self
	 */
	public function setData(string $data) {
		$this->data = $data;
		return $this;
	}

	/**
	 * @return  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param   \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> 
	 * @return  self
	 */
	public function setCategories($categories) {
		$this->categories = $categories;
		return $this;
	}
}
