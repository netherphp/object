<?php

namespace Nether\Object;

class PropertyMap {

	const
	Name = 0,
	Type = 1;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static private array
	$Cache = [];

	static public function
	Get(string $ClassName):
	?array {

		return static::$Cache[$ClassName];
	}

	static public function
	Has(string $ClassName):
	bool {

		return array_key_exists($ClassName,static::$Cache);
	}

	static public function
	Set(string $ClassName, array $PropertyMap):
	array {

		static::$Cache[$ClassName] = $PropertyMap;
		return $PropertyMap;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

}
