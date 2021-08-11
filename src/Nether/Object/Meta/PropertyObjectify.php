<?php

namespace Nether\Object\Meta;

use Attribute;
use Nether\Object\Prototype\AttributeInterface;
use Nether\Object\Prototype\PropertyAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyObjectify
implements AttributeInterface {
/*//
@date 2021-08-09
@related Nether\Object\Prototype::__Construct
when attached to a class property, when the parent object is constructed this
property will get a fresh new instance of whatever type this property is
defined as. arguments given to the attribute will be passed along as arguments
to the object being constructed for that property.
//*/

	public array
	$Args;

	public function
	__Construct(...$Args) {

		$this->Args = $Args;
		return;
	}

	public function
	OnReady(PropertyAttributes $Attrib):
	static {

		$Attrib->Objectify = $this;
		return $this;
	}

}
