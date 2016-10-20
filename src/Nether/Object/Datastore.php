<?php

namespace Nether\Object;
use \Nether;

use \Exception;
use \Iterator;

class Datastore
implements Iterator {

	protected
	$Count = 0;
	/*//
	@type Int
	maintains how many items are in this datastore so we can avoid doing a
	recount when the dataset gets large. this means you can Count() in
	a loop without the time spent counting lots of things.
	//*/

	////////////////////////////////
	////////////////////////////////

	protected
	$Data = [];
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

	protected
	$Filename = '';
	/*//
	@type string
	the filename that we loaded from and will write to.
	//*/

	public function
	GetFilename():
	String {
		return $this->Filename;
	}

	public function
	SetFilename(String $Filename):
	Self {

		$this->Filename = $Filename;
		return $this;
	}

	////////////////////////////////
	////////////////////////////////

	const FormatPHP  = 1;
	const FormatJSON = 2;

	protected
	$Format = self::FormatPHP;
	/*//
	@type Int
	defines the mode that will be used when serializing this object to write
	it to disk. valid values are php or json.
	//*/

	public function
	GetFormat():
	Int {
		return $this->Format;
	}

	public function
	SetFormat(Int $Format):
	Self {

		switch($Format) {
			case static::FormatPHP:
			case static::FormatJSON: {
				$this->Format = $Format;
				break;
			}
			default: {
				$this->Format = static::FormatPHP;
				break;
			}
		}

		return $this;
	}


	////////////////////////////////
	////////////////////////////////

	protected
	$Sorter = null;
	/*//
	@type Callable
	holds a callable method for sorting.
	//*/

	public function
	GetSorter() {
	/*//
	@todo strict type this when php 7 has nullable types.
	//*/

		return $this->Sorter;
	}

	public function
	SetSorter(Callable $Function):
	Self {
		$this->Sorter = $Function;
		return $this;
	}

	////////////////////////////////
	////////////////////////////////

	protected
	$Title = '';
	/*//
	@type String
	holds a title for this list for whatever purposes you wish to use it for.
	we mainly use it for grouped widgets which contain multiple of these
	datastores.
	//*/

	public function
	GetTitle():
	String {
		return $this->Title;
	}

	public function
	SetTitle(String $Title=''):
	Self {
		$this->Title = $Title;
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
	Append(Array $List, Bool $Keys=FALSE) {
	/*//
	goes through the given array and appends all the data to this dataset. by
	default the array keys are completely ignored. if you need to preseve
	the keys (and ergo overwrite any existing data) set the second argument
	to true.
	//*/

		foreach($List as $Key => $Value) {
			if(!$Keys)
			$this->Push($Value);

			else
			$this->Shove($Key,$Value);
		}

		return;
	}

	public function
	Clear() {
	/*//
	dump the old dataset to start fresh. syntaxual sugar instead of having to
	use $Store->SetData([]);
	//*/

		$this->Data = [];
		$this->Count = 0;
		return $this;
	}

	public function
	Count($Recount=false) {
	/*//
	@return Int
	count how many items are in this datastore. only recount the actual
	dataset if it is demanded, as we assume that the counter is working
	as expected.
	//*/

		// handle underflow attempts.
		if($this->Count < 0)
		$this->Count = 0;

		// return local count by default.
		if(!$Recount)
		return $this->Count;

		// get actual count and cache.
		return $this->Count = count($this->Data);
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
	&Use($Key) {
	/*//
	@argv Mixed KeyName
	@return &Mixed | NULL
	works the same as Get but instead returns a reference to the data so you
	can manipulate non-objects if needed.
	//*/

		if(array_key_exists($Key,$this->Data))
		return $this->Data[$Key];

		return null;
	}

	public function
	HasKey($Key) {
	/*//
	@argv Mixed KeyName
	@return Bool
	returns if this datastore has the requested key.
	//*/

		return array_key_exists($Key,$this->Data);
	}

	public function
	HasValue($Val,$Strict=false) {
	/*//
	@argv Mixed KeyName
	@return Mixed
	returns if this datastore has the requested value. if the value is found
	it will return the key that contains it. if not found it will return a
	boolean false.
	//*/

		return array_search($Val,$this->Data,$Strict);
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

		if($Key === NULL || !array_key_exists($Key,$this->Data))
		$this->Count++;

		if($Key === null)
		$this->Data[] = $Value;

		else
		$this->Data[$Key] = $Value;

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
	Shove($Key,$Value) {
	/*//
	append the specified item to the end of the dataset. if the key already
	exists then the original data will be overwritten in the same place. same
	principal as Push, but syntaxally makes more sense when dealing with
	associative data.
	//*/

		if(!array_key_exists($Key,$this->Data))
		$this->Count++;

		$this->Data[$Key] = $Value;
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

	////////////////////////////////
	////////////////////////////////

	public function
	Shift() {
	/*//
	@return mixed
	performs a standard array shifting operation returning whatever slide off
	the front of the dataset.
	//*/

		$this->Count--;
		return array_shift($this->Data);
	}

	public function
	Unshift($Val) {
	/*//
	@return self
	performs a standard array unshifting operation, shoving the specified value
	onto the front of the array.
	//*/

		$this->Count++;
		array_unshift($this->Data,$Val);
		return $this;
	}

	////////////////////////////////
	////////////////////////////////

	public function
	BlendRight(Array $Input) {
	/*//
	@return self
	works the same as MergeRight, only instead of your input overwriting the
	original data will be kept. your new data will appear at the end of the
	array, and the original data will maintain its original location. numeric
	keys will flat out be appended with the next numeric in the sequence just
	like array_merge.
	//*/

		foreach($Input as $Key => $Val) {
			if(is_int($Key)) {
				$this->Data[] = $Val;
				$this->Count++;
				continue;
			}

			if(!array_key_exists($Key,$this->Data)) {
				$this->Data[$Key] = $Val;
				$this->Count++;
				continue;
			}
		}

		return $this;
	}

	public function
	BlendLeft(Array $Input) {
	/*//
	@return self
	works the same as MergeLeft, only instead of your input overwriting the
	original data will be kept. your new data will appear at the beginning
	of the array pushing the original data down.

	as with MergeLeft, this function is much less performant.
	//*/

		$this->Data = array_reverse($this->Data);

		foreach(array_reverse($Input) as $Key => $Val) {
			if(is_int($Key)) {
				$this->Data[] = $Val;
				$this->Count++;
				continue;
			}

			if(!array_key_exists($Key,$this->Data)) {
				$this->Data[$Key] = $Val;
				$this->Count++;
				continue;
			}
		}

		$this->Data = array_reverse($this->Data);
		return $this;
	}

	public function
	MergeRight(Array $Input) {
	/*//
	@return self
	appends the input to the dataset. if there are conflicting assoc keys, the
	input data here will override whatever already existed. numeric keys will
	be appended no matter what.

	all new data will appear at the end of the array. any data that had
	conflicting assoc keys will remain in the sequence position that it was
	already in.
	//*/

		$this->Data = array_merge(
			$this->Data,
			$Input
		);

		$this->Count = count($this->Data);
		return $this;
	}

	public function
	MergeLeft(Array $Input) {
	/*//
	@return self
	appends the input to the dataset. same as MergeRight but will appear to add
	your data to the start of the array, and still overwriting. the union
	operator is not good for this instance because it doesnt behave as a proper
	merge, allowing numerical keys to cause collisions, and this needs to
	accurately mirror standard array_merge behaviour.

	all new data will appear at the beginning of the array. any data that had
	conflicting assoc keys will remain in the sequence position that it was
	already in.

	this is not performant. use sparingly. it is suggested you attempt to
	structure your data such that order doesnt really matter, or your code
	such that data can always be appended in the most cheap way possible.
	//*/

		$this->Data = array_reverse(array_merge(
			array_reverse($this->Data),
			array_reverse($Input)
		));

		$this->Count = count($this->Data);
		return $this;
	}

	////////////////////////////////
	////////////////////////////////

	// item manipulation api for the data.

	public function
	Each(callable $Function) {
	/*//
	@argv callable Func
	run the specified function against every single thing in the list. it is
	is slower than running a direct foreach() on the property but it sure makes
	for some nice looking shit sometimes.
	//*/

		foreach($this->Data as $Key => &$Value)
		$Function($Value,$Key);

		return $this;
	}

	public function
	Sort(Callable $Function=null, Bool $Presort=false):
	Self {
	/*//
	sort the dataset by the function defined in this datastore's
	sorter property. if a function is defined as an argument here
	then we will use that instead of the sorter property. if presort
	is enabled then the specified sort function will be used before
	the defined sorter.
	//*/

		if(is_callable($Function)) {
			uasort($this->Data,$Function);
			if(!$Presort) return $this;
		}

		if(is_callable($this->Sorter)) {
			uasort($this->Data,$this->Sorter);
			return $this;
		}

		return $this;
	}

	////////////////////////////////
	////////////////////////////////

	public function
	Read(String $Filename=NULL) {

		if($Filename === NULL) $Filename = $this->Filename;
		else $this->Filename = $Filename;

		if(!$Filename)
		throw new Exception('No filename specified.');

		$Basename = basename($Filename);
		$Ext = NULL;

		if(strpos($Basename,'.') !== FALSE)
		$Ext = strtolower(explode('.',$Basename,2)[1]);

		////////
		////////

		if(!file_exists($Filename))
		throw new Exception("File {$Basename} not found.");

		if(!is_readable($Filename))
		throw new Exception("File {$Basename} is not readable.");

		////////
		////////

		if($Ext === 'json' || $this->Format === static::FormatJSON)
		$this->Data = json_decode(file_get_contents($Filename));

		elseif($Ext === 'phson' || $this->Format === static::FormatPHP)
		$this->Data = unserialize(file_get_contents($Filename));

		////////
		////////

		if(is_object($this->Data))
		$this->Data = (array)$this->Data;

		return $this;
	}

	public function
	Write(String $Filename=NULL) {
	/*//
	write this datastructure to disk.
	//*/

		if($Filename === NULL)
		$Filename = $this->Filename;

		$Error = NULL;
		$Dirname = dirname($Filename);
		$Basename = basename($Filename);
		$Ext = NULL;

		if(strpos($Basename,'.') !== FALSE)
		$Ext = strtolower(explode('.',$Basename,2)[1]);

		////////
		////////

		if(!file_exists($Filename)) {
			if(!is_dir($Dirname) && !@mkdir($Dirname,0777,TRUE))
			$Error = ["Unable to create directory ({$Dirname})",3];

			elseif(!is_writable($Dirname))
			$Error = ["Unable to create file ({$Dirname}) not writable.",1];
		}

		else {
			if(!is_writable($Filename))
			$Error = ["Unable to write to file ({$Basename}).",2];
		}

		if($Error)
		throw new Exception(...$Error);

		////////
		////////

		if($Ext === 'json' || $this->Format === static::FormatJSON)
		$Data = json_encode($this->Data,JSON_PRETTY_PRINT);

		else
		$Data = serialize($this->Data);

		////////
		////////

		file_put_contents($Filename,$Data);

		////////
		////////

		return $this;
	}

	public static function
	GetFromFile($Filename) {

		return (new static)->Read($Filename);
	}

}
