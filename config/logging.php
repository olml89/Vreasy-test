<?php declare(strict_types = 1);


return [
	'path' 		=> 'data/log.txt',
	'format'	=> 'd/m/Y H:i:s',
	'output'	=> '[%datetime%] %channel%.%level_name%: %message% %context% %extra%'.PHP_EOL
];
