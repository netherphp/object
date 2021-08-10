<?php

namespace Nether\Object\Meta;

use Attribute;

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
