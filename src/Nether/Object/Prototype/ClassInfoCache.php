<?php

namespace Nether\Object\Prototype;

class ClassInfoCache {
/*//
@date 2022-08-10
provides a static cache for the prototype class info structures.
//*/

	static protected array
	$Cache = [];

	static public function
	Get(string $ClassName):
	?ClassInfo {
	/*//
	@date 2022-08-10
	//*/

		if(!isset(static::$Cache[$ClassName]))
		return NULL;

		return static::$Cache[$ClassName];
	}

	static public function
	Drop(string $ClassName):
	void {
	/*//
	@2022-08-12
	//*/

		if(array_key_exists($ClassName, static::$Cache))
		unset(static::$Cache[$ClassName]);

		return;
	}

	static public function
	Has(string $ClassName):
	bool {
	/*//
	@date 2022-08-10
	//*/

		return isset(static::$Cache[$ClassName]);
	}

	static public function
	Set(string $ClassName, ClassInfo $ClassInfo):
	ClassInfo {
	/*//
	@date 2022-08-10
	//*/

		static::$Cache[$ClassName] = $ClassInfo;
		return $ClassInfo;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

}
