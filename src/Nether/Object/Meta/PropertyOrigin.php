<?php

namespace Nether\Object\Meta;

use Attribute;
use Stringable;

#[Attribute]
class PropertyOrigin {

	public string
	$Name;

	public function
	__Construct(string $Name) {
		$this->Name = $Name;
		return;
	}

}
