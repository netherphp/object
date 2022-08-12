<?php

namespace Nether\Object;

use Exception;

class Deepstore
extends Datastore {
/*//
@date 2016-04-22
//*/

	////////////////////////////////////////////////////////////////
	// Magic Methods ///////////////////////////////////////////////

	public function
	__Get(mixed $Key):
	mixed {
	/*//
	@date 2016-04-22
	handle when you attempt to read data out directly as a property. if the
	data in question is an array then it will convert that array into a
	datastore on the fly.
	//*/

		// data not found return an empty set.
		if(!array_key_exists($Key,$this->Data))
		return $this->Data[$Key] = (new static);

		// data found and is array wrap it.
		if(is_array($this->Data[$Key]))
		return $this->Data[$Key] = (new static)->SetData($this->Data[$Key]);

		// data found return it.
		return $this->Data[$Key];
	}

	public function
	__Set(mixed $Key, mixed $Value):
	void {
	/*//
	@date 2016-04-22
	handle when you attempt to write data directly to a property. at this point
	we will not actually anything with it. using the Get method right after
	this will return an array as you gave it. it is not fussed with until you
	attempt to read it back out directly.
	//*/

		$this->Data[$Key] = $Value;
		return;
	}

	public function
	__Call(mixed $Key, mixed $Args):
	mixed {
	/*//
	@date 2016-04-22
	i cannot believe this works. i mean, i can, but, still, i did not intend
	for this to be a feature of this datastore until i had this hillarious idea
	of how bad i can be.
	//*/

		if(!array_key_exists($Key, $this->Data))
		throw new Exception("{$Key} is not an existing callable.", 1);

		if(!is_callable($this->Data[$Key]))
		throw new Exception("{$Key} is not callable.", 2);

		return $this->Data[$Key]->Call($this,...$Args);
	}

}
