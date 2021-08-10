<?php

namespace Nether\Object\Prototype;

class PropertyCache {
/*//
@date 2021-08-09
provides an instance cache for the prototype property attribute structures.
//*/

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static private array
	$Cache = [];


	static public function
	Get(string $ClassName):
	?array {

		if(!array_key_exists($ClassName,static::$Cache))
		return NULL;

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
