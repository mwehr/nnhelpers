.. include:: ../Includes.txt

.. _installation:

============
Installation
============

Die Installation läuft wie bei jeder Extension ab. 

Du musst keine TypoScript-Templates hinzufügen. Da die Extension "nichts tut", außer einen Werkzeugkoffer an Methoden und Funktionen für Deine tägliche Arbeit bereitzustellen, wird es auch keine Konflikte mit anderen Extensions geben.

Du stehst auf den Extension Manager?
-------------------------------------
Drücke den "Erweiterung hinzufügen"-Button und suche Sie nach dem Extension Key `nnhelpers`. 
Importiere die Extension aus dem Repository. Schau ins Backend-Modul.
Fang an zu coden. Hab Spaß.

Handarbeit ist Dein Ding?
---------------------------
Die aktuellste Version findest Du immer auf `https://extensions.typo3.org/extension/nnhelpers/ <https://extensions.typo3.org/extension/nnhelpers/>`_ zum direkten Download.
Lade die t3x- oder zip-Version herunter - und anschließend im Extension-Manager hoch.
Aktivieren, fertig.

composer ist Dein Freund?
-------------------------
Wenn Typo3 bei Dir im Composer-Modus läuft, findest Du die neueste Version auf packagist unter dem Key `nng/nnhelpers`.
Sorry, dass `nng` wie Angular klingt. Das ist ein ziemlich dämliches Akronym für `Neunundneunziggrad` (99°).

.. code-Block:: bash

   composer require nng/nnhelpers


Nichts git übers GIT?
-----------------------
Klar, gi(b)t es auch dort. Um genau zu sein, bei Bitbucket.
Wenn es Dich glücklich macht, ziehe es Dir wie die "harten Jungs" über die Kommandozeile. 

.. code-block:: bash

   git clone https://bitbucket.org/99grad/nnhelpers/src/master/


Dependencies festlegen
========================

Wenn Du `nnhelpers` in Deiner eigenen Extension verwenden möchtest, denke daran, die Abhängigkeiten in der `ext_emconf.php` und `composer.json` zu definieren:

Das hier kommt in die `ext_emconf.php` Deiner Extension:

.. code-block:: php

   $EM_CONF[$_EXTKEY] = [
      ...
      'constraints' => [
         'depends' => [
            'nnhelpers' => '1.7.0-0.0.0',
         ],
      ],
   ];

Und das hier kommt in die `composer.json` Deiner Extension:

.. code-block:: json

   {
      ...
      "require": {
         "nng/nnhelpers": "^1.6"
      },
   }