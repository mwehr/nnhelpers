
.. include:: ../../Includes.txt

.. _Fal:

==============================================
Fal
==============================================

\\nn\\t3::Fal()
----------------------------------------------

Overview of Methods
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

\\nn\\t3::Fal()->attach(``$model, $field, $itemData = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->clearCache(``$filenameOrSysFile = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->createFalFile(``$storageConfig, $srcFile, $keepSrcFile = false, $forceCreateNew = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->createForModel(``$model, $field, $itemData = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->createSysFile(``$file, $autoCreateStorage = true``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->deleteProcessedImages(``$sysFile = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->deleteSysFile(``$uidOrObject = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->deleteSysFileReference(``$uidOrFileReference = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->detach(``$model, $field, $obj = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->fileReferenceExists(``$sysFile = NULL, $params = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->fromFile(``$params = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->getFalFile(``$srcFile``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->getFileObjectFromCombinedIdentifier(``$file = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->getFilePath(``$falReference``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->getFileReferenceByUid(``$uid = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->getImage(``$src = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->process(``$fileObj = '', $processing = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->setInModel(``$model, $fieldName = '', $imagesToAdd = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->toArray(``$fileReference = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->unlink(``$uidOrObject = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Fal()->updateMetaData(``$filenameOrSysFile = '', $data = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

