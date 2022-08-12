<?php

namespace Nether\Object\Prototype;

class PropertyInfoCache {
/*//
@date 2021-08-09
provides a static cache for the prototype property attribute structures.
//*/

	static public array
	$Cache = [];

	static public function
	Get(string $ClassName):
	?array {

		if(!isset(static::$Cache[$ClassName]))
		return NULL;

		return static::$Cache[$ClassName];
	}

	static public function
	Drop(string $ClassName):
	void {

		if(array_key_exists($ClassName, static::$Cache))
		unset(static::$Cache[$ClassName]);

		return;
	}

	static public function
	Has(string $ClassName):
	bool {

		return isset(static::$Cache[$ClassName]);
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
