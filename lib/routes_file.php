<?PHP 
return [
    //BEGIN_AUTO
	'ResourceOne' => [
		'authorize' => [
			'POST' => '\Controllers\ResourceOne:authorize'
		],
		'GET' => '\Controllers\ResourceOne:GET',
		'POST' => '\Controllers\ResourceOne:POST',
		'id:[0-9]+' => [
			'GET' => '\Controllers\ResourceOne:GET',
			'PUT' => '\Controllers\ResourceOne:PUT',
			'DELETE' => '\Controllers\ResourceOne:DELETE'
		]
	],
	'ResourceThree' => [
		'authorize' => [
			'POST' => '\Controllers\ResourceThree:authorize'
		],
		'GET' => '\Controllers\ResourceThree:GET',
		'POST' => '\Controllers\ResourceThree:POST',
		'id:[0-9]+' => [
			'GET' => '\Controllers\ResourceThree:GET',
			'PUT' => '\Controllers\ResourceThree:PUT',
			'DELETE' => '\Controllers\ResourceThree:DELETE'
		]
	],
	'ResourceTwo' => [
		'authorize' => [
			'POST' => '\Controllers\ResourceTwo:authorize'
		],
		'GET' => '\Controllers\ResourceTwo:GET',
		'POST' => '\Controllers\ResourceTwo:POST',
		'id:[0-9]+' => [
			'GET' => '\Controllers\ResourceTwo:GET',
			'PUT' => '\Controllers\ResourceTwo:PUT',
			'DELETE' => '\Controllers\ResourceTwo:DELETE'
		]
	]
    //END_AUTO
];
