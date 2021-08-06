<?php

namespace Nether\Object;

class PropertyMapCache {

	static public array
	$Classes = [];

	static public function
	Get(string $ClassName):
	?array {

		return static::$Classes[$ClassName];
	}

	static public function
	Has(string $ClassName):
	bool {

		return array_key_exists($ClassName,static::$Classes);
	}

	static public function
	Set(string $ClassName, array $PropertyMap):
	array {

		static::$Classes[$ClassName] = $PropertyMap;
		return $PropertyMap;
	}

}
