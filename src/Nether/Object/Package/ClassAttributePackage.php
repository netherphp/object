<?php

namespace Nether\Object\Package;

use ReflectionClass;

trait ClassAttributePackage {

	static public function
	FetchClassAttributes(bool $Init=TRUE):
	array {
	/*//
	@date 2021-08-24
	return a list of all the attributes on this class.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$Attrib = NULL;
		$Output = [];

		foreach($RefClass->GetAttributes() as $Attrib)
		$Output[] = $Init ? $Attrib->NewInstance() : $Attrib;

		return $Output;
	}

}
