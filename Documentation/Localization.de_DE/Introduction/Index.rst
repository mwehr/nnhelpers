.. include:: ../Includes.txt

.. _introduction:

======================
Wozu die Extension?
======================

.. danger::

   Diese Extension wird Dir viel, viel Zeit sparen. Zeit, die Du dann mit anderen Dinge verbringen musst. Mit Deinen Kindern, Deinem Ehepartner oder Deiner cholerischen Schwiegermutter. Anders gesagt: Dir könnten die Ausreden ausgehen.

   Falls Du Zweifel hast, ob Du das durchhältst, sollten Du nnhelpers **nicht** installieren. Für Risiken und Nebenwirkungen, die durch gewonnene Zeit in Deinem Entwicklerleben auftreten könnten, übernehmen wir keine Haftung.

.. _what-it-does:

Was macht nnhelpers?
=====================

Diese Extension übernimmt den unschönen Teil Deines Entwickleralltags. Immer wenn Du denkst: "Verdammt, das muss doch irgendwie einfacher gehen" wirst Du Dich an nnhelpers erinnern. Die Extension sitzt in einem Backend-Modul, immer griffbereit – und (fast) immer mit einem einfachen Einzeiler, der Dein Problem löst. 

Du willst Beispiele? Wunderbar.
--------------------------------

Schon mal probiert, in Typo3 ein simples Upload-Formular zu bauen, bei dem Dein User im Frontend ein Bild hochladen kann? Klingt eigentlich nach einem Kinderspiel, oder?

Nun ja. Wäre es auch – gäbe es da nicht den "File Abstract Layer" (FAL).
Ein neues Domain-Model ist schnell gebaut und instanziiert. Aber wie bekommt man die Upload-Datei aus dem PHP tmp-Verzeichnis vernünftig in eine `SysFileReference` konvertiert, die sich an das Model hängen lässt?

Du stellt die Frage Google. Nach vielen Varianten Deiner Suchphrase landest Du auf der vielversprechenden Doku unter `docs.typo3.org <https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Fal/UsingFal/ExamplesFileFolder.html>`__. Leider findest Du dort nur die ernüchternde Aussage: "Sorry, mein Lieber. Nicht unser Bier. Frag Helmut."

Du gibst also `Helmut <https://github.com/helhum/upload_example>`__ einen Besuch. Der macht einen prima Job. Viel Code. Super strukturiert und kommentiert. Eine knappe halbe Stunde versuchst Du, in seinem Repo genau das Stück destillierten Code zu identifiziern, das Du für Deinen Fall brauchst.

Für das Beispiel oben haben wir in der ersten Umsetzung knapp 4 Stunden gebraucht. Wir waren damals in der 6er Version von Typo3. Als dann Typo3 7 LTS veröffentlicht wurde, suchten wir erneut - im Core gab es neue, brilliante Ideen. Und in Version 8? Halleluja.

Immer wieder passten wir alle Extensions von uns an die neuen Konzepte der Typo3 Versionen an. Und jedes Mal schien uns das recht sinnfrei. Viel Zeit, die ich offen gesagt lieber mit anderen Dingen verbringe. Wenn es sein muss, auch mit meiner Schwiegermutter. 

**Wie lösen wir das heute?**

| Wir würde `nnhelpers` um Hilfe bitten.
| Schauen wir mal ins **Backend Module**:
| 

.. figure:: ../../Images/backend-01.jpg
   :class: with-shadow
   :alt: nnhelpers Backend Module
   :width: 100%

   Das Backend Modul zeigt alle Methoden und ViewHelper übersichtlich gruppiert nach Themen

| Wonach suchen wir?
| Richtig. Es geht um **FAL**. Also: Runterscrollen wir zum Abschnitt **FAL** und schauen, was es im Angebot gibt|

.. figure:: ../../Images/backend-02.gif
   :class: with-shadow
   :alt: nnhelpers Backend Module
   :width: 100%

   Jede Methode hat eine ausführliche Erklärung und Beispiel.


| `setInModel()` hört sich genau nach dem an, was wir suchen. 
| Wie sieht das Beispiel dazu aus? 

.. code-block:: php

   \nn\t3::Fal()->setInModel( $model, 'fieldname', 'path/to/image.jpg' );

| Moment, willst Du mir wirklich sagen, um das Problem zu lösen, brauche ich nur einen verdammten Einzeiler?
| Kein `ObjectManager->get()`, kein `@inject`?
| 
| Aber das Beste ist: Dieser Einzeiler wird sich nie ändern.
| Nicht für Typo3 Version 7. Nicht für Typo3 11. Versprochen.
|

| Ok, aber was ist, wenn der User mehrere Bilder hochgeladen hat?
| Klar, ich könnte eine `foreach`-Schleife daraus machen. Aber Moment!
| Das ist so einfach – hätte ich von selbst drauf kommen können.
| 

.. code-block:: php

   \nn\t3::Fal()->setInModel( $model, 'fieldname', ['image-1.jpg', 'image-2.jpg'] );

| **Ja, schön. Aber das ist gemogelt.**
| Meine Anwendung ist komplexer. Ich möchte, dass der User auch den Bildtitel und die Bildbeschreibung im Frontend eingeben kann. Oh – aber das scheint ja auch zu gehen:
|

.. code-block:: php

   \nn\t3::Fal()->setInModel( $member, 'fieldname', ['publicUrl'=>'01.jpg', 'title'=>'Titel', 'description'=>'...'] );

| **HA! Jetzt hab ich Euch erwischt.**
| Ihr habt Euch selbst ein Ei gelegt. Jetzt kann der User ja nicht **mehrere Bilder** mit **jeweils** einem Titel und einer Beschreibung hochladen. Ähm... Moment - was ist das?

.. code-block:: php

   \nn\t3::Fal()->setInModel( $member, 'fieldname', [
      ['publicUrl'=>'01.jpg', 'title'=>'Titel', 'description'=>'...'],
      ['publicUrl'=>'02.jpg', 'title'=>'Titel', 'description'=>'...'],
   ]);

| **Ja, aber mal ehrlich: Das ist häßlich. An der Uni habe ich gelernt, dass...**
| Richtig. Aber wenn es das Leben einfacher macht, ist uns das einfach egal. 
| Das hier ist eine Zeile Code, die Du Dir merken kannst. Die Benutzung ist intuitiv – sie folgt keinen Konventionen oder Paradigmen. Sie folgt Deiner Intuition.
| 
| **Dir gefällt das alles nicht?**
| Fein. Dann kannst Du Dir den Quelltext klauen und daraus etwas eigenes bauen. Damit Du nicht in Dein Dateisystem abtauchen musst, haben wir auch hier mitgedacht: Ein Klick und Du blickst mitten ins Eingemachte.
|

.. figure:: ../../Images/backend-03.gif
   :class: with-shadow
   :alt: nnhelpers Backend Module
   :width: 100%

   Quelltext direkt im Backend anschauen.

| Deine Entscheidung!
| 


Stop thinking, start coding.
------------------------------------