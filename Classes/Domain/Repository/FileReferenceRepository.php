<?php

namespace Nng\Nnhelpers\Domain\Repository;

class FileReferenceRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {


    /**
     *  Holt eine \TYPO3\CMS\Extbase\Domain\Model\FileReference anhand der uid
     *  Alias zu `\nn\t3::Convert( $uid )->toFileReference();`
     * 
     *  @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    /*
    public function findByUid( $uid = null ) {
        $fileRepository = \nn\t3::injectClass(\TYPO3\CMS\Core\Resource\FileRepository::class);
        return $fileRepository->findFileReferenceByUid($uid);
    }
    */
}