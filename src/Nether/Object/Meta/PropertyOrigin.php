<?php

namespace Nether\Object\Meta;

use Attribute;
use Nether\Object\Prototype\AttributeInterface;
use Nether\Object\Prototype\PropertyAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyOrigin
implements AttributeInterface {
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
	OnPropertyAttributes(PropertyAttributes $Attrib):
	static {

		$Attrib->Origin = $this->Name;
		return $this;
	}
}
