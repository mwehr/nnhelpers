
.. include:: ../../Includes.txt

.. _FrontendUser:

==============================================
FrontendUser
==============================================

\\nn\\t3::FrontendUser()
----------------------------------------------

Overview of Methods
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

\\nn\\t3::FrontendUser()->get();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getAvailableUserGroups(``$returnRowData = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getCookieName();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getCurrentUser();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getCurrentUserGroups(``$returnRowData = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getCurrentUserUid();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getGroups(``$returnRowData = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getLanguage();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getSession();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getSessionData(``$key = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->getSessionId();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->hasRole(``$roleUid``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->isInUserGroup(``$feGroups = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->isLoggedIn();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->login(``$username, $password = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->logout();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->removeCookie();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->resolveUserGroups(``$arr = [], $ignoreUids = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->setCookie(``$sessionId = NULL, $request = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->setPassword(``$feUserUid = NULL, $password = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::FrontendUser()->setSessionData(``$key = NULL, $val = NULL, $merge = true``);
"""""""""""""""""""""""""""""""""""""""""""""""

