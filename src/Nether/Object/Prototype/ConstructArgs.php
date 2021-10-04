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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	DefaultHas(mixed $Key):
	bool {

		return (
			is_array($this->Defaults)
			&& isset($this->Defaults[$Key])
		);
	}

	public function
	DefaultExists(mixed $Key):
	bool {

		return (
			is_array($this->Defaults)
			&& array_key_exists($Key, $this->Defaults)
		);
	}

	public function
	DefaultGet(mixed $Key):
	mixed {

		if(!is_array($this->Defaults))
		return NULL;

		return $this->Defaults[$Key];
	}

	public function
	InputHas(mixed $Key):
	bool {

		return (
			is_array($this->Input)
			&& isset($this->Input[$Key])
		);
	}

	public function
	InputExists(mixed $Key):
	bool {

		return (
			is_array($this->Input)
			&& array_key_exists($Key, $this->Input)
		);
	}

	public function
	InputGet(mixed $Key):
	mixed {


		if(!is_array($this->Input))
		return NULL;

		return $this->Input[$Key];
	}

}
