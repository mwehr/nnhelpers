
.. include:: ../../Includes.txt

.. _Db:

==============================================
Db
==============================================

\\nn\\t3::Db()
----------------------------------------------

Overview of Methods
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

\\nn\\t3::Db()->debug(``$query = NULL, $return = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->delete(``$table = '', $constraint = [], $reallyDelete = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->filterDataForTable(``$data = [], $table = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->findAll(``$table = '', $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->findByUid(``$table = '', $uid = NULL, $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->findByUids(``$table = '', $uids = NULL, $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->findByValues(``$table = NULL, $whereArr = [], $useLogicalOr = false, $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->findIn(``$table = '', $column = '', $values = [], $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->findNotIn(``$table = '', $colName = '', $values = [], $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->findOneByValues(``$table = NULL, $whereArr = [], $useLogicalOr = false, $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->get(``$uid, $modelType = '', $ignoreEnableFields = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getColumn(``$table = '', $colName = '', $useSchemaManager = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getColumnLabel(``$column = '', $table = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getColumns(``$table = '', $useSchemaManager = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getColumnsByType(``$table = '', $colType = '', $useSchemaManager = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getConnection();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getDeleteColumn(``$table = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getQueryBuilder(``$table = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getRepositoryForModel(``$className = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->getTableNameForModel(``$className = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->ignoreEnableFields(``$queryOrRepository, $ignoreStoragePid = true, $ignoreHidden = false, $ignoreDeleted = false, $ignoreStartEnd = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->insert(``$tableNameOrModel = '', $data = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->orderBy(``$queryOrRepository, $ordering = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->persistAll();
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->quote(``$value = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->save(``$tableNameOrModel = '', $data = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->setFalConstraint(``$queryBuilder = NULL, $tableName = '', $falFieldName = '', $numFal = true, $operator = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->setNotInSysCategoryConstraint(``$queryBuilder = NULL, $sysCategoryUids = [], $tableName = '', $categoryFieldName = 'categories'``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->setSysCategoryConstraint(``$queryBuilder = NULL, $sysCategoryUids = [], $tableName = '', $categoryFieldName = 'categories', $useNotIn = false``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->sortBy(``$objectArray, $fieldName = 'uid', $uidList = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->statement(``$statement = '', $params = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->tableExists(``$table = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->truncate(``$table = ''``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->undelete(``$table = '', $constraint = []``);
"""""""""""""""""""""""""""""""""""""""""""""""

\\nn\\t3::Db()->update(``$tableNameOrModel = '', $data = [], $uid = NULL``);
"""""""""""""""""""""""""""""""""""""""""""""""

