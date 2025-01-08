
.. include:: ../../Includes.txt

.. _File:

==============================================
File
==============================================

\\nn\\t3::File()
----------------------------------------------

Overview of Methods
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

\\nn\\t3::File()->absPath(``$file = NULL, $resolveSymLinks = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->absUrl(``$file = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->addPathSite(``$file``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->addSuffix(``$filename = NULL, $newSuffix = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->cleanFilename(``$filename = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->copy(``$src = NULL, $dest = NULL, $renameIfFileExists = true``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->createFolder(``$path = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->download(``$files = NULL, $filename = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->exists(``$src = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->extractExifData(``$filename = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getData(``$file = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getExifData(``$filename = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getFolder(``$file``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getImageData(``$filename = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getImageSize(``$filename = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getLocationData(``$filename = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getPath(``$file, $storage = NULL, $absolute = true``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getPublicUrl(``$obj = NULL, $absolute = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getRelativePathInStorage(``$file, $storage = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->getStorage(``$file, $createIfNotExists = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->isAllowed(``$filename = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->isConvertableToImage(``$filename = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->isExternalVideo(``$url = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->isFolder(``$file``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->isForbidden(``$filename = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->isVideo(``$filename = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->mkdir(``$path = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->move(``$src = NULL, $dest = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->moveUploadedFile(``$src = NULL, $dest = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->normalizePath(``$path``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->process(``$fileObj = '', $processing = [], $returnProcessedImage = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->processImage(``$filenameOrSysFile = '', $processing = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->read(``$src = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->relPath(``$path = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->resolvePathPrefixes(``$file = NULL, $absolute = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->sendDownloadHeader(``$filename = '', $filesize = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->size(``$src = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->stripPathSite(``$file, $prefix = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->suffix(``$filename = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->suffixForMimeType(``$mime = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->type(``$filename = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->uniqueFilename(``$filename = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->unlink(``$file = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::File()->write(``$path = NULL, $content = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

