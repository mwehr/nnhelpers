.. include:: ../Includes.txt

.. _use-cases:

====================
Use Cases & Examples
====================

Debugging
--------------------------------------------

Du denkst vielleicht: Die Typo3 `DebuggerUtility` ist großartig. Ja, ist sie.

Aber: Hast Du jemals versucht, einen `QueryBuilder` zu debuggen, weil Du mit dem "Old School" Blick auf ein SQL-Statement besser verstehst, was genau bei Deiner Datenbankabfrage schief geht? Hast Du Dich dann gewundert, dass Du keine Paramter siehst, sondern nur `?` an den entscheidenden Stellen? Wie lange hast Du gegoogelt, um eine Antwort zu finden und Dich dann wieder einmal gefragt: "Muss das so kompliziert sein?"

Ist es Dir schon mal passiert, dass Du ein `debug()` im Code vergessen hast und am nächsten Morgen einfach nicht mehr wusstest, wo Du diesen debug hingeschrieben hast? Wäre es nicht schön, einfach die Zeilennummer und das Script direkt sehen zu können, die das debug getriggert hat? 

Wenn Du auch sonst keine einzige anderer Methode aus `nnhelpers` anfassen wirst:
Hier ist die eine Zeile, nach der Du süchtig werden wirst:

.. code-block:: php
   
   \nn\t3::debug( $whatever );


Extensions entwickeln
-------------------

Wenn man Jahre lang Extensions für Typo3 entwickelt, freut man sich über vieles. Aber man wundert sich auch oft.

Eine davon: Seit einigen Versionen, kann man Icons für das Backend (z.B. für ein Backend-Modul) nicht mehr als Pfad zum svg oder jpg angeben. Stattdessen geht man den Umweg über die `IconRegistry` und registriert das Icon in der `ext_tables.php`.

Mag Sinn haben. Aber es wird ja noch komplizierter. Abhängig vom Dateityp muss man zunächst den passenden `IconProvider` dazu instanziieren. Für ein `svg` landet man so bei einigen Zeilen Code – vom `SvgIconProvider` bis zum `IconRegistry`.
 
Das ist eine Menge Hirnschmalz dafür, dass doch eigentlich das Suffix `.svg` sagen sollte, um welchen Dateityp es sich handelt.

Wir finden, Helpers könnte uns diese Denkarbeit abnehmen:

.. code-block:: php
   
   \nn\t3::Registry()->icon('my-icon-identifier', 'EXT:myext/Resources/Public/Icons/wizicon.svg');



TCAaaalbträume
-------------------

Noch einer unserer Lieblinge: Im TCA ein Feld für eine FAL Relation definieren. Erinnerst Du Dich an die 28 Zeilen code, um einen Dateiupload im Backend zu ermöglichen? Hast Du gemerkt, dass sich die Struktur gerne von Version zu Version ändert – und musstest Du durch alle TCAs gehen und die Anpassungen nachziehen?

Hier ist ein magischer Einzeiler für Dein nächstes `FAL`:

.. code-block:: php
   
   'falprofileimage' => [
      'config' => \nn\t3::TCA()->getFileFieldTCAConfig('falprofileimage'),
   ],

Ach, Du vermisst die Optionen? Kein Problem:

.. code-block:: php
   
   'falprofileimage' => [
      'config' => \nn\t3::TCA()->getFileFieldTCAConfig('falprofileimage', ['maxitems'=>1, 'fileExtensions'=>'jpg']),
   ],

Und natürlich gibt es noch viele, weitere Einzeiler, z.B. für die Definition eines Texteditors (`ckeditor`).

Das beste daran: Wenn das Core-Team sich entscheidet, von `ckeditor` Abschied zu nehmen (so wie sie es - zum Glück - vor einigen Jahren mit der `rtehtmlarea` getan haben), dann wird das nicht mehr Dein Problem sein. Lass es das Problem von `nnhelpers` sein. 

.. code-block:: php
   
   'mytextfield' => [
      'config' => \nn\t3::TCA()->getRteTCAConfig(),
   ],

Wir wäre es mit einem Farbwähler?

.. code-block:: php
   
   'mycolor' => [
      'config' => \nn\t3::TCA()->getColorPickerTCAConfig(),
   ],

Ein FlexForm in ein TCA-Feld einschleusen
-------------------------------------------

Lass uns durchdrehen. Schon mal darüber nachgedacht, ein **externes FlexForm in ein  TCA-Feld einzuschleusen**? Im Grunde macht Typo3 das ja bei jedem Plugin. Und `DCE` definiert über FlexForms den gesamten Inhalt des DCE-Elementes.

Nicht träumen. Coden. Und – klar – natürlich mal wieder mit einem einzigen Einzeiler.

.. code-block:: php

   'myoptions' => [
      'config' => \nn\t3::TCA()->insertFlexForm('FILE:EXT:path/to/yourFlexForm.xml');
   ],

Klingt vielleicht verrückt, aber tatsächlich nutzen wir diese Methode ständig – vor allem im Kontext mit der besten Extension, die je für Typo3 erfunden wurde: `Mask <https://extensions.typo3.org/extension/mask>`__ (`EXT:mask`).

Im folgenden Beispiel hatten wir ca. 30 Slider-Optionen für Übergänge, Dauer, Breakpoints / Responsivität und vieles mehr. Jede Option sollte in dem Mask-Element vom Redakteur auswählbar sein. Mit den Standard-Feldern von `EXT:mask` hätten wir die Datenbank-Tabelle `tt_content` um 30 Felder erweitern müssen. Felder, an die keine andere Logik gebunden ist – keine Indizierung, Sortierung oder Suche. So ein Fall schreit förmlich nach einem FlexForm statt einzelnen Datenbank-Feldern.

Mask selbst erlaubt (noch) kein FlexForm. Aber, was viele nicht wissen: Felder lassen sich in der `Configuration/TCA/Overrides/tt_content.php` seiner eigenen Extension einfach neu konfigurieren. (Achtung, Stolperfalle: Die eigene Extension muss eine Dependency zu mask in der `ext_emconf.php` definiert haben!)

Das ganze sieht dann so aus:

.. code-block:: php

   if ($_GET['route'] != '/module/tools/MaskMask') {
      if ($GLOBALS['TCA']['tt_content']['columns']['tx_mask_slideropt']) {
         $GLOBALS['TCA']['tt_content']['columns']['tx_mask_slideropt']['config'] = \nn\t3::TCA()->insertFlexForm('FILE:EXT:myext/Configuration/FlexForm/customFlexForm.xml');
      }
   }


FlexForm pimpen
-------------------

In einem Plugin (FlexForm) möchte man häufig dem Redakteur die Möglichkeit bieten, zwischen verschiendenen Layouts, Farben oder Designs zu wählen. Dazu nutzt man üblicherweise eine `select / selectSingle` Definition mit allen Optionen.

Praktischer finde ich es persönlich, wenn sich die Optionen, die im Dropdown erscheinen, per TypoScript-Setup oder `PageTSconfig` definieren lassen. Pro "Ast" im Seitenbaum können so unterschiedliche Optionen zur Verfügung gestellt werden. Außerdem finde ich es "praktischer" nicht jedesmal, wenn eine Option dazukommt, im XML des FlexForms zu hantieren.

Hier ist ein schöner, kleiner Helfer dazu:

.. code-block:: xml

   <config>
      <type>select</type>
      <renderType>selectSingle</renderType>
      <items type="array"></items>
      <itemsProcFunc>nn\t3\Flexform->insertOptions</itemsProcFunc>
      <typoscriptPath>plugin.tx_extname.settings.colors</typoscriptPath>
      <!-- Alternativ: Load options from the PageTSConfig: -->
      <pageconfigPath>tx_extname.colors</pageconfigPath>
      <insertEmpty>1</insertEmpty>
   </config>

Stimmt – und dann ist da noch die Sache mit der Länderauswahl im FlexForm. Warum auch hier komplizierter werden, als unbedingt nötig?

.. code-block:: xml

   <config>
      <type>select</type>
      <renderType>selectSingle</renderType>
      <items type="array"></items>
      <itemsProcFunc>nn\t3\Flexform->insertCountries</itemsProcFunc>
      <insertEmpty>1</insertEmpty>
   </config>



Mails senden
-------------

Großartig. Typo3 hat sich gerade von `SwiftMailer` verabschiedet. 
Erinnerst Du Dich? Das hatten wir vor ein paar Jahren schon mal – als Typo3 den Schritt **ZU** SwiftMailer gemacht hat. Blöd, wenn man `mail` in 562 Extensions genutzt hat.

Ach, und: Sorry, nein. Wir haben vor dem letzten großen Typo3 Update **nicht** Stunden damit verbracht, uns durch alle `breaking changes <https://docs.typo3.org/c/typo3/cms-core/master/en-us/>`__ zu wühlen. Wenn wir ehrlich sind, läuft ein Update auf eine neue LTS immer so ab: Helm auf, anschnallen und auf den ersten Knall warten. Google wird uns schon irgendwie aus der Unfallstelle rausschneiden.

Gut zu wissen, dass sich ab sofort ein paar Dinge **nie wieder*+ ändern werden.

.. code-block:: php

	\nn\t3::Mail()->send([
	   'html'	=> $html,
	   'fromEmail'	=> 'me@somewhere.de',
	   'toEmail'	=> 'you@faraway.de',
	   'subject'	=> 'Nice'
	]);

Angst vor Outlook? Nichts mehr zu befürchten. Der Mailhelper hat einen kleinen, weitere Gehilfen am Start: `Emogrifier <https://github.com/MyIntervals/emogrifier>`__. Der lässt sich auch mit einer Option abschalten – aber wozu... er beantwortet vielleicht eine Frage, die Du noch gar nicht gestellt hattest.

Und wie sieht es mit **Dateianhängen** aus?

.. code-block:: php

	\nn\t3::Mail()->send([
	   ...
	   'attachments' => ['path/to/file.jpg', 'path/to/other.pdf']
	]);

| Schön – aber hattest Du nicht letztes Mal das Problem, dass Du die Anhänge dynamisch während des Rendering Deines Mail-Templates einbinden wolltest? Kopf zerbrochen? Wie wäre es hiermit gewesen? Ein `data-embed="1"` in Deinem Template und Dein neuer, bester Freund macht die Denkarbeit für Dich. 

.. code-block:: php

	<img data-embed="1" src="path/to/image.jpg" />
	{f:image(image:fal, maxWidth:200, data:'{embed:1}')}

	<a href="attach/this/file.pdf" data-embed="1">Download</a>
	{f:link.typolink(parameter:file, data:'{embed:1}')}


Fluid rendern
---------------

In dem Beispiel oben haben wir noch gar nicht über den `StandaloneView` zum Rendern von Templates gesprochen. Klar, macht `nnhelpers` Dir auch hier das Leben leichter:

.. code-block:: php

	\nn\t3::Template()->render( 'path/to/template.html', $vars );

Da war noch die Sache mit den `partialRootPaths`, `layoutRootPaths` etc. Aber mal ehrlich: Ist man denn nicht (fast) immer in einer Extension, die auch die Templates dazu im Standard-Template-Ordner dieser Extension hat? 

.. code-block:: php

	\nn\t3::Template()->render( 'Templatename', $vars, 'myext' );

Der Extension-Key genügt – nnhelpers übernimmt das Nachschauen, was Du in Deiner Extension unter `plugin.tx_myext.view...` definiert hast.

Aber dann kann es natürlich auch sein, dass Du `partialRootPaths` doch selbst festlegen möchtest. Geht natürlich auch:

.. code-block:: php

	\nn\t3::Template()->render( 'Templatename', $vars, [
	   'templateRootPaths' => ['EXT:myext/Resource/Somewhere/Templates/', ...],
	   'layoutRootPaths' => ['EXT:myext/Resource/Somewhere/Layouts/', ...],
	]);

Die Beispiel zeigen, was `nnhelpers` besonders macht. Nicht `strict` sondern `smart` ist hier der Ansatz. Wenn Du einfach das tust, was Du intuitiv tun würdest, dann kannst Du relativ sicher sein: Genau so haben wir bei der Entwicklung von nnhelpers auch gedacht. 

Daten von einer Extension migrieren
--------------------------------------------

Wir haben kürzlich ein großes Projekt von Typo3 7 LTS auf Typo3 10 LTS aktualisiert. Das Projekt hatte Calendar Base (`EXT:cal`) im Einsatz und stolze 5.000 Kalendereinträge. Leider wurde `EXT:cal` (noch) nicht für Typo3 10 aktualisiert, so dass wir uns entschlossen, auf unsere eigene Kalendererweiterung `nncalendar` umzusteigen (die in ein paar Wochen im TER veröffentlicht wird).

Wir standen vor drei großen Herausforderungen: 

- Es war unmöglich, `EXT:cal` in Typo3 10 zu aktivieren - folglich gab es keine einfache Möglichkeit, auf die Datenbank-Tabellen von Calendar Base zuzugreifen oder "schöne" Modelle mit Gettern und Settern zu erstellen
- Calendar Base hatte in Version 7 noch eine eigene Kategorisierung und verwendete noch keine `sys_category`.
- Es gab tonnenweise Bilder im Ordner `uploads/pics/`, die in FAL-Bilder umgewandelt und an das neue EntryModel von `nncalendar` angehängt werden mussten

Das ist das destillerte Ergebnis:

.. code-block:: php

   // Holt alle Datensätze von EXT:cal. Die Ext muss dazu nicht aktiviert sein!
   $calData = \nn\t3::Db()->statement( "SELECT * FROM tx_cal_event WHERE deleted = 0");

   // Das neue Repo für die Kalender-Einträge
   $calendarRepository = \nn\t3::injectClass(\Nng\Nncalendar\Domain\Repository\EntryRepository::class);

   // NnCalendar-Modelle aus den rohen array-Daten erzeugen!
   foreach ($calData as $row) {

      // [...] hier gab es noch ein paar Zeilen Code, um Datum etc. zu parsen.

      $entry = \nn\t3::Convert($row)->toModel( \Nng\Nncalendar\Domain\Model\Entry::class );
      $calendarRepository->add( $entry );
   }

   \nn\t3::Db()->persistAll();


Auch die neuen `SysCategories` zu setzen war so einfach:


.. code-block:: php

   $row['category'] = [1, 4, 3];
   $entry = \nn\t3::Convert($row)->toModel( \Nng\Nncalendar\Domain\Model\Entry::class );


nnhelpers erkennt automatisch, dass das Entry-Model eine Relation zu den SysCategories im Feld `category` hat und erzeugt die ObjectStorage mit den SysCategories automatisch.

Bei den Bildern wurde es auch nicht komplizierter:

.. code-block:: php

   // e.g. $oldPath = 'uploads/pics/image.jpg' - $newPath = 'fileadmin/calendar/image.jpg'
   \nn\t3::File()->copy( $oldPath, $newPath );

   $row['falImage'] = $newPath;
   $entry = \nn\t3::Convert($row)->toModel( \Nng\Nncalendar\Domain\Model\Entry::class );

Auch hier erkennt nnhelpers automatisch, dass die Property `falImage` ein FAL or eine  ObjectStorage im Entry-Model möchte und erzeugt die `sys_file` und `sys_file_reference` automatisch.

**Fein. Und was machst Du jetzt den Rest des Tages?**


Database operations
-------------------

Ich kann mich nicht erinnern, wie oft wir einfach nur ein direktes und unkompliziertes `Update`, `Löschen` oder `Einfügen` von einzelnen Datensätzen in einer Datenbanktabelle durchführen wollten. 

Doctrine ist eine der genialsten Kreationen der vergangenen Jahre, aber es gibt Anwendungsfälle, bei denen man einfach einfach bleiben möchte. Und was ist einfacher, als ein Einzeiler – der im Hintergrund daraus eine "ganz normale" Doctrine Query baut.

Hier ist ein kleiner Auszug aus den `nn\t3::Db()`-Methoden, die uns jeden Tag Zeit sparen:

Daten des Frontend-User mit `uid = 12` laden: 


.. code-block:: php

   $feUser = \nn\t3::Db()->findByUid('fe_user', 12);


Enable-Felder ignorieren (hidden, start_time etc.)


.. code-block:: php

   $feUser = \nn\t3::Db()->findByUid('fe_user', 12, true);


Alle Datensätze der Tabelle `tx_news_domain_model_news` laden:


.. code-block:: php

   $news = \nn\t3::Db()->findAll('tx_news_domain_model_news');


Alle Frontend-User laden, die Donny heißen:

.. code-block:: php

   $feUser = \nn\t3::Db()->findByValues('fe_users', ['first_name'=>'Donny']);


Den ersten Frontend-User laden, der Peter heißt:

.. code-block:: php

   $feUser = \nn\t3::Db()->findOneByValues('fe_users', ['first_name'=>'Peter']);


Die storagePid für ein Repository ignorieren:

.. code-block:: php

   $myRepo = \nn\t3::injectClass( MyRepo::class );
   \nn\t3::Db()->ignoreEnableFields( $myRepo );


Ignoriere the storagePid and das `hidden`-Flag für ein Repository

.. code-block:: php

   $myRepo = \nn\t3::injectClass( MyRepo::class );
   \nn\t3::Db()->ignoreEnableFields( $myRepo, true, true );
