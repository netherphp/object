<?php

namespace Nether\Object\Struct;

use Nether\Object\Datafilter;

class DatafilterCallable {

	public mixed
	$Func;

	public ?array
	$Argv;

	public function
	__Construct(callable $Func, ?array $Argv=NULL) {

		$this->Func = $Func;
		$this->Argv = $Argv;

		return;
	}

	public function
	__Invoke(mixed $Val, string $Key, Datafilter $Input):
	mixed {

		return ($this->Func)($Val, $Key, $Input, ...($this->Argv ?? []));
	}

}
