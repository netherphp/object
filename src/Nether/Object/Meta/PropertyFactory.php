<?php

namespace Nether\Object\Meta;

use Nether\Object\Prototype\PropertyInfo;
use Nether\Object\Prototype\PropertyInfoInterface;

use Attribute;
use ReflectionProperty;
use ReflectionAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyFactory
extends PropertyObjectify {

	public string
	$Callable;

	public function
	__Construct(string $Callable, string $Source, ...$Args) {

		$this->Callable = $Callable;
		$this->Source = $Source;
		$this->Args = $Args;

		return;
	}

	public function
	OnPropertyInfo(PropertyInfo $Attrib, ReflectionProperty $RefProp, ReflectionAttribute $RefAttrib):
	static {

		$Attrib->Objectify = $this;

		return $this;
	}

}
