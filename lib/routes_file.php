<?PHP 
return [
    //BEGIN_AUTO
	'ResourceOne' => [
		'GET' => '\Controllers\ResourceOne:GET',
		'POST' => '\Controllers\ResourceOne:POST',
		'id:[0-9]+' => [
			'GET' => '\Controllers\ResourceOne:GET',
			'PUT' => '\Controllers\ResourceOne:PUT',
			'DELETE' => '\Controllers\ResourceOne:DELETE'
		]
	],
	'ResourceThree' => [
		'GET' => '\Controllers\ResourceThree:GET',
		'POST' => '\Controllers\ResourceThree:POST',
		'id:[0-9]+' => [
			'GET' => '\Controllers\ResourceThree:GET',
			'PUT' => '\Controllers\ResourceThree:PUT',
			'DELETE' => '\Controllers\ResourceThree:DELETE'
		]
	],
	'ResourceTwo' => [
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
