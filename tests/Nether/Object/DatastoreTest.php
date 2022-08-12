<?php

namespace NetherTestSuite\Object\DatastoreTest;
use PHPUnit;

use Nether\Object\Datastore;
use StdClass;
use Throwable;
use Exception;

class DatastoreTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestDataStorageDataset() {
	/*//
	this tests that we can actually push and pull data out of the storage
	glomping around the entire datastore array at once.
	//*/

		// testing right out of the box.

		$Key = NULL;
		$Item = NULL;

		$Store = new Datastore;
		$this->AssertTrue($Store->Count() === 0);
		$this->AssertTrue(count($Store->GetData()) === 0);

		// throw some sample data in.

		$Store->SetData([1,2,3,4,5,6]);
		$this->AssertTrue($Store->Count() === 6);
		$this->AssertTrue(count($Store->GetData()) === 6);

		// throw more sample data in and integrity test it.

		$Input = ['zomg','wtfz','bbqueen'];

		$Store->SetData($Input);
		$this->AssertTrue($Store->Count() === count($Input));

		$Output = $Store->GetData();
		$this->AssertTrue(count($Output) === count($Input));

		foreach($Output as $Key => $Item)
		$this->AssertTrue($Item === $Input[$Key]);

		return;
	}

	/** @test */
	public function
	TestDataStorageItemsNormal() {
	/*//
	tests that we can push and pull data out of the storage using the item
	management api of the storage.
	//*/

		$X = NULL;
		$Key = NULL;
		$Value = NULL;

		$Store = new Datastore;

		// test that we can add items one at a time.

		for($X = 1; $X <= 6; $X++)
		$Store->Push($X);

		// and that it properly counted them.

		$this->AssertTrue($Store->Count() === 6);

		// and that they equal to what was expected.

		foreach($Store as $Key => $Value)
		$this->AssertTrue($Value === ($Key + 1));

		return;
	}

	/** @test */
	public function
	TestDataStorageItemsAssoc() {
	/*//
	test that we can push and pull data out of storage using the item
	management api with associative keys.
	//*/

		$Key = NULL;
		$Value = NULL;
		$Store = new Datastore;

		// test that we can add items one at a time.

		$Input = [
			'Forename' => 'Bob',
			'Surname'  => 'Majdak',
			'Suffix'   => 'II'
		];

		foreach($Input as $Key => $Value)
		$Store->Push($Value,$Key);

		$this->AssertTrue($Store->Count() === 3);

		// test that we can get items one at time.

		foreach($Input as $Key => $Value)
		$this->AssertTrue($Store->Get($Key) === $Value);

		return;
	}

	/** @test */
	public function
	TestDataStorageRemoval() {
	/*//
	test that we can remove data from the set and reindex numeric keys while
	preserving associative keys.
	//*/

		$Store = new Datastore;
		$Store->SetData([
			1,2,3,4,5,6,
			'Donkey' => 'Kong',
			7,8,9
		]);
		$this->AssertTrue($Store->Count() === 10);
		$this->AssertTrue($Store->Get(1) === 2);

		$Store->Remove(1);
		$this->AssertTrue($Store->Count() === 9);
		$this->AssertTrue($Store->Get(1) === NULL);

		$Store->Reindex();
		$this->AssertTrue($Store->Count() === 9);
		$this->AssertTrue($Store->Get(1) === 3);
		$this->AssertTrue($Store->Get('Donkey') === 'Kong');

		return;
	}

	/** @test */
	public function
	TestDataStoragePushPop() {
	/*//
	this tests that we implemented the push and popping behaviour in a
	way that works as expected.
	//*/

		$Store = new Datastore;

		$Store->Push(1);
		$Store->Push(2);
		$Store->Push(3);
		$this->AssertTrue($Store->Count() === 3);

		$this->AssertTrue($Store->Pop() === 3);
		$this->AssertTrue($Store->Pop() === 2);
		$this->AssertTrue($Store->Pop() === 1);
		$this->AssertTrue($Store->Count() === 0);

		return;
	}

	/** @test */
	public function
	TestDataStorageShiftUnshift() {

		$Store = (new Datastore)
		->Push(1)
		->Push(2)
		->Push(3);

		$this->AssertTrue($Store->Count() === 3);

		$this->AssertTrue($Store->Shift() === 1);
		$this->AssertTrue($Store->Shift() === 2);
		$this->AssertTrue($Store->Shift() === 3);
		$this->AssertTrue($Store->Count() === 0);
		return;
	}

	/** @test */
	public function
	TestDataIteration() {
	/*//
	this tests that the implememented Iterator interface appears to be
	working as expected.
	//*/

		$Store = new Datastore;
		$Store->SetData([1,2,3,4,5,6]);

		$Loop = 1;
		while($Loop <= 4) {

			// run these tests a few times so we can see reset and
			// valid working. for reasons.

			$Iter = 1;
			foreach($Store as $Key => $Item) {
				$this->AssertTrue($Iter === ($Key + 1));
				$this->AssertTrue($Iter === $Item);
				$Iter++;
			}

			$Loop++;
		}

		return;
	}

	/** @test */
	public function
	TestDataMergeRight() {
	/*//
	testing that we implemented merging new data onto the end of the array
	correctly. (array_merge behaviour)
	//*/

		/*
		this is what we want to see.
		Array
		(
			[0] => 1
			[1] => 2
			[2] => 3
			[one] => one
			[two] => two    // old data here and up.
			[three] => tres // overwrite
			[3] => 4        // new data here and down.
			[4] => 5
			[5] => 6
			[four] => four
			[five] => five
		)
		*/

		$Store = new Datastore;
		$Store->SetData([1,2,3,'one'=>'one','two'=>'two','three'=>'three']);

		$Store->MergeRight([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		$Data = $Store->GetData();

		$this->AssertTrue($Data[0] === 1);
		$this->AssertTrue($Data[3] === 4);
		$this->AssertTrue($Data['three'] === 'tres');
		$this->AssertTrue($Data['five'] === 'five');

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 1);

		end($Data);
		$this->AssertTrue(key($Data) === 'five');
		$this->AssertTrue(current($Data) === 'five');

		$Data = array_values($Data);
		$this->AssertTrue($Data[5] === 'tres');
		$this->AssertTrue($Data[6] === 4);

		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		return;
	}

	/** @test */
	public function
	TestDataMergeLeft() {
	/*//
	testing that we implemented merging new data onto the start of the array
	correctly. (array_merge behaviour)
	//*/

		/*
		this is what we want to see.
		Array
		(
			[0] => 4
			[1] => 5
			[2] => 6
			[four] => four
			[five] => five   // new data here and up
			[3] => 1         // old data here and down
			[4] => 2
			[5] => 3
			[one] => one
			[two] => two
			[three] => tres  // except for this overwrite
		)
		*/


		$Store = new Datastore;
		$Store->SetData([1,2,3,'one'=>'one','two'=>'two','three'=>'three']);

		$Store->MergeLeft([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		$Data = $Store->GetData();

		$this->AssertTrue($Data[0] === 4);
		$this->AssertTrue($Data[3] === 1);
		$this->AssertTrue($Data['three'] === 'tres');
		$this->AssertTrue($Data['five'] === 'five');

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 4);

		end($Data);
		$this->AssertTrue(key($Data) === 'three');
		$this->AssertTrue(current($Data) === 'tres');

		$Data = array_values($Data);
		$this->AssertTrue($Data[4] === 'five');
		$this->AssertTrue($Data[5] === 1);

		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		return;
	}

	/** @test */
	public function
	TestDataBlendRight() {
	/*//
	testing that we implemented blending new data into the end of the array,
	where original data wins any assoc conflicts. (sorta array_merge)
	//*/

		/*
		this is what we want to see.
		Array
		(
			[0] => 1
			[1] => 2
			[2] => 3
			[one] => one
			[two] => two
			[three] => three
			[3] => 4
			[4] => 5
			[5] => 6
			[four] => four
			[five] => five
		)
		*/

		$Store = new Datastore;
		$Store->SetData([1,2,3,'one'=>'one','two'=>'two','three'=>'three']);

		$Store->BlendRight([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		$Data = $Store->GetData();

		$this->AssertTrue($Data[0] === 1);
		$this->AssertTrue($Data[3] === 4);
		$this->AssertTrue($Data['three'] === 'three');
		$this->AssertTrue($Data['five'] === 'five');

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 1);

		end($Data);
		$this->AssertTrue(key($Data) === 'five');
		$this->AssertTrue(current($Data) === 'five');

		$Data = array_values($Data);
		$this->AssertTrue($Data[5] === 'three');
		$this->AssertTrue($Data[6] === 4);

		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		return;
	}

	/** @test */
	public function
	TestDataBlendLeft() {
	/*//
	testing that we implemented blending new data into the the start of the
	array, where original data wins any assoc conflicts. (sorta array_merge)
	//*/

		/*
		this is what we want to see.
		Array
		(
			[0] => 4
			[1] => 5
			[2] => 6
			[four] => four
			[five] => five
			[3] => 1
			[4] => 2
			[5] => 3
			[one] => one
			[two] => two
			[three] => three
		)
		*/

		$Store = new Datastore;
		$Store->SetData([1,2,3,'one'=>'one','two'=>'two','three'=>'three']);

		$Store->BlendLeft([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		$Data = $Store->GetData();

		$this->AssertTrue($Data[0] === 4);
		$this->AssertTrue($Data[3] === 1);
		$this->AssertTrue($Data['three'] === 'three');
		$this->AssertTrue($Data['five'] === 'five');

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 4);

		end($Data);
		$this->AssertTrue(key($Data) === 'three');
		$this->AssertTrue(current($Data) === 'three');

		$Data = array_values($Data);
		$this->AssertTrue($Data[4] === 'five');
		$this->AssertTrue($Data[5] === 1);
		$this->AssertTrue($Data[10] === 'three');

		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		return;
	}

	/** @test */
	public function
	TestDataClear() {
	/*//
	testing that we implemented clearing the dataset properly.
	//*/

		$Store = new Datastore;
		$Store->SetData([1,2,3]);

		$this->AssertTrue($Store->Count(FALSE) === 3);

		$Store->Clear();

		$this->AssertTrue($Store->Count(FALSE) === 0);
		$this->AssertTrue($Store->Count(FALSE) === count($Store->GetData()));

		return;
	}

	/** @test */
	public function
	TestWriteFormat() {
	/*//
	testing that we were able to tell it what format to write to disk as, and
	that an invalid value resulted in a sane default.
	//*/

		$Store = new Datastore;

		$Store->SetFormat($Store::FormatJSON);
		$this->AssertTrue($Store->GetFormat() === $Store::FormatJSON);

		$Store->SetFormat($Store::FormatPHP);
		$this->AssertTrue($Store->GetFormat() === $Store::FormatPHP);

		$Store->SetFormat(-1);
		$this->AssertTrue($Store->GetFormat() === $Store::FormatPHP);

		return;
	}

	/** @test */
	public function
	TestWriteToDisk() {

		$Dataset = [1,2,3];
		$Filename = sprintf(
			'/tmp/nether-object-datastore-%s.phson',
			md5(microtime(TRUE))
		);

		// test that we could create a new file.

		$Store = new Datastore;
		$Store->SetData($Dataset);
		$Store->Write($Filename);
		$this->AssertTrue(file_exists($Filename));

		// test that we could overwrite it.

		$Dataset = [3,2,1];
		$Store->SetData($Dataset);
		$Store->Write($Filename);
		$this->AssertTrue(file_exists($Filename));

		$Data = file_get_contents($Filename);
		$this->AssertEquals($Data,serialize($Dataset));

		// test that we could write it in json.

		$Store->SetFormat($Store::FormatJSON);
		$Store->Write($Filename);
		$Data = file_get_contents($Filename);
		$this->AssertEquals($Data,json_encode($Dataset,JSON_PRETTY_PRINT));

		unlink($Filename);
		return;
	}

	/** @test */
	public function
	TestReadFromDisk() {

		$Iter = NULL;

		$Dataset = [1,2,3];
		$Filename = sprintf(
			'/tmp/nether-object-datastore-%s.phson',
			md5(microtime(TRUE))
		);

		// write a file.

		$Store = new Datastore;
		$Store->SetData($Dataset);
		$Store->Write($Filename);
		$this->AssertTrue(file_exists($Filename));

		// read a file.

		unset($Store);
		$Store = new Datastore;
		$Store->Read($Filename);

		for($Iter = 0; $Iter < count($Dataset); $Iter++)
		$this->AssertTrue($Store->Get($Iter) === $Dataset[$Iter]);

		unset($Filename);
		return;
	}

	/** @test */
	public function
	TestReadFromDisk_ThatOneFuckingPhpBugTheyRefuseToFix() {
	/*//
	serialise is the default for a reason but lets see if that one php bug
	is going to fuck us. https://bugs.php.net/bug.php?id=45959
	//*/

		if(version_compare(phpversion(),'7.2.0-dev','>=')) {
			// holy ballweasles this was fixed in 7.2.0.
			$this->AssertTrue(TRUE);
			return;
		}

		$Dataset = [1,'two'=>2,3];
		$Filename = sprintf(
			'/tmp/nether-object-datastore-%s.json',
			md5(microtime(TRUE))
		);

		// write a file.

		$Store = new Datastore;
		$Store->SetData($Dataset);
		$Store->Write($Filename);
		$this->AssertTrue(file_exists($Filename));

		// read a file.

		unset($Store);
		$Store = new Datastore;
		$Store->Read($Filename);

		// these three asserts passing means yes, the bug is gonna fuck us.
		// we are going to have to make the library warn you about mixing
		// assoc and non-assoc keys when using json.
		$this->AssertTrue($Store->Get(0) === NULL);
		$this->AssertTrue($Store->Get(2) === NULL);
		$this->AssertTrue($Store->Get('two') === 2);

		return;
	}

	/** @test */
	public function
	TestMapAndRemap() {

		$Store = new Datastore;
		$Store->Push('1')->Push('2')->Push('3');
		$New = NULL;

		// make sure i have a store of strings.

		$this->AssertEquals(3,$Store->Count());

		$Store->Each(function($Val){
			$this->AssertTrue(is_string($Val));
			return;
		});

		// make sure i have an old store of strings and a new store
		// of integers.

		$New = $Store->Map(function($Val){ return (Int)$Val; });
		$this->AssertEquals(3,$Store->Count());
		$this->AssertEquals(3,$New->Count());

		$Store->Each(function($Val){
			$this->AssertTrue(is_string($Val));
			return;
		});

		$New->Each(function($Val){
			$this->AssertTrue(is_int($Val));
			return;
		});

		// make sure my store of its is now a store of strings.

		$New->Remap(function($Val){ return (String)$Val; });
		$this->AssertEquals(3,$New->Count());

		$New->Each(function($Val){
			$this->AssertTrue(is_string($Val));
			return;
		});

		return;
	}

	/** @test */
	public function
	TestFilterAndDistill() {

		$Store = new Datastore;
		$Other = NULL;

		$Store->Push(1)->Push(2)->Push(3);
		$this->AssertEquals(3,$Store->Count());

		$Other = $Store->Distill(function($Val){ return $Val !== 2; });
		$this->AssertEquals(2,$Other->Count());
		$this->AssertEquals(3,$Store->Count());
		$this->AssertTrue($Other->HasValue(2) === FALSE);
		$this->AssertTrue($Store->HasValue(2) !== FALSE);
		$this->AssertTrue($Store !== $Other);

		$Other->Push(4);
		$this->AssertEquals(3,$Other->Count());
		$this->AssertEquals(3,$Store->Count());
		$this->AssertTrue($Other->HasValue(4) !== FALSE);
		$this->AssertTrue($Store->HasValue(4) === FALSE);

		$Other = $Store->Filter(function($Val){ return $Val !== 2; });
		$this->AssertEquals(2,$Store->Count());
		$this->AssertTrue($Store->HasValue(2) === FALSE);
		$this->AssertTrue($Store === $Other);

		return;
	}

	/** @test */
	public function
	TestJsonise() {

		$Object = new Datastore([
			'one', 'two', 'three'
		]);

		$JSON = json_encode($Object);
		$Data = json_decode($JSON);
		$this->AssertIsArray($Data);
		$this->AssertCount(3, $Data);

		$Object->SetFullJSON(TRUE);

		$JSON = json_encode($Object);
		$Data = json_decode($JSON);
		$this->AssertIsObject($Data);
		$this->AssertInstanceOf(StdClass::class, $Data);

		return;
	}

	/** @test */
	public function
	TestGetFirstLastHasKeys() {

		$Data = new Datastore([
			'one',
			'two',
			'three'
		]);

		$Key = NULL;
		$Val = NULL;

		$this->AssertTrue($Data->HasKey(0));
		$this->AssertFalse($Data->HasKey(-1));
		$this->AssertEquals(0, $Data->GetFirstKey());
		$this->AssertEquals(2, $Data->GetLastKey());

		foreach($Data as $Key => $Val) {
			if($Val === 'one')
			$this->AssertTrue(
				$Data->IsFirstKey($Key)
				&& !$Data->IsLastKey($Key)
			);

			if($Val === 'two')
			$this->AssertTrue(
				!$Data->IsFirstKey($Key)
				&& !$Data->IsLastKey($Key)
			);

			if($Val === 'three')
			$this->AssertTrue(
				!$Data->IsFirstKey($Key)
				&& $Data->IsLastKey($Key)
			);
		}

		return;
	}

	/** @test */
	public function
	TestTheThingWithEmptyArrayBrackets() {

		$Data = new Datastore;
		$this->AssertEquals(0,$Data->Count());

		$Data[] = 'one';
		$this->AssertEquals(1,$Data->Count());

		$Data[] = 'two';
		$this->AssertEquals(2,$Data->Count());

		return;
	}

	/** @test */
	public function
	TestGetRef():
	void {

		$Data = [ 1, 2 ];
		$Store = new Datastore($Data);

		$this->AssertTrue($Store->Get(0) === 1);
		$this->AssertTrue($Store->Get(1) === 2);
		$this->AssertNull($Store->Get(3));

		$Hack = &$Store->GetRef(0);
		$Hack = 42;

		$this->AssertTrue($Store->Get(0) === 42);
		$this->AssertTrue($Store->Get(1) === 2);
		$this->AssertNull($Store->Get(3));

		////////

		try { $Fail = &$Store->GetRef(3); }
		catch(Throwable $Err) {
			$this->AssertInstanceOf(
				Exception::class,
				$Err
			);
		}

		return;
	}

	/** @test */
	public function
	TestFullDebug():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertFalse($Store->GetFullDebug());
		$this->AssertIsArray($Store->__DebugInfo());
		$this->AssertCount(3, $Store->__DebugInfo());

		$Store->SetFullDebug(TRUE);
		$this->AssertTrue($Store->GetFullDebug());
		$this->AssertGreaterThan(3, count($Store->__DebugInfo()));

		return;
	}

	/** @test */
	public function
	TestInvokeSyntax():
	void {

		$Store = new Datastore([1, 2, 3]);
		$Result = $Store();

		$this->AssertIsArray($Result);
		$this->AssertCount(3, $Result);

		return;
	}

	/** @test */
	public function
	TestGetSetFilename():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertNull($Store->GetFilename());

		$Store->SetFilename('output.json');
		$this->AssertEquals('output.json', $Store->GetFilename());

		return;
	}

	/** @test */
	public function
	TestGetSetFullJSON():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertFalse($Store->GetFullJSON());

		$Result = json_decode(json_encode($Store));
		$this->AssertIsArray($Result);
		$this->AssertCount(3, $Result);

		////////

		$Store->SetFullJSON(TRUE);
		$this->AssertTrue($Store->GetFullJSON());

		$Result = json_decode(json_encode($Store));
		$this->AssertIsObject($Result);

		return;
	}

	/** @test */
	public function
	TestGetSetTitle():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertNull($Store->GetTitle());

		$Store->SetTitle('HerpDerp');
		$this->AssertEquals('HerpDerp', $Store->GetTitle());

		return;
	}

	/** @test */
	public function
	TestGetSetSorter():
	void {

		$Store = new Datastore([2, 1, 3]);
		$Sorter = (fn($A, $B)=> $B <=> $A);

		// check initial sort

		$this->AssertNull($Store->GetSorter());
		$this->AssertEquals(2, array_values($Store->GetData())[0]);

		// check sort using default asort

		$Store->Sort();
		$this->AssertEquals(1, array_values($Store->GetData())[0]);

		// check sort using custom sorter

		$Store->SetSorter($Sorter);
		$this->AssertTrue($Store->GetSorter() === $Sorter);

		$Store->Sort();
		$this->AssertEquals(3, array_values($Store->GetData())[0]);

		// confirm asort but sorted

		$Store->Rewind();
		$this->AssertEquals(2, $Store->Key());
		$this->AssertEquals(3, $Store->Current());

		// confirm forced sort func.

		$Store->SetSorter(NULL);
		$Store->Sort();
		$Store->Rewind();
		$this->AssertEquals(1, $Store->Key());
		$this->AssertEquals(1, $Store->Current());

		$Store->Rewind();
		$Store->Sort($Sorter);
		$this->AssertEquals(2, $Store->Key());
		$this->AssertEquals(3, $Store->Current());

		return;
	}

	/** @test */
	public function
	TestArrayAccess():
	void {

		$Source = [1, 2, 3];
		$Store = new Datastore($Source);
		$Iter = 0;

		// check reading via OffsetGet.

		for($Iter = 0; $Iter < 3; $Iter++)
		$this->AssertEquals($Source[$Iter], $Store[$Iter]);

		// check writing via OffsetSet.

		$Store[] = 9001;
		$this->AssertEquals(9001, $Store[3]);

		$Store[1] = 42;
		$this->AssertEquals(42, $Store[1]);

		$Store['One'] = 'Won';
		$this->AssertEquals('Won', $Store['One']);

		unset($Store['One']);
		$this->AssertFalse($Store->HasKey('One'));
		$this->AssertFalse(isset($Store['One']));

		return;
	}

	/** @test */
	public function
	TestMethodKeysValues():
	void {

		$Store = new Datastore([
			1, 2, 3,
			'One' => 'Won'
		]);

		$Keys = $Store->Keys();
		$this->AssertIsArray($Keys);
		$this->AssertCount(4, $Keys);
		$this->AssertArrayHasKey(0, $Keys);
		$this->AssertContains('One', $Keys);

		$Values = $Store->Values();
		$this->AssertIsArray($Values);
		$this->AssertCount(4, $Values);
		$this->AssertEquals(1, $Values[0]);
		$this->AssertEquals('Won', $Values[3]);

		return;
	}

	/** @test */
	public function
	TestMethodAccumulate():
	void {

		$Store = new Datastore([ 1, 2, 3 ]);
		$Result = $Store->Accumulate(
			9000,
			(fn(int $Prev, int $Val)=> $Prev + $Val )
		);

		$this->AssertEquals(9006, $Result);

		return;
	}

	/** @test */
	public function
	TestMethodJoin():
	void {

		$Store = new Datastore([ 1, 2, 3 ]);
		$Result = $Store->Join('-');

		$this->AssertEquals('1-2-3', $Result);

		return;
	}

}
