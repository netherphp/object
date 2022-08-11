<?php

namespace Nether\Object\Prototype;

class ClassInfoCache {
/*//
@date 2021-08-09
provides a static cache for the prototype class info structures.
//*/

	static public array
	$Cache = [];

	static public function
	Get(string $ClassName):
	?ClassInfo {
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
	Set(string $ClassName, ClassInfo $ClassInfo):
	ClassInfo {
	/*//
	@date 2021-08-08
	//*/

		static::$Cache[$ClassName] = $ClassInfo;
		return $ClassInfo;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

}
