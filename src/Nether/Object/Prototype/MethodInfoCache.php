<?php

namespace Nether\Object\Prototype;

class MethodInfoCache {
/*//
@date 2021-08-09
provides a static cache for the prototype method attribute structures.
//*/

	static public array
	$Cache = [];

	static public function
	Get(string $ClassName):
	?array {
	/*//
	@date 2021-08-08
	@mopt isset
	//*/

		if(!isset(static::$Cache[$ClassName]))
		return NULL;

		return static::$Cache[$ClassName];
	}

	static public function
	Has(string $ClassName):
	bool {
	/*//
	@date 2021-08-08
	@mopt isset
	//*/

		return isset(static::$Cache[$ClassName]);
	}

	static public function
	Set(string $ClassName, array $MethodMap):
	array {
	/*//
	@date 2021-08-08
	//*/

		static::$Cache[$ClassName] = $MethodMap;
		return $MethodMap;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

}
