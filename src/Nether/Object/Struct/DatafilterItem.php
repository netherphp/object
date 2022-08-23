<?php

namespace Nether\Object\Struct;

use Nether\Object\Datafilter;

class DatafilterItem {

	public mixed
	$Key;

	public mixed
	$Value;

	public Datafilter
	$Source;

	public function
	__Construct(mixed $Value, string $Key, Datafilter $Source) {

		$this->Key = $Key;
		$this->Value = $Value;
		$this->Source = $Source;

		return;
	}

}
