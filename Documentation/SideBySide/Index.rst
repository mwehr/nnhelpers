.. include:: ../Includes.txt

.. _SideBySide:

============
Side-by-Side
============

Still not believing, that this Extension could completely change the way you have been working on Typo3 projects up until now?

Let's have a look at some of the everyday tasks you have been implementing over and over again. 
Compare not only the number of code-lines involved in getting the job done – but also the brain-energy needed to memorize
the steps, methods and parameters required in the direct comparison.  

Also pay attention to how the main concept of building links in the backend context has changed from Version 8 to 9.
Sure: Things have definately gotten better. But think of the time you would have spent to find this solution and update it in all of
you extensions.

Then look at the nnhelpers one-liner on the right side. See the difference between building links in the frontend or backend context?
Any difference in building the links from version 8 LTS to 9 LTS?

I think you understand, what we are talking about.


Getting the TypoScript Setup outside of the Controller
------------------------------------------------------------

**Task**: You need to get the `demo.path` value from the TypoScript setup of a plugin, but you are not in a context where you could simply do a `$this->settings` to retrieve it.

.. container:: row m-0 p-0

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **Standard Typo3**

      .. code-block:: php

         use TYPO3\CMS\Core\Utility\GeneralUtility;
         use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
         use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

         $objectManager = GeneralUtility::makeInstance( ObjectManager::class );
         $configurationManager = $objectManager->get( ConfigurationManager::class );
         $settings = $configurationManager->getConfiguration( ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, null, 'tx_my_ext');
         $value = $settings['demo']['path'] ?? '';

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **WITH nnhelpers**

      .. code-block:: php

         $value = \nn\t3::Settings()->get('my_ext_table', 'demo.path');


Build a Link - in the Frontend Context
---------------------------------------

**Task**: Create a Link to a page in the frontend and pass a parameter. 
This script could be in a Controller or ViewHelper.

.. container:: row m-0 p-0

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **Standard Typo3**

      .. code-block:: php

         use TYPO3\CMS\Core\Utility\GeneralUtility;
         use TYPO3\CMS\Extbase\Object\ObjectManager;
         use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

         $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
         $uriBuilder = $objectManager->get(UriBuilder::class);
         $uri = $uriBuilder
            ->reset()
            ->setTargetPageUid(199)
            ->setArguments(['test'=>99])
            ->build();

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **WITH nnhelpers**

      .. code-block:: php

         \nn\t3::Page()->getLink(199, ['test'=>99]);


Build a Link - in the Backend Context, prior Version 9 LTS
------------------------------------------------------------

**Task**: Create a Link from a scheduler or CLI Job and pass a parameter. 
This script could be in a task called by a crobjob via Scheduler.
Up until version Typo3 9 LTS it was necessary to manually init the Frontend. 

.. container:: row m-0 p-0

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **Standard Typo3**

      .. code-block:: php

         $id = 1;
         $typeNum = 0;
         EidUtility::initTCA();

         if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
            $GLOBALS['TT']->start();
         }

         $GLOBALS['TSFE'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], $id, $typeNum);
         $GLOBALS['TSFE']->connectToDB();
         $GLOBALS['TSFE']->initFEuser();
         $GLOBALS['TSFE']->determineId();
         $GLOBALS['TSFE']->initTemplate();
         $GLOBALS['TSFE']->getConfigArray();

         if (ExtensionManagementUtility::isLoaded('realurl')) {
            $rootline = BackendUtility::BEgetRootLine($id);
            $host = BackendUtility::firstDomainRecord($rootline);
            $_SERVER['HTTP_HOST'] = $host;
         }

         $cObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');

         $uri = $cObj->typoLink_URL([
            'parameter' => intval(199),
            'additionalParams' => '&test=99,
            'additionalParams.insertData' => 1,
            'returnLast' => 'url',
            'useCacheHash' => 1
         ]);

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **WITH nnhelpers**

      .. code-block:: php

         \nn\t3::Page()->getLink(199, ['test'=>99]);


Build a Link - in the Backend Context, since Version 9 LTS
------------------------------------------------------------

**Same task**: Create a Link from a scheduler or CLI Job and pass a parameter. 
This script could be in a task called by a crobjob via Scheduler.
With version 9 of Typo3 things have gotten a little more easy... or have they?  

.. container:: row m-0 p-0

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **Standard Typo3**

      .. code-block:: php

         use TYPO3\CMS\Core\Site\SiteFinder;
         use TYPO3\CMS\Core\Utility\GeneralUtility;

         $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId( 199 );
         $uri = $site->getRouter()->generateUri( 199, ['test' => 99] );

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **WITH nnhelpers**

      .. code-block:: php

         \nn\t3::Page()->getLink(199, ['test'=>99]);


Retrieving data from a table
------------------------------------------------------------

**Task**: Get one row of raw data from the database table by its `uid`.

.. container:: row m-0 p-0

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **Standard Typo3**

      .. code-block:: php

         use TYPO3\CMS\Core\Database\ConnectionPool;
         use TYPO3\CMS\Core\Utility\GeneralUtility;

         $queryBuilder = GeneralUtility::makeInstance( ConnectionPool::class )
            ->getQueryBuilderForTable( 'my_ext_table' )
            ->select('*')
            ->from( 'my_ext_table' );
         $queryBuilder->andWhere(
            $queryBuilder->expr()->eq( 'uid', $queryBuilder->createNamedParameter( 99 ))
         );
         $row = $queryBuilder->executeQuery()->fetchAllAssociative();

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **WITH nnhelpers**

      .. code-block:: php

         \nn\t3::Db()->findByUid('my_ext_table', 99);


Retrieving data from a table
------------------------------------------------------------

**Task**: Get the raw data of a database table and ignore the `hidden` field and start/endtime restrictions i.e. to simply export it as CSV or iterate through it.

.. container:: row m-0 p-0

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **Standard Typo3**

      .. code-block:: php

         use TYPO3\CMS\Core\Database\ConnectionPool;
         use TYPO3\CMS\Core\Utility\GeneralUtility;
         use TYPO3\CMS\Core\Database\Query\Restriction;

         $queryBuilder = GeneralUtility::makeInstance( ConnectionPool::class )
            ->getQueryBuilderForTable( 'my_ext_table' )
            ->select('*')
            ->from( 'my_ext_table' );
         $restrictions = $queryBuilder->getRestrictions();				
         $restrictions->removeByType( StartTimeRestriction::class );
         $restrictions->removeByType( EndTimeRestriction::class );
         $restrictions->removeByType( HiddenRestriction::class );

         $rows = $queryBuilder->executeQuery()->fetchAllAssociative();

   .. container:: col-md-6 pl-0 pr-3 py-3 m-0

      .. rst-class:: card-header

         **WITH nnhelpers**

      .. code-block:: php

         \nn\t3::Db()->findAll('my_ext_table', true);

