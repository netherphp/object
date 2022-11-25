<?php

namespace Nether\Object;
use Nether;

use Exception;
use Iterator;
use ArrayAccess;
use Countable;
use JsonSerializable;
use ReturnTypeWillChange;
use SplFileInfo;

class Datastore
implements Iterator, ArrayAccess, Countable, JsonSerializable {
/*//
@date 2015-12-02
//*/

	const
	FormatPHP  = 1,
	FormatJSON = 2;

	protected ?string
	$Title = NULL;

	protected ?string
	$Filename = NULL;

	protected int
	$Format = self::FormatPHP;

	protected bool
	$FullJSON = FALSE;

	protected bool
	$FullDebug = FALSE;

	protected bool
	$FullSerialize = FALSE;

	protected mixed
	$Sorter = NULL;

	protected array
	$ProtectedKeys = [];

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

		if($this->FullDebug)
		return (array)$this;

		////////

		$Output = [];
		$Key = NULL;
		$Val = NULL;
		$Type = NULL;

		foreach($this->Data as $Key => $Val) {
			if(array_key_exists($Key, $this->ProtectedKeys)) {

				// TRUE = keep key obfus value
				// FALSE = omit key completely.

				if($this->ProtectedKeys[$Key] === FALSE)
				continue;

				////////

				$Type = gettype($Val);
				$Val = match($Type) {
					'string'
					=> sprintf(
						'[protected %s len:%d]',
						$Type, strlen($Val)
					),

					default
					=> sprintf('[protected %s]', $Type)
				};
			}

			$Output[$Key] = $Val;
		}

		return $Output;
	}

	public function
	__Invoke():
	array {

		return $this->Data;
	}

	public function
	__Serialize():
	array {

		$Output = NULL;
		$Value = NULL;

		// bubble down the serialize setting to any substores.

		foreach($this->Data as $Value)
		if($Value instanceof self)
		$Value->SetFullSerialize($this->FullSerialize);

		// handle if we want a small serialize.

		if(!$this->FullSerialize) {
			$Output = [ 'Data' => $this->Data ];

			if($this->Title)
			$Output['Title'] = $this->Title;

			if($this->Filename) {
				$Output['Filename'] = $this->Filename;
				$Output['Format'] = $this->Format;
			}

			return $Output;
		}

		// or the full serialize.

		return (array)$this;
	}

	public function
	__Unserialize(array $Input):
	void {

		$Key = NULL;
		$Value = NULL;

		foreach($Input as $Key => $Value)
		$this->{ltrim($Key, "\0*\0")} = $Value;

		return;
	}

	////////////////////////////////////////////////////////////////
	// protected key api ///////////////////////////////////////////

	// just a way to provide for not accidentally var_dumping all your
	// database secrets or whatever. it only really has effect for the
	// magic debug method info only, it will not stop you from straight up
	// asking for and then echoing something you should not have.

	public function
	Protect(string|array $Key, bool $Omit=FALSE):
	static {

		if(is_array($Key)) {
			$K = NULL;

			foreach($Key as $K)
			$this->ProtectedKeys[$K] = !$Omit;
		}

		else {
			$this->ProtectedKeys[$Key] = !$Omit;
		}

		return $this;
	}

	public function
	Expose(string|array|bool $Key):
	static {

		if(is_array($Key)) {
			$K = NULL;

			foreach($Key as $K)
			if(array_key_exists($K, $this->ProtectedKeys))
			unset($this->ProtectedKeys[$K]);
		}

		elseif(is_string($Key)) {
			if(array_key_exists($Key, $this->ProtectedKeys))
			unset($this->ProtectedKeys[$Key]);
		}

		elseif($Key === TRUE) {
			unset($this->ProtectedKeys);
			$this->ProtectedKeys = [];
		}

		return $this;
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
	@date 2022-11-23
	return the dataset by reference. keep in mind that you need to do the
	ampersand on the reciever end too, and that it only really works if
	assigned to a variable first. dropping this on an array function for
	example wont work.
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
	?string {
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
		$this->Format = $this->DetermineFormatByFilename($Filename);

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
	GetFullSerialize():
	bool {
	/*//
	@date 2021-08-18
	//*/

		return $this->FullSerialize;
	}

	public function
	SetFullSerialize(bool $Val):
	static {
	/*//
	@date 2021-08-18
	//*/

		$this->FullSerialize = $Val;
		return $this;
	}

	public function
	GetSorter():
	mixed {
	/*//
	@date 2016-02-25
	//*/

		return $this->Sorter;
	}

	public function
	SetSorter(?callable $Function):
	static {
	/*//
	@date 2016-02-25
	//*/

		$this->Sorter = $Function;
		return $this;
	}

	public function
	GetTitle():
	?string {
	/*//
	@date 2016-03-25
	//*/

		return $this->Title;
	}

	public function
	SetTitle(?string $Title=''):
	static {
	/*//
	@date 2016-03-25
	//*/

		$this->Title = $Title;
		return $this;
	}

	public function
	DetermineFormatByFilename(string $Filename):
	int {
	/*//
	@date 2022-08-15
	if the filename matches these specific types it will return what we
	think it should be. else it will return what it already is in the event
	you just doing whatever you want.
	//*/

		$File = strtolower($Filename);

		if(str_ends_with($File, '.json'))
		return static::FormatJSON;

		if(str_ends_with($File, '.phson'))
		return static::FormatPHP;

		return $this->Format;
	}

	////////////////////////////////////////////////////////////////
	// implements ArrayAccess //////////////////////////////////////

	public function
	OffsetExists(mixed $Key):
	bool {
	/*//
	@date 2015-12-02
	@implements ArrayAccess
	//*/

		return array_key_exists($Key, $this->Data);
	}

	public function
	OffsetGet(mixed $Key):
	mixed {
	/*//
	@date 2015-12-02
	@implements ArrayAccess
	//*/

		if(array_key_exists($Key, $this->Data))
		return $this->Data[$Key];

		return NULL;
	}

	public function
	OffsetSet(mixed $Key, mixed $Value):
	void {
	/*//
	@date 2015-12-02
	@implements ArrayAccess
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
	@implements ArrayAccess
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
		$Function($Value, $Key, $this, ...($Argv??[]));

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

		return throw new Exception(
			'unable to give you a reference to data that does not exist '.
			'in this case you really should be Has\'ing first if you '.
			'insist on doing this silly hacky stuff.'
		);
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

	public function
	IsTrue(string $Key, bool $NullIsTrue=FALSE):
	bool {
	/*//
	@date 2022-08-31
	check if the data is true or trueable, and handle if undefined values
	should be treated as true or not.
	//*/

		$Val = $this->Get($Key);

		// if the value was null in the context of a configuration file
		// you may want undefined keys to be treated as true or false
		// depending on your desired default behaviours.

		if($Val === NULL)
		$Val = $NullIsTrue ? TRUE : FALSE;

		// if the value was a string we will only accept true and TRUE
		// as truth. everything else will be false to avoid php being
		// flippity floppy based on the first character.

		if(is_string($Val))
		$Val = match($Val) {
			'true', 'TRUE'
			=> TRUE,

			default
			=> FALSE
		};

		////////

		$Val = (bool)$Val;

		return $Val;
	}

	public function
	IsTrueEnough(string $Key):
	bool {
	/*//
	@date 2022-08-31
	check if data is true and consider undefined keys as true.
	//*/

		return $this->IsTrue($Key, TRUE);
	}

	public function
	IsFalse(string $Key, bool $NullIsTrue=FALSE):
	bool {
	/*//
	@date 2022-08-31
	inversion of IsTrue lmao.
	//*/

		return !$this->IsTrue($Key, $NullIsTrue);
	}

	public function
	IsFalseEnough(string $Key):
	bool {
	/*//
	@date 2022-08-31
	check if data is false and consider undefined keys as false.
	//*/

		return !$this->IsTrue($Key, FALSE);
	}

	public function
	IsNull(string $Key):
	bool {
	/*//
	@date 2022-08-31
	check if data is null or nullable. this could be either a literal null
	or undefined. if you need to know if it legit exists or not there is
	the HasKey method.
	//*/

		$Val = $this->Get($Key);

		////////

		if(is_string($Val))
		if($Val === 'null' || $Val === 'NULL')
		$Val = NULL;

		////////

		return ($Val === NULL);
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
	Define(string $Key, mixed $Val):
	static {
	/*//
	@date 2022-08-29
	add this data under this key, but only if it does not already exist.
	the "do not overwrite" version of Shove.
	//*/

		if(!array_key_exists($Key, $this->Data))
		$this->Data[$Key] = $Val;

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

		$this->Data = array_filter($this->Data,$FilterFunc);

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
	Set(mixed $Key, mixed $Value):
	static {
	/*//
	@date 2022-08-29
	alias for shove. the two methods may eventually flip flop with one of them
	becoming deprecated idk yet. shove makes sense in some contexts.
	//*/

		return $this->Shove($Key, $Value);
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
	Sort(?callable $Function=NULL):
	static {
	/*//
	@date 2015-12-02
	sort the dataset. if no custom function is supplied it will execute the
	default sorter function. if there is no default sorter function it will
	execute the php asort function.
	//*/

		if($Function === NULL) {
			if(is_callable($this->Sorter))
			uasort($this->Data, $this->Sorter);

			else
			asort($this->Data);
		}

		////////

		if(is_callable($Function))
		uasort($this->Data, $Function);

		////////

		return $this;
	}

	public function
	SortKeys(?callable $Function=NULL):
	static {
	/*//
	@date 2022-11-23
	behaves the same as Sort except against the keys.
	//*/

		if($Function === NULL) {
			if(is_callable($this->Sorter))
			uksort($this->Data, $this->Sorter);

			else
			ksort($this->Data);
		}

		////////

		if(is_callable($Function))
		uksort($this->Data, $Function);

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
	MergeRight(array|object $Input):
	static {
	/*//
	@date 2016-03-18
	appends the input to the dataset. if there are conflicting assoc keys, the
	input data here will override whatever already existed. numeric keys will
	be appended no matter what. all new data will appear at the end of the
	array. any data that had conflicting assoc keys will remain in the
	sequence position that it was already in.
	//*/

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

		$this->Data = array_merge(
			$this->Data,
			$Input
		);

		return $this;
	}

	public function
	MergeLeft(array|object $Input):
	static {
	/*//
	@date 2016-03-18
	works like MergeRight except it appears your data was added to the
	beginning of the dataset.
	//*/

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

		$this->Data = array_reverse(array_merge(
			array_reverse($this->Data),
			array_reverse($Input)
		));

		return $this;
	}

	public function
	BlendRight(array|object $Input):
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

		////////

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

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
	BlendLeft(array|object $Input):
	static {
	/*//
	@date 2016-03-18
	works the same as BlendRight, only it appears your data was added to
	the beginning to the dataset.
	//*/

		$Key = NULL;
		$Val = NULL;

		////////

		if($Input instanceof static)
		$Input = $Input->GetData();

		elseif(is_object($Input))
		$Input = (array)$Input;

		////////

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
	Read(?string $Filename=NULL, bool $Append=FALSE):
	static {
	/*//
	@date 2015-12-02
	//*/

		$Filename ??= $Filename ?? $this->Filename;

		if(!$Filename)
		throw new Error\FileNotSpecified;

		////////

		$File = new SplFileInfo($Filename);
		$Basename = $File->GetBasename();
		$Ext = strtolower($File->GetExtension()) ?: NULL;

		if(!file_exists($Filename))
		throw new Error\FileNotFound($Basename);

		if(!$File->IsReadable())
		throw new Error\FileUnreadable($Basename);

		////////

		$Data = NULL;

		if($Ext === 'json' || $this->Format === static::FormatJSON)
		$Data = json_decode(file_get_contents($Filename));
		else
		$Data = unserialize(file_get_contents($Filename));

		if(!is_array($Data))
		$Data = (array)$Data;

		////////

		if(!$Append)
		$this->Data = $Data;
		else
		$this->Data = array_merge($this->Data, $Data);

		return $this;
	}

	public function
	Write(?string $Filename=NULL):
	static {
	/*//
	@date 2015-12-02
	write this datastructure to disk.
	//*/

		$Filename ??= $Filename ?? $this->Filename;

		if(!$Filename)
		throw new Error\FileNotSpecified;

		////////

		$File = new SplFileInfo($Filename);
		$Val = NULL;

		$Dirname = $File->GetPath();
		$Format = $this->Format;

		if($Filename !== $this->Filename)
		$Format = $this->DetermineFormatByFilename($Filename);

		////////

		if(!file_exists($Filename)) {
			if(!is_dir($Dirname) && !@mkdir($Dirname, 0777, TRUE))
			throw new Error\DirUnwritable(dirname($Dirname));

			elseif(!is_writable($Dirname))
			throw new Error\DirUnwritable($Dirname);
		}

		else {
			if(!is_writable($Filename))
			throw new Error\FileUnwritable($Filename);
		}

		////////

		foreach($this->Data as $Val)
		if($Val instanceof self)
		$Val->SetFullSerialize($this->FullSerialize);

		////////

		$Data = match(TRUE) {
			($Format === static::FormatJSON)
			=> json_encode($this->Data, JSON_PRETTY_PRINT),

			default
			=> serialize($this->Data)
		};

		////////

		file_put_contents($Filename, $Data);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public static function
	NewFromFile(string $Filename):
	static {
	/*//
	@date 2022-08-15
	//*/

		return (new static)->Read($Filename);
	}

}
