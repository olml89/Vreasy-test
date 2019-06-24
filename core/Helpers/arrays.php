<?php declare(strict_types = 1);


function array_lastElement(array $array) {
	return array_values(array_slice($array, -1))[0];
}


function array_lastKey(array $array) {
	return array_lastElement(array_keys($array));
}


//retorna TRUE si a y b tienen los mismos valores, sea cual sea el orden
function array_equals(array $a, array $b, bool $strict) : bool {

	if($strict && count($a) != count($b)) {	//strict: tiene que haber exactamente los mismos elementos. FALSE: todos los de a tienen que estar en b, pero no al revés
		return FALSE;
	}

	$equals = TRUE;

	for($i = 0; $i < count($a) && $equals; ++$i) {
		$equals = in_array($a[$i], $b);
	}

	return $equals;

}


//retorna TRUE si es un array asociativo, FALSE si es secuencial
function array_assoc(array $a) : bool {

    if(array() === $a) {
    	return FALSE;
    }

    return array_keys($a) !== range(0, count($a) - 1);
    
}


function getPositionInList(float $value, array $list) : int {

	usort($list, function(float $val1, float $val2) : bool {
		return $val1 < $val2;
	});

	$pos = 0;
	$n = count($list);

	for($i = 0; $i < $n; ++$i) {

		if($value < $list[$i]) {
			++$pos;
		}

	}

	return $pos;

}


function getPercentValue(float $value, array $list) : float {
	$max = max($list);
	$min = min($list);
	$range = $max - $min;
	$value = $value - $min;
	return $value * 100 / $range;
}


//https://stackoverflow.com/questions/1319903/how-to-flatten-a-multidimensional-array
function array_flatten(array $array) : array {
	$result = [];
	array_walk_recursive($array, function($a) use (&$result) { $result[] = $a; });
	return $result;
}
