<?php 

return [
	\Nng\Nnhelpers\Domain\Model\Category::class => [
		'tableName' => 'sys_category',
	],
	\Nng\Nnhelpers\Domain\Model\File::class => [
		'tableName' => 'sys_file',
		'properties' => [
			'storageUid' => [
				'fieldName' => 'storage',
			],
		],
	],
	\Nng\Nnhelpers\Domain\Model\FileReference::class => [
		'tableName' => 'sys_file_reference',
		'properties' => [
			'file' => [
				'fieldName' => 'uid_local',
			],
		],
	],
];