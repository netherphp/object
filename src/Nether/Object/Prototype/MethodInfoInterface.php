<?php

namespace Nether\Object\Prototype;

use Nether\Object\Prototype\MethodInfo;
use ReflectionMethod;
use ReflectionAttribute;

interface MethodInfoInterface {
/*//
@date 2022-08-10
currently the only good way to filter attributes our framework owns is to give
them interfaces and to check that after asking reflection for the list.
//*/

	public function
	OnMethodInfo(MethodInfo $Attrib, ReflectionMethod $RefMethod, ReflectionAttribute $RefAttrib);
	/*//
	@date 2022-08-10
	implement this method in your attribute to make it do something when
	this method is indexed by the prototype system.
	//*/

}
