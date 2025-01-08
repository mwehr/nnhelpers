
.. include:: ../../Includes.txt

.. _Page:

==============================================
Page
==============================================

\\nn\\t3::Page()
----------------------------------------------

Overview of Methods
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

\\nn\\t3::Page()->addCssFile(``$path, $compress = false, $atTop = false, $wrap = false, $concat = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addCssLibrary(``$path, $compress = false, $atTop = false, $wrap = false, $concat = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addFooter(``$str = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addFooterData(``$html = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addHeader(``$str = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addHeaderData(``$html = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addJsFile(``$path, $compress = false, $atTop = false, $wrap = false, $concat = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addJsFooterFile(``$path, $compress = false, $atTop = false, $wrap = false, $concat = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addJsFooterLibrary(``$path, $compress = false, $atTop = false, $wrap = false, $concat = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->addJsLibrary(``$path, $compress = false, $atTop = false, $wrap = false, $concat = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->clearCache(``$pid = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->get(``$uid = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getAbsLink(``$pidOrParams = NULL, $params = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getActionLink(``$pid = NULL, $extensionName = '', $pluginName = '', $controllerName = '', $actionName = '', $params = [], $absolute = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getChildPids(``$parentPid = 0, $recursive = 999``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getData(``$pids = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getField(``$key, $slide = false, $override = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getLink(``$pidOrParams = NULL, $params = [], $absolute = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getPageRenderer();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getPid(``$fallback = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getPidFromRequest();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getRootline(``$pid = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getSiteRoot(``$returnAll = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getSubpages(``$pid = NULL, $includeHidden = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->getTitle();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->hasSubpages(``$pid = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Page()->setTitle(``$title = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

