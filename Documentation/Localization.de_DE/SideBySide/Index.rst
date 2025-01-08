.. include:: ../Includes.txt

.. _SideBySide:

============
Side-by-Side
============

Du glaubst noch immer nicht, dass diese kleine Extension Deinen Typo3-Alltag auf den Kopf stellen könnte?

Werfen wir einen Blick auf einige klassische Aufgaben. Die linke Spalte wirst Du gut kennen: So arbeitest Du bisher. Rechts daneben siehst Du die Lösung mit nnhelpers.
Vergleiche nicht nur die Anzahl der Code-Zeilen, die für das Lösen der Aufgabe erforderlich sind - sondern auch den geistigen Energieaufwand, den man braucht, um sich 
die Schritte, Methoden und Parameter zu merken.  

Achten auch darauf, wie sich das z.B. Grundkonzept beim Erstellen von Links im Backend-Kontext von Version 8 zu 9 verändert hat.
Ja, die Situation hat sich deutlich verbessert. Aber denke mal an die Zeit, die Du wieder gebraucht hättest, diese Lösung zu finden und sie in allen Deinen Extensions zu aktualisieren!

Dann sieh Dir den nnhelpers-Einzeiler auf der rechten Seite an. Siehst Du den Unterschied im Aufbau von Links im Frontend- oder Backend-Kontext?
Und wie steht es mit dem Sprung von Version 8 LTS zu 9 LTS?

Ich denke, Du verstehst, um was es geht.


Ein TypoScript-Setup außerhalb des Controllers erhalten
------------------------------------------------------------

**Aufgabe**: Du möchtest den Wert für `demo.path` aus den TypoScript-Settings eines Plugins holen, aber leider bist Du nicht in einem Kontext, bei dem `$this->settings` vorhanden ist.
Vielleicht bist Du in einem Repository – oder ViewHelper.

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

         **MIT nnhelpers**

      .. code-block:: php

         $value = \nn\t3::Settings()->get('my_ext_table', 'demo.path');


Einen Link bauen - im Frontend Context
---------------------------------------

**Aufgabe**: Einen Link zu einer Seite im Frontend erzeugen während Du im Frontend-Context bist.
Dieses Script könnte z.B. in einem Controller oder ViewHelper sein.

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

         **MIT nnhelpers**

      .. code-block:: php

         \nn\t3::Page()->getLink(199, ['test'=>99]);


Einen Link bauen - im Backend Context, vor Version 9 LTS
------------------------------------------------------------

**Aufgabe**: Einen Link zu einer Seite im Frontend bauen, allerdings im Backend- oder CLI-Context.
Sprich: Du hast kein Frontend, brauchst aber einen Link zu einer Seite im Frontend. Auf dieses Problem stößt jeder, der mal 
versucht hat, innerhalb eines eigenen Scheduler-Tasks einen Link zu generieren.
Bis Version 9 musste man dazu das Frontend manuell instanziieren, was ungefähr in diesem Wahnsinn endete:

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

         **MIT nnhelpers**

      .. code-block:: php

         \nn\t3::Page()->getLink(199, ['test'=>99]);


Einen Link bauen - im Backend Context, seit Version 9 LTS
------------------------------------------------------------

**Gleiche Aufgabe**: In Version 9 ist einiges besser und kürzer geworden.
Trotzdem bleibt es eine Menge Zeilen Code:

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

         **MIT nnhelpers**

      .. code-block:: php

         \nn\t3::Page()->getLink(199, ['test'=>99]);


Einen Datensatz aus der Datenbank lesen
------------------------------------------------------------

**Aufgabe**: Eine "rohe" Zeile aus der Datenbank lesen anhand seiner `uid`.

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

         **MIT nnhelpers**

      .. code-block:: php

         \nn\t3::Db()->findByUid('my_ext_table', 99);


Alle Zeilen in der Datenbank holen
------------------------------------------------------------

**Aufgabe**: Alle Daten einer Tabelle aus der Datenbank lesen, aber die Flags für `hidden` sowie die `start_time` und `end_time` ignorieren.
Diese Anwendung hat man häufig, wenn man extrem viele Datensätze möglichst performant und ohne Model braucht, z.B. für einen Eport nach Excel.

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

         **MIT nnhelpers**

      .. code-block:: php

         \nn\t3::Db()->findAll('my_ext_table', true);

