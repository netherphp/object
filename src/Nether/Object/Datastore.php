<?php

namespace Nether\Object;
use \Nether;
use \Iterator;

class Datastore
implements Iterator {

	protected $Count = 0;
	/*//
	@type Int
	maintains how many items are in this datastore so we can avoid doing a
	recount when the dataset gets large. this means you can Count() in
	a loop without the time spent counting lots of things.
	//*/

	protected $Data = [];
	/*//
	@type Array
	holds the data that we will operate upon.
	//*/

	public function
	GetData() {
	/*//
	@return Array
	get the literal array storing the data.
	//*/

		return $this->Data;
	}

	public function
	SetData(array $Input) {
	/*//
	@argv Array InputData
	@return self
	force the data array to be the specified input.
	//*/

		$this->Data = $Input;
		$this->Count = count($this->Data);

		return $this;
	}

	////////////////////////////////
	////////////////////////////////

	// implementation of the iterator interface.
	// these allow the object to be used in things such as foreach loops.

	public function
	Current() {
	/*//
	@return Mixed
	allow the object to be queried for the current array item.
	//*/

		return current($this->Data);
	}

	public function
	Key() {
	/*//
	@return Int | String
	allow the object to to be queried for the current array key.
	//*/

		return key($this->Data);
	}

	public function
	Next() {
	/*//
	@return Mixed
	allow the object to be queried for the next item in the array wich will
	also progress the array's pointer.
	//*/

		return next($this->Data);
	}

	public function
	Rewind() {
	/*//
	@return Mixed
	allow the object to have its data iteration reset.
	//*/

		return reset($this->Data);
	}

	public function
	Valid() {
	/*//
	@return Bool
	allow the object to be queried if we are still within range of the data.
	//*/

		return (key($this->Data) !== null);
	}

	////////////////////////////////
	////////////////////////////////

	// item management api for the datastore.

	public function
	Count($Recount=false) {
	/*//
	@return Int
	count how many items are in this datastore. only recount the actual
	dataset if it is demanded, as we assume that the counter is working
	as expected.
	//*/

		if(!$Recount)
		return $this->Count;

		return count($this->Data);
	}

	public function
	Get($Key) {
	/*//
	@argv Mixed KeyName
	@return Mixed | null
	returns the data by the specified key name. if data with that key did not
	exist then it will return null. keep this in mind if you are also
	inserting nulls into the dataset.
	//*/

		if(array_key_exists($Key,$this->Data))
		return $this->Data[$Key];

		return null;
	}

	public function
	Merge($Input) {
	/*//
	@argv Array InputData
	@return self
	merges the specified array into the dataset. if it has associative keys
	then what you get is the newer version. numeric keys however will be
	appended to the set.
	//*/

		$this->Data = array_merge(
			$this->Data,
			$Input
		);

		$this->Count = count($this->Data);

		return $this;
	}

	public function
	Pop() {
	/*//
	@return Mixed | null
	return and remove the last value on the array. if the array is empty it
	will return null. keep this in mind if you are also inserting nulls into
	the dataset.
	//*/

		if($this->Count > 0) {
			$this->Count--;
			return array_pop($this->Data);
		}

		return null;
	}

	public function
	Push($Value,$Key=null) {
	/*//
	@argv Mixed Input
	@return self
	appends the specified item to the end of the dataset. if a key is
	specified and a data for that key already existed, then it will be
	overwritten with the new data.
	//*/

		if($Key === null)
		$this->Data[] = $Value;

		else
		$this->Data[$Key] = $Value;

		$this->Count++;
		return $this;
	}

	public function
	Reindex() {
	/*//
	@return self
	reindex the data array to remove gaps in the numeric keys while still
	preserving any string keys that existed.
	//*/

		$Data = [];
		foreach($this->Data as $Key => $Value) {
			if(is_int($Key)) $Data[] = $Value;
			else $Data[$Key] = $Value;
		}

		$this->Data = $Data;
		return $this;
	}

	public function
	Remove($Key) {
	/*//
	@argv Mixed KeyName
	@return self
	removes the data by the specified key name if it exists. if not then
	nothing happens and you can just go on your way.
	//*/

		if(array_key_exists($Key,$this->Data)) {
			unset($this->Data[$Key]);
			$this->Count--;
		}

		return $this;
	}

}
