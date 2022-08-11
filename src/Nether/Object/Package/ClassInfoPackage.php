<?php

namespace Nether\Object\Package;

use ReflectionClass;
use Nether\Object\Prototype\ClassInfo;
use Nether\Object\Prototype\ClassInfoCache;

trait ClassInfoPackage {

	static public function
	FetchClassInfo(bool $Init=TRUE):
	ClassInfo {
	/*//
	@date 2021-08-11
	return a list of all the attributes on this class.
	//*/

		return new ClassInfo(new ReflectionClass(static::class));
	}


	static public function
	GetClassInfo():
	ClassInfo {
	/*//
	@date 2022-08-11
	//*/

		if(ClassInfoCache::Has(static::class))
		return ClassInfoCache::Get(static::class);

		return ClassInfoCache::Set(
			static::class,
			static::FetchClassInfo()
		);
	}

}
