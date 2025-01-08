.. include:: ../Includes.txt

.. _use-cases:

====================
Use Cases & Examples
====================

Debugging
--------------------------------------------

You might think: The Typo3 `DebuggerUtility` is great. Yes, it is.

But have you ever tried to debug a QueryBuilder-Statment? Or have you tried to find a debug command somewhere in your code that you forgot to remove and you can't remember in which class and method you had put it?

Here is the one thing that will get you addicted to nnhelpers. Even if you ignore the rest and go your own way. This is worth it:

.. code-block:: php
   
   \nn\t3::debug( $whatever );


Creating Extensions
-------------------

Many small things make you wonder while you develop Typo3 extensions. 

One of them is: When Typo3 switched to the `IconRegistry` way of registering icons in `ext_tables.php` – why do you have to first use the correct instance of the `{Type}IconProvider` to get the icon in its place? To much brain work.

So, here you go. Stop asking Google. Ask nnhelpers:

.. code-block:: php
   
   \nn\t3::Registry()->icon('my-icon-identifier', 'EXT:myext/Resources/Public/Icons/wizicon.svg');



TCA nightmares
-------------------

Another one of my favorites: **Defining FALs in the TCA.**
Remember the 28 lines of code for enabling the file select/upload in the TCA? Remember having to slightly change the syntax and structure with (almost) every major Typo3 update?

Well, here is your time-saver for your next FAL definition in the `TCA`:

.. code-block:: php
   
   'falprofileimage' => [
      'config' => \nn\t3::TCA()->getFileFieldTCAConfig('falprofileimage'),
   ],

Ah, ok - your missing some options here? No problem:

.. code-block:: php
   
   'falprofileimage' => [
      'config' => \nn\t3::TCA()->getFileFieldTCAConfig('falprofileimage', ['maxitems'=>1, 'fileExtensions'=>'jpg']),
   ],

And of course, there are lots more, like a oneliner for a **Rich Text Editor** (RTE).

If the core team decides to move away from `ckeditor` like they (fortunately) did with `rtehtmlarea`, then it won't be your problem anymore. It will be nnhelpers problem. 

.. code-block:: php
   
   'mytextfield' => [
      'config' => \nn\t3::TCA()->getRteTCAConfig(),
   ],

... or a color-picker:

.. code-block:: php
   
   'mycolor' => [
      'config' => \nn\t3::TCA()->getColorPickerTCAConfig(),
   ],

Injecting a FlexForm in a TCA-field
-----------------------------------

Well, let's go wild. Ever though of injecting an **external FlexForm in a TCA**?

Don't dream about it. Code it.

.. code-block:: php

   'myoptions' => [
      'config' => \nn\t3::TCA()->insertFlexForm('FILE:EXT:path/to/yourFlexForm.xml');
   ],

Might sound crazy, but we actually need this all the time when extending the best extension ever developed for Typo3: `Mask <https://extensions.typo3.org/extension/mask>`__ (`EXT:mask`).

In the following example, we had about 30 slider-options for transition-types, duration, resposiveness etc. Every option is selectable in the plugin by the user. With plain old mask this would mean extending the `tt_content`-table by 30 fields. Fields, that have no other logic connected to them(searchability, indexing, sorting etc.). A one-time-shot for rendering the content-element – and therefore a clear case for a FlexForm. 

Mask doesn't allow injecting a FlexForm (yet). So here is what we do in `Configuration/TCA/Overrides/tt_content.php`. (Make sure to define a dependency from your extension to mask!)

.. code-block:: php

   if ($_GET['route'] != '/module/tools/MaskMask') {
      if ($GLOBALS['TCA']['tt_content']['columns']['tx_mask_slideropt']) {
         $GLOBALS['TCA']['tt_content']['columns']['tx_mask_slideropt']['config'] = \nn\t3::TCA()->insertFlexForm('FILE:EXT:myext/Configuration/FlexForm/customFlexForm.xml');
      }
   }


FlexForm pimping
-------------------

One thing we love to do is make the values in a FlexForm configurable over TypoScript Setup oder the pageTSConfig, e.g. to let the user see different options in the dropdown of a flexform (or TCA), depending on the rootline he is on in the backend.

Look at this nice Helper:

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

Ah, right. And then there was that thing about inserting a select-field for **all countries**.

.. code-block:: xml

   <config>
      <type>select</type>
      <renderType>selectSingle</renderType>
      <items type="array"></items>
      <itemsProcFunc>nn\t3\Flexform->insertCountries</itemsProcFunc>
      <insertEmpty>1</insertEmpty>
   </config>



Sending Mails
-------------

Great. Typo3 just removed `SwiftMailer`. 
We went through this already a few years ago, when Typo3 switched **TO** SwiftMailer. We have the mail-function scattered over 562 extensions. Thanks.

And, nope, sorry, we **DON'T** spend hours reading the `breaking changes <https://docs.typo3.org/c/typo3/cms-core/master/en-us/>`__ everytime we have to do an update. We upgrade the core, put on our helmets and safety-belts and see what happens. Google will somehow cut us out of the accident scene.

Good to know, some things never change.

.. code-block:: php

	\nn\t3::Mail()->send([
	   'html'	=> $html,
	   'fromEmail'	=> 'me@somewhere.de',
	   'toEmail'	=> 'you@faraway.de',
	   'subject'	=> 'Nice'
	]);

Worried about Outlook? Don't worry. `Emogrifier <https://github.com/MyIntervals/emogrifier>`__ is helping nnhelper as default settings.

Want to **add attachments?**

.. code-block:: php

	\nn\t3::Mail()->send([
	   ...
	   'attachments' => ['path/to/file.jpg', 'path/to/other.pdf']
	]);

| What about adding attachments (or inline-images) from your Fluid mail-template?
| Add `data-embed="1"` to your image or link. `nn\t3::Mail()` will take care of the hard work.

.. code-block:: php

	<img data-embed="1" src="path/to/image.jpg" />
	{f:image(image:fal, maxWidth:200, data:'{embed:1}')}

	<a href="attach/this/file.pdf" data-embed="1">Download</a>
	{f:link.typolink(parameter:file, data:'{embed:1}')}


Rendering Fluid
---------------

In the Mail-examples above we forgot to talk about the `StandaloneView` for rendering Templates.
Of course, nnhelpers makes life easier here, too:

.. code-block:: php

	\nn\t3::Template()->render( 'path/to/template.html', $vars );

But, right, then there was that thing about setting the `partialRootPaths`, `layoutRootPaths` etc. As you probably have noticed yourself, most of the time, you are in an extension when using the StandaloneView. And most of the time you want to use the `view` settings defined in the TypoScript-Setup for this extension.

.. code-block:: php

	\nn\t3::Template()->render( 'Templatename', $vars, 'myext' );

But then again, what if you need other `partialRootPaths` for rendering?

.. code-block:: php

	\nn\t3::Template()->render( 'Templatename', $vars, [
	   'templateRootPaths' => ['EXT:myext/Resource/Somewhere/Templates/', ...],
	   'layoutRootPaths' => ['EXT:myext/Resource/Somewhere/Layouts/', ...],
	]);

Let's put it this way: There is no wrong way to use nnhelpers.
Think simple and intuitive. Do what seems logical to you. 
Most of the time, we will have had the same idea.


Importing data from one extension to another
--------------------------------------------

We recently updated a major project from Typo3 7 LTS to Typo3 10 LTS. The project used Calendar Base (`EXT:cal`) and had over 5.000 calender-entries. Unfortunatly `EXT:cal` had not been updated to work with Typo3 10 so we decided to switch to our own calendar extension `nncalendar` (which will be released for public in a few weeks).

But here we faced three main challenges: 

-  It was impossible to activate `EXT:cal` in Typo3 10 - consequently there was no simple way to access the database-tables of Calendar Base or create "nice" Models with getters and setters
-  Calendar Base hat not migrated their calendar categories to the `sys_category` yet.
-  There were tons of raw images in the `uploads/pic/` folder which needed to be converted to FAL images and attached to the new EntryModel of `nncalendar`

Here is the essence of what we came up with:

.. code-block:: php

   // Get all rows as array from the Calendar Base table. EXT:cal does NOT need to activated!
   $calData = \nn\t3::Db()->statement( "SELECT * FROM tx_cal_event WHERE deleted = 0");

   // This is the Repository we're aiming at
   $calendarRepository = \nn\t3::injectClass(\Nng\Nncalendar\Domain\Repository\EntryRepository::class);

   // Create NnCalendar-Models from the raw array-data
   foreach ($calData as $row) {

      // [...] we had a few lines of code here for parsing and converting the date etc.

      $entry = \nn\t3::Convert($row)->toModel( \Nng\Nncalendar\Domain\Model\Entry::class );
      $calendarRepository->add( $entry );
   }

   \nn\t3::Db()->persistAll();


Even setting the new SysCategories in the new Model was as simple as:


.. code-block:: php

   $row['category'] = [1, 4, 3];
   $entry = \nn\t3::Convert($row)->toModel( \Nng\Nncalendar\Domain\Model\Entry::class );


nnhelpers automatically recognizes, that the Entry-Model has SysCategories related to the field `category` and will create the according relations and ObjectStorage on-the-fly.

No different approach with migrating the images:

.. code-block:: php

   // e.g. $oldPath = 'uploads/pics/image.jpg' - $newPath = 'fileadmin/calendar/image.jpg'
   \nn\t3::File()->copy( $oldPath, $newPath );

   $row['falImage'] = $newPath;
   $entry = \nn\t3::Convert($row)->toModel( \Nng\Nncalendar\Domain\Model\Entry::class );

nnhelpers automatically recognizes, that `falImage` is defined as a FAL or ObjectStorage in the Entry-Model and creates the `sys_file` and `sys_file_reference` which it then attaches to the model.

**So, what are YOU going to do the rest of the day?**


Database operations
-------------------

I can't remember how many times we just wanted to do a direct and straightforward `update`, `delete` or `insert` of individual records in a database table - without digging through the docs again and again. 

Here is a small excerpt from the `nn\t3::Db()` methods that save us time every day:

Get data for the FrontendUser with `uid = 12` 


.. code-block:: php

   $feUser = \nn\t3::Db()->findByUid('fe_user', 12);


Ignore the enable-fields (hidden, start_time etc.)


.. code-block:: php

   $feUser = \nn\t3::Db()->findByUid('fe_user', 12, true);


Get all entries from the table `tx_news_domain_model_news`


.. code-block:: php

   $news = \nn\t3::Db()->findAll('tx_news_domain_model_news');


Get all Frontend-Users named Donny

.. code-block:: php

   $feUser = \nn\t3::Db()->findByValues('fe_users', ['first_name'=>'Donny']);


Get the first Frontend-Users named Peter

.. code-block:: php

   $feUser = \nn\t3::Db()->findOneByValues('fe_users', ['first_name'=>'Peter']);


Ignore the storagePid for a Repository

.. code-block:: php

   $myRepo = \nn\t3::injectClass( MyRepo::class );
   \nn\t3::Db()->ignoreEnableFields( $myRepo );


Ignore the storagePid and `hidden`-Flag for a Repository

.. code-block:: php

   $myRepo = \nn\t3::injectClass( MyRepo::class );
   \nn\t3::Db()->ignoreEnableFields( $myRepo, true, true );
