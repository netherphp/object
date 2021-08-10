<?php

namespace Nether\Object\Prototype;

class ConstructArgs {
/*//
@date 2021-08-09
wrap the arguments given to a prototype based object so they can be then
passed around in a single bundle later on.
//*/

	public ?array
	$Raw;

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
	__Construct(array|object|NULL $Raw, array|object|NULL $Defaults, int $Flags, array $Props) {

		// the main data we want to transport.
		$this->Raw = $Raw;
		$this->Defaults = $Defaults;
		$this->Flags = $Flags;
		$this->Properties = $Props;

		// ask some questions once that get reused in loopses.
		$this->StrictDefault = ($Flags & Flags::StrictDefault) !== 0;
		$this->StrictInput = ($Flags & Flags::StrictInput) !== 0;
		$this->CullUsingDefault = ($Flags & Flags::CullUsingDefault) !== 0 && $Defaults;

		return;
	}

}
