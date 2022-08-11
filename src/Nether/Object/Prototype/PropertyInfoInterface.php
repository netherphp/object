<?php

namespace Nether\Object\Prototype;

use Nether\Object\Prototype\MethodInfo;
use ReflectionProperty;
use ReflectionAttribute;

interface PropertyInfoInterface {
/*//
@date 2021-08-11
currently the only good way to filter attributes our framework owns is to give
them interfaces and to check that after asking reflection for the list.
//*/

	public function
	OnPropertyInfo(PropertyInfo $Attrib, ReflectionProperty $RefProp, ReflectionAttribute $RefAttrib);
	/*//
	@date 2021-08-11
	implement this method in your attribute to make it do something when
	this property is indexed by the prototype system.
	//*/

}
