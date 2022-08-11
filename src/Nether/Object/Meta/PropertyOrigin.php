<?php

namespace Nether\Object\Meta;

use Nether\Object\Prototype\PropertyInfo;
use Nether\Object\Prototype\PropertyInfoInterface;

use Attribute;
use ReflectionProperty;
use ReflectionAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyOrigin
implements PropertyInfoInterface {
/*//
@date 2021-08-05
@related Nether\Object\Prototype::__Construct
when attached to a class property with a single string argument that will
tell the prototype object to pull the data stored in the arguement and to put
it into the property this is attached to.
//*/

	public string
	$Name;

	public function
	__Construct(string $Name) {

		$this->Name = $Name;
		return;
	}

	public function
	OnPropertyInfo(PropertyInfo $Attrib, ReflectionProperty $RefProp, ReflectionAttribute $RefAttrib):
	static {

		$Attrib->Origin = $this->Name;
		return $this;
	}
}
