<?php

namespace Nether\Object;

use Exception;
use Iterator;
use ArrayAccess;
use Countable;
use JsonSerializable;
use ReturnTypeWillChange;

class Datastore
implements Iterator, ArrayAccess, Countable, JsonSerializable {
/*//
@date 2015-12-02
//*/

	const
	FormatPHP  = 1,
	FormatJSON = 2;

	protected string
	$Title = '';

	protected string
	$Filename = '';

	protected int
	$Format = self::FormatPHP;

	protected bool
	$FullJSON = FALSE;

	protected bool
	$FullDebug = FALSE;

	protected mixed
	$Sorter = NULL;

	protected array
	$Data = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(?array $Input=NULL) {
	/*//
	@date 2015-12-02
	//*/

		if($Input !== NULL)
		$this->SetData($Input);

		return;
	}

	public function
	__DebugInfo():
	array {

		if(!$this->FullDebug)
		return $this->Data;

		return (array)$this;
	}

	public function
	__Invoke():
	array {

		return $this->Data;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetData():
	array {
	/*//
	@date 2015-12-02
	//*/

		return $this->Data;
	}

	public function
	&GetDataRef():
	array {
	/*//
	@date 2015-12-02
	//*/

		return $this->Data;
	}

	public function
	SetData(array $Input):
	static {
	/*//
	@date 2015-12-02
	//*/

		$this->Data = $Input;
		return $this;
	}

	public function
	GetFilename():
	string {
	/*//
	@date 2015-12-02
	//*/

		return $this->Filename;
	}

	public function
	SetFilename(string $Filename):
	static {
	/*//
	@date 2015-12-02
	//*/

		$this->Filename = $Filename;
		return $this;
	}

	public function
	GetFormat():
	int {
	/*//
	@date 2015-12-02
	//*/

		return $this->Format;
	}

	public function
	SetFormat(int $Format):
	static {
	/*//
	@date 2015-12-02
	//*/

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

	public function
	GetFullDebug():
	bool {
	/*//
	@date 2021-08-18
	//*/

		return $this->FullDebug;
	}

	public function
	SetFullDebug(bool $Val):
	static {
	/*//
	@date 2021-08-18
	//*/

		$this->FullDebug = $Val;
		return $this;
	}

	public function
	GetFullJSON():
	bool {
	/*//
	@date 2021-08-18
	//*/

		return $this->FullJSON;
	}

	public function
	SetFullJSON(bool $Val):
	static {
	/*//
	@date 2021-08-18
	//*/

		$this->FullJSON = $Val;
		return $this;
	}

	public function
	GetSorter() {
	/*//
	@date 2016-02-25
	//*/

		return $this->Sorter;
	}

	public function
	SetSorter(callable $Function):
	static {
	/*//
	@date 2016-02-25
	//*/

		$this->Sorter = $Function;
		return $this;
	}

	public function
	GetTitle():
	string {
	/*//
	@date 2016-03-25
	//*/

		return $this->Title;
	}

	public function
	SetTitle(string $Title=''):
	static {
	/*//
	@date 2016-03-25
	//*/

		$this->Title = $Title;
		return $this;
	}

	////////////////////////////////////////////////////////////////
	// implements ArrayAccess //////////////////////////////////////

	public function
	OffsetExists(mixed $Key):
	bool {
	/*//
	@date 2015-12-02
	//*/

		return array_key_exists($Key,$this->Data);
	}

	public function
	OffsetGet(mixed $Key):
	mixed {
	/*//
	@date 2015-12-02
	//*/

		return $this->Data[$Key];
	}

	public function
	OffsetSet(mixed $Key, mixed $Value):
	void {
	/*//
	@date 2015-12-02
	//*/

		// enables $Dataset[] = 'val';

		if($Key === NULL)
		$this->Data[] = $Value;

		// enables $Dataset['key'] = 'val';

		else
		$this->Data[$Key] = $Value;

		return;
	}

	public function
	OffsetUnset($Key):
	void {
	/*//
	@date 2015-12-02
	//*/

		unset($this->Data[$Key]);
		return;
	}

	////////////////////////////////////////////////////////////////
	// implements Iterator /////////////////////////////////////////

	public function
	Current():
	mixed {
	/*//
	@date 2015-12-02
	//*/

		return current($this->Data);
	}

	public function
	Key():
	int|string {
	/*//
	@date 2015-12-02
	//*/

		return key($this->Data);
	}

	#[ReturnTypeWillChange]
	public function
	Next():
	mixed {
	/*//
	@date 2015-12-02
	//*/

		return next($this->Data);
	}

	#[ReturnTypeWillChange]
	public function
	Rewind():
	mixed {
	/*//
	@date 2015-12-02
	//*/

		return reset($this->Data);
	}

	public function
	Valid():
	bool {
	/*//
	@date 2015-12-02
	//*/

		return (key($this->Data) !== NULL);
	}

	////////////////////////////////////////////////////////////////
	// implements JsonSerializable /////////////////////////////////

	public function
	JsonSerialize():
	mixed {
	/*//
	@date 2021-08-18
	//*/

		if(!$this->FullJSON)
		return $this->Data;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	// General API /////////////////////////////////////////////////

	public function
	Count():
	int {
	/*//
	@date 2015-12-02
	count how many items are in this datastore.
	//*/

		return count($this->Data);
	}

	public function
	Each(callable $Function, ?array $Argv=NULL):
	static {
	/*//
	@date 2016-12-02
	run the specified function against every single thing in the list. it is
	is slower than running a direct foreach() on the property but it sure makes
	for some nice looking shit sometimes.
	//*/

		$Key = NULL;
		$Value = NULL;

		foreach($this->Data as $Key => &$Value)
		$Function($Value,$Key,$this,...($Argv??[]));

		return $this;
	}

	public function
	Get(mixed $Key):
	mixed {
	/*//
	@date 2015-12-02
	returns the data by the specified key name. if data with that key did not
	exist then it will return null. keep this in mind if you are also
	inserting nulls into the dataset.
	//*/

		if(array_key_exists($Key,$this->Data))
		return $this->Data[$Key];

		return NULL;
	}

	public function
	&GetRef(mixed $Key):
	mixed {
	/*//
	@date 2015-12-02
	works the same as Get but instead returns a reference to the data so you
	can manipulate non-objects if needed.
	//*/

		if(array_key_exists($Key,$this->Data))
		return $this->Data[$Key];

		return NULL;
	}

	public function
	GetFirstKey():
	mixed {
	/*//
	@date 2021-09-13
	get what the first key in this dataset is.
	//*/

		return array_key_first($this->Data);
	}

	public function
	GetLastKey():
	mixed {
	/*//
	@date 2021-09-13
	get what the last key in this dataset is.
	//*/

		return array_key_last($this->Data);
	}

	public function
	HasKey(mixed $Key):
	bool {
	/*//
	@date 2015-12-02
	returns if this datastore has the requested key.
	//*/

		return array_key_exists($Key,$this->Data);
	}

	public function
	HasValue(mixed $Val, bool $Strict=FALSE):
	bool {
	/*//
	@date 2015-12-02
	returns if this datastore has the requested value. if the value is found
	it will return the key that contains it. if not found it will return a
	boolean false.
	//*/

		return array_search($Val, $this->Data, $Strict) !== FALSE;
	}

	public function
	IsFirstKey(mixed $Key):
	bool {
	/*//
	@date 2021-09-13
	ask if this is the first key.
	//*/

		return ($Key === array_key_first($this->Data));
	}

	public function
	IsLastKey(mixed $Key):
	bool {
	/*//
	@date 2021-09-13
	ask if this is the last key.
	//*/

		return ($Key === array_key_last($this->Data));
	}

	public function
	Keys():
	array {
	/*//
	@date 2021-09-20
	//*/

		return array_keys($this->Data);
	}

	public function
	Values():
	array {
	/*//
	@date 2021-01-05
	fetches a clean indexed copy of the data via array_values.
	//*/

		return array_values($this->Data);
	}

	////////////////////////////////////////////////////////////////
	// Manipulation API ////////////////////////////////////////////

	public function
	Accumulate(mixed $Initial, callable $Function):
	mixed {
	/*//
	@date 2021-04-15
	pass the initial value through a chained callable game and return the
	resulting value.
	//*/

		// i absolutely loath that its called array_reduce when at no point
		// does it reduce the data set. in reality what this function does
		// is play the telephone game with an initial value. that is why
		// it is called accumulate here.

		return array_reduce($this->Data,$Function,$Initial);
	}

	public function
	Clear():
	static {
	/*//
	@date 2016-03-19
	dump the old dataset to start fresh. syntaxual sugar instead of having to
	use $Store->SetData([]);
	//*/

		unset($this->Data);
		$this->Data = [];

		return $this;
	}

	public function
	Distill(callable $FilterFunc):
	Datastore {
	/*//
	@date 2020-10-22
	return a new datastore of the result of an array filter.
	//*/

		return new static(
			array_filter($this->Data,$FilterFunc)
		);
	}

	public function
	Filter(callable $FilterFunc):
	self {
	/*//
	@date 2020-05-27
	alter the current dataset with the result of an array filter.
	//*/

		$this->Data = array_filter($this->Data,$FilterFunc,ARRAY_FILTER_USE_BOTH);

		return $this;
	}

	public function
	Join(string $Delimiter=' '):
	string {
	/*//
	@date 2022-07-29
	join and return the dataset together with the specified delimiter.
	note you should probably map or remap it to values that you know will
	actually be joinable prior.
	//*/

		return join($Delimiter, $this->Data);
	}

	public function
	Map(callable $FilterFunc):
	Datastore {
	/*//
	@date 2020-05-27
	return a new datastore of the result of an array map.
	//*/

		return new static(
			array_map($FilterFunc,$this->Data)
		);
	}

	public function
	MapKeys(callable $Func):
	Datastore {
	/*//
	@date 2021-09-20
	same as RemapKeys except it returns a new datastore of the result.
	//*/

		$Output = [];
		$Result = NULL;
		$Key = NULL;
		$Val = NULL;

		foreach($this->Data as $Key => $Val) {
			$Result = $Func($Key,$Val,$this);

			if(is_array($Result))
			$Output[key($Result)] = current($Result);
		}

		return new Datastore($Output);
	}

	public function
	Pop():
	mixed {
	/*//
	@date 2015-12-02
	return and remove the last value on the array. if the array is empty it
	will return null. keep this in mind if you are also inserting nulls into
	the dataset.
	//*/

		return array_pop($this->Data);
	}

	public function
	Push(mixed $Value, mixed $Key=NULL):
	static {
	/*//
	@date 2015-12-02
	appends the specified item to the end of the dataset. if a key is
	specified and a data for that key already existed, then it will be
	overwritten with the new data.
	//*/

		if($Key === NULL)
		$this->Data[] = $Value;

		else
		$this->Data[$Key] = $Value;

		return $this;
	}

	public function
	Reindex():
	static {
	/*//
	@date 2015-12-02
	reindex the data array to remove gaps in the numeric keys while still
	preserving any string keys that existed.
	//*/

		$Key = NULL;
		$Value = NULL;
		$Data = [];

		foreach($this->Data as $Key => $Value) {
			if(is_int($Key)) $Data[] = $Value;
			else $Data[$Key] = $Value;
		}

		$this->Data = $Data;
		return $this;
	}

	public function
	RemapKeys(callable $Func):
	static {
	/*//
	@date 2021-09-20
	rekey and remap this dataset by returning an array with a single
	element from the callback. the key of the result is where the data will
	be moved to, with the data of the result being said data. if false or
	null is returned instead, it will also filter that item out of the
	array. modifies this datastore.

	based on a gist by jasand found randomly one day.
	https://gist.github.com/jasand-pereza/84ecec7907f003564584
	//*/

		$Output = [];
		$Result = NULL;
		$Key = NULL;
		$Val = NULL;

		foreach($this->Data as $Key => $Val) {
			$Result = $Func($Key,$Val,$this);

			if(is_array($Result))
			$Output[key($Result)] = current($Result);
		}

		$this->SetData($Output);

		return $this;
	}

	public function
	Remap(callable $FilterFunc):
	static {
	/*//
	@date 2020-05-27
	alter the current dataset using the array_map filtering.
	//*/

		$this->Data = array_map($FilterFunc,$this->Data);

		return $this;
	}

	public function
	Remove(mixed $Key):
	static {
	/*//
	@date 2015-12-02
	removes the data by the specified key name if it exists. if not then
	nothing happens and you can just go on your way.
	//*/

		if(array_key_exists($Key,$this->Data))
		unset($this->Data[$Key]);

		return $this;
	}

	public function
	Revalue():
	static {
	/*//
	@date 2021-01-05
	rebuilds the dataset with clean indexes via array_values.
	//*/

		$this->Data = array_values($this->Data);
		return $this;
	}

	public function
	Shove(mixed $Key, mixed $Value):
	static {
	/*//
	@date 2015-12-02
	append the specified item to the end of the dataset. if the key already
	exists then the original data will be overwritten in the same place. same
	principal as Push, but syntaxally makes more sense when dealing with
	associative data.
	//*/

		$this->Data[$Key] = $Value;
		return $this;
	}

	public function
	Shuffle():
	static {
	/*//
	@date 2021-02-22
	randomize the array in-place.
	//*/

		shuffle($this->Data);
		return $this;
	}

	public function
	Shift():
	mixed {
	/*//
	@date 2015-12-02
	performs a standard array shifting operation returning whatever slide off
	the front of the dataset.
	//*/

		return array_shift($this->Data);
	}

	public function
	Sort(callable $Function=NULL, bool $Presort=FALSE):
	static {
	/*//
	@date 2015-12-02
	sort the dataset by the function defined in this datastore's
	sorter property. if a function is defined as an argument here
	then we will use that instead of the sorter property. if presort
	is enabled then the specified sort function will be used before
	the defined sorter.
	//*/

		if($Function === NULL) {
			asort($this->Data);

			if(!$Presort)
			return $this;
		}

		if(is_callable($Function)) {
			uasort($this->Data, $Function);

			if(!$Presort)
			return $this;
		}

		if(is_callable($this->Sorter)) {
			uasort($this->Data, $this->Sorter);
			return $this;
		}

		return $this;
	}

	public function
	SortKeys(callable $Function=NULL, bool $Presort=FALSE):
	static {
	/*//
	@date 2022-11-23
	//*/

		if($Function === NULL) {
			ksort($this->Data);

			if(!$Presort)
			return $this;
		}

		if(is_callable($Function)) {
			uksort($this->Data, $Function);

			if(!$Presort)
			return $this;
		}

		if(is_callable($this->Sorter)) {
			uksort($this->Data, $this->Sorter);
			return $this;
		}

		return $this;
	}

	public function
	Unshift(mixed $Val):
	static {
	/*//
	@date 2015-12-02
	performs a standard array unshifting operation, shoving the specified value
	onto the front of the array.
	//*/

		array_unshift($this->Data,$Val);
		return $this;
	}

	////////////////////////////////////////////////////////////////
	// Merging API /////////////////////////////////////////////////

	public function
	MergeRight(array $Input):
	static {
	/*//
	@date 2016-03-18
	appends the input to the dataset. if there are conflicting assoc keys, the
	input data here will override whatever already existed. numeric keys will
	be appended no matter what. all new data will appear at the end of the
	array. any data that had conflicting assoc keys will remain in the
	sequence position that it was already in.
	//*/

		$this->Data = array_merge(
			$this->Data,
			$Input
		);

		return $this;
	}

	public function
	MergeLeft(array $Input):
	static {
	/*//
	@date 2016-03-18
	works like MergeRight except it appears your data was added to the
	beginning of the dataset.
	//*/

		$this->Data = array_reverse(array_merge(
			array_reverse($this->Data),
			array_reverse($Input)
		));

		return $this;
	}

	public function
	BlendRight(array $Input):
	static {
	/*//
	@date 2016-03-18
	works the same as MergeRight, only instead of your input overwriting the
	original data will be kept. your new data will appear at the end of the
	array, and the original data will maintain its original location. numeric
	keys will flat out be appended with the next numeric in the sequence just
	like array_merge.
	//*/

		$Key = NULL;
		$Val = NULL;

		foreach($Input as $Key => $Val) {
			if(is_int($Key)) {
				$this->Data[] = $Val;
				continue;
			}

			if(!array_key_exists($Key,$this->Data)) {
				$this->Data[$Key] = $Val;
				continue;
			}
		}

		return $this;
	}

	public function
	BlendLeft(array $Input):
	static {
	/*//
	@date 2016-03-18
	works the same as BlendRight, only it appears your data was added to
	the beginning to the dataset.
	//*/

		$Key = NULL;
		$Val = NULL;

		$this->Data = array_reverse($this->Data);

		foreach(array_reverse($Input) as $Key => $Val) {
			if(is_int($Key)) {
				$this->Data[] = $Val;
				continue;
			}

			if(!array_key_exists($Key,$this->Data)) {
				$this->Data[$Key] = $Val;
				continue;
			}
		}

		$this->Data = array_reverse($this->Data);
		return $this;
	}

	////////////////////////////////////////////////////////////////
	// File Operations /////////////////////////////////////////////

	public function
	Read(?string $Filename=NULL):
	static {
	/*//
	@date 2015-12-02
	//*/

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
		throw new Exception("File {$Basename} not found.",1);

		if(!is_readable($Filename))
		throw new Exception("File {$Basename} is not readable.",2);

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
	Write(?string $Filename=NULL):
	static {
	/*//
	@date 2015-12-02
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
	GetFromFile(string $Filename):
	static {
	/*//
	@date 2015-12-02
	//*/

		return (new static)->Read($Filename);
	}

}
