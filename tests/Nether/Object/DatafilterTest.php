<?php

namespace NetherTestSuite\Object\Datafilter;

use Exception;
use PHPUnit\Framework\TestCase;
use Nether\Object\Datafilter;
use Nether\Object\Struct\DatafilterItem;

class DatafilterTest
extends TestCase {

	/** @test */
	public function
	TestReadWrite():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2 ];
		$Filter = new Datafilter($Data);

		// basic reading.

		$this->AssertIsInt($Filter->One);
		$this->AssertIsInt($Filter->Two);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertEquals(2, $Filter->Two);
		$this->AssertNull($Filter->Zero);

		// basic writing.

		$Filter->Three = 3;
		$this->AssertIsInt($Filter->Three);
		$this->AssertEquals(3, $Filter->Three);

		// test cache clear on write.

		$Filter['Three'] = 3;
		$Filter->Three(fn($Item)=> (float)$Item->Value);
		$this->AssertIsFloat($Filter->Three);
		$this->AssertEquals(3, $Filter->Three);
		$this->AssertTrue($Filter->CacheHas('Three'));

		// test countable

		$this->AssertEquals(3, count($Filter));

		// test offset Get and Unset

		$this->AssertTrue(isset($Filter['One']));

		unset($Filter['One']);
		$this->AssertFalse(isset($Filter['One']));
		$this->AssertNull($Filter['One']);

		return;
	}

	/** @test */
	public function
	TestFiltering():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2 ];
		$Filter = new Datafilter($Data);

		$Filter
		->Zero(fn(DatafilterItem $Item): bool => TRUE)
		->One(fn(DatafilterItem $Item): string => (string)$Item->Value)
		->Two(fn(DatafilterItem $Item): float => (float)$Item->Value);

		$this->AssertIsString($Filter->One);
		$this->AssertTrue($Filter->One === '1');

		$this->AssertIsFloat($Filter->Two);
		$this->AssertTrue($Filter->Two === 2.0);

		$this->AssertNull($Filter->Zero);

		ob_start();
		var_dump($Filter);
		$Buffer = ob_get_clean();

		return;
	}

	/** @test */
	public function
	TestIteration():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Keys = array_keys($Data);

		$Filter = new Datafilter($Data);
		$Key = NULL;
		$Val = NULL;

		$Iter = 1;
		foreach($Filter as $Key => $Val) {
			$this->AssertEquals($Val, $Iter);
			$this->AssertEquals($Key, strtolower($Keys[$Iter - 1]));

			$Iter++;
		}

		$Iter = 1;
		foreach($Data as $Key => $Val) {
			$this->AssertEquals($Filter[$Key], $Iter);

			$Iter++;
		}

		return;
	}

	/** @test */
	public function
	TestUncallable():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Filter = new Datafilter($Data);
		$HadExcept = FALSE;

		try {
			$Filter->One('func_does_not_exist_my_dudes');
		}

		catch(Exception $Err) {
			$HadExcept = TRUE;
		}

		$this->AssertTrue($HadExcept);

		return;
	}

	/** @test */
	public function
	TestCallableWithMoreArgs():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Filter = new Datafilter($Data);
		$Key = NULL;
		$Val = NULL;
		$MinMax = function(DatafilterItem $Item, int $Min, int $Max) {
			if($Item->Value < $Min)
			return $Min;

			if($Item->Value > $Max)
			return $Max;

			return $Item->Value;
		};

		$Filter->One($MinMax, 2, 3);
		$Filter->Two($MinMax, 2, 3);
		$Filter->Three($MinMax, 2, 3);
		$Filter->Four($MinMax, 2, 3);

		foreach($Filter as $Key => $Val)
		$this->AssertEquals(
			match($Key){
				'one'   => 2,
				'two'   => 2,
				'three' => 3,
				'four'  => 3
			},
			$Val
		);

		return;
	}

	/** @test */
	public function
	TestCaseSensitivity():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Filter = new Datafilter($Data, Case: TRUE);

		$this->AssertEquals(1, $Filter->One);
		$this->AssertNull($Filter->one);

		$Filter->SetCaseSensitive(FALSE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertEquals(1, $Filter->one);

		return;
	}

	/** @test */
	public function
	TestCacheToggle():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];

		$Filter = new Datafilter($Data, Cache: FALSE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertFalse($Filter->CacheHas('One'));

		$Filter->SetCacheOutput(TRUE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertTrue($Filter->CacheHas('One'));

		$Filter->SetCacheOutput(FALSE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertFalse($Filter->CacheHas('One'));

		return;
	}

}
