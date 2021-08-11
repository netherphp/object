<?php

namespace Nether\Object\Prototype;

class ConstructArgs {
/*//
@date 2021-08-09
wrap the arguments given to a prototype based object so they can be then
passed around in a single bundle later on.
//*/

	public ?array
	$Input;

	public ?array
	$Defaults;

	public int
	$Flags;

	public array
	$Properties;

	public bool
	$StrictDefault = FALSE;

	public bool
	$StrictInput = FALSE;

	public function
	__Construct(?array $Input, ?array $Defaults, int $Flags, array $Props) {
	/*//
	@date 2021-08-09
	//*/

		$this->Input = $Input;
		$this->Defaults = $Defaults;
		$this->Flags = $Flags;
		$this->Properties = $Props;

		return;
	}

}
