.. every .rst file should include Includes.txt
.. use correct path!

.. include:: Includes.txt

.. Every manual should have a start label for cross-referencing to
.. start page. Do not remove this!

.. _start:

=============================================================
99° Helpers for Typo3 (nnhelpers)
=============================================================

:Version:
   |release|

:Language:
   en

:Authors:
   99° + Lindner & Steffen

:Email:
   info@99grad.de

:License:
   This extension documentation is published under the
   `CC BY-NC-SA 4.0 <https://creativecommons.org/licenses/by-nc-sa/4.0/>`__ (Creative Commons)
   license

Save time. Have fun.
=====================

What it's about
-----------------

Do you remember the day you discovered jQuery? How many hours of life did jQuery save you? 

The idea behind jQuery was simple: Wrap many lines of code in a oneliner to make the "raw", low-level JavaScript simple to use. No worries about cross-browser compatibilities or newer browser-versions coming out. 
No need to memorize many lines of code. That's what made jQuery so successful.

Imagine ``nnhelpers`` as a kind of "jQuery for TYPO3". It wraps up many lines of code from the core in to a simple oneliner. Intuitive to use and easy to remember.
Have a look at :ref:`some examples<SideBySide>` - and you'll understand what it is all about.

But even if you say: *"Naa, I don't like jQuery. I'm a Vanilla flavoured developer."* then ``nnhelpers`` can be a real pain relieve for you. You can use it as a reference to look up code snippets for your projects.
You don't even have to dive in to the source code of the extension: Everything can be viewed :ref:`in the backend<screenshots>`. Or maybe the only thing you are looking for is a better :ref:`debugging function <use-cases>`
that can even output a QueryBuilder-Object as a readable SQL query.

The thing about TYPO3
-----------------

We love TYPO3. We think it is one of best CMS out there. But there are things that are frustrating.

One of them is, that things change a lot from version to version. With every LTS released, you start over and over again finding the namespaces that have changed, the Classes that have been removed, replaced or marked as deprecated.
And you do the same changes over and over again for every extension you have written.

The TYPO3 core team moves on and on, improving code and questioning existing concepts. This is wonderful. That's what makes TYPO3 so stable, secure and great. 
But there are many devolopers out there that simply want to "use" TYPO3 without having to invest too much time in following and understanding the new concepts. These developers tend to think TYPO3 is much too complicated.

Then they read threads like `this one <https://gist.github.com/alexanderschnitzler/c685218feea1a8956cc3f915f7a08d0b>`__ that reinforces this impression. It is part of the discussion
about the ``switchableControllerActions`` and justifies, why the core team is planning to drop them in one of the future versions:

*"Well, I can't force people not to use flexforms [...] But I will make the lives of those very hard in the future. Not because I want to make things hard but because 
I want to teach people to have a clean architecture and benefit from it. Even better though if they understand my intentions."*

This is written by one of the core developers. Although I actually think he is right – saying it in this way will make a lot of developers think: "Listen man, I have a job to get done. If I need a teacher, I'll tell you."

We think: The only one who should be forced to learn is ``nnhelpers``. He can translate the changes back to a oneliner that doesn't change. 

Simple things should stay simple. And complicated things should only be solved once. Not over and over again with every project and every LTS update.


Behind the scenes
-----------------

Over almost 6 years and 4 Typo3 versions, this extension has now grown. It has become an indispensable basic building block in Typo3 extension development at `99° <https://www.99grad.de>`__. 
Through `nnhelpers <https://bitbucket.org/99grad/nnhelpers/src/master/>`__ we have been able to reduce the development time of new extensions by half on average and the time of updates to new Typo3 versions by almost 80%.

For Contributors
-----------------

You are welcome to help improve this guide.
Just click on "Edit me on GitHub" on the top right to submit your change request.

Inspirations
-----------------

The architecture of this extension and its methods and classes is maximally inspired by the wonderful work of Sascha Ende's `t3helpers <https://extensions.typo3.org/extension/t3helpers>`__ extension.

.. toctree::
   :maxdepth: 6

   Introduction/Index
   Installation/Index
   Screenshots/Index
   Use-Cases/Index
   SideBySide/Index
   Helpers/Index
   ViewHelpers/Index
   AdditionalClasses/Index
   KnownProblems/Index
   WpHelpers/Index
   Changelog/Index
   Sitemap
