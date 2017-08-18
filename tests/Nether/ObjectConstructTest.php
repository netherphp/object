<?php

namespace Nether;

class ObjectConstructTest
extends \PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestEmpty() {
	/*//
	check that when used on its own or with invalid input that we have created
	an object which has no properties.
	//*/

		$Obj = new Object\Mapped;
		$this->AssertTrue(count(get_object_vars($Obj)) === 0);

		$Obj = new Object\Mapped('donkey');
		$this->AssertTrue(count(get_object_vars($Obj)) === 0);

		$Obj = new Object\Mapped('donkey','kong');
		$this->AssertTrue(count(get_object_vars($Obj)) === 0);

		$Obj = new Object\Mapped('donkey','kong','country');
		$this->AssertTrue(count(get_object_vars($Obj)) === 0);

		return;
	}

	/** @test */
	public function
	TestInput() {
	/*//
	check that if given input that it gets copied into the object as it was
	given to it.
	//*/

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Object = new Object\Mapped($Input);

		foreach($Input as $Key => $Value) {
			$this->AssertObjectHasAttribute($Key,$Object);
			$this->AssertEquals($Object->{$Key},$Value);
		}

		return;
	}

	/** @test */
	public function
	TestDefaults() {
	/*//
	check that any properties missing from the input get set to the specified
	default values.
	//*/

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Default = [
			'PropertyOne'   => -1,
			'PropertyTwo'   => -2,
			'PropertyThree' => -3
		];

		$Result = $Input + $Default;

		$Object = new Object\Mapped($Input,$Default);
		foreach($Result as $Key => $Value) {
			$this->AssertObjectHasAttribute($Key,$Object);
			$this->AssertEquals($Object->{$Key},$Value);
		}

		return;
	}

	/** @test */
	public function
	TestDefaultsWithCulling() {
	/*//
	check that any missing properties missing from the input get set to the
	specified default values, but if it did not have a default then it did
	not get copied in.
	//*/

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Default = [
			'PropertyTwo'   => -2,
			'PropertyThree' => -3
		];

		// check the properties that should exist.

		$Result = $Input + $Default;
		unset($Result['PropertyOne']);

		$Object = new Object\Mapped($Input,$Default,[
			'DefaultKeysOnly' => TRUE
		]);

		foreach($Result as $Key => $Value) {
			$this->AssertObjectHasAttribute($Key,$Object);
			$this->AssertEquals($Object->{$Key},$Value);
		}

		// make sure that one property does /not/ exist as it should have been
		// culled by not having a key in the default array.

		$this->AssertFalse(property_exists($Object,'PropertyOne'));

		return;
	}

	/** @test */
	public function
	TestTypecasting() {
	/*//
	check that typecasting via the colon syntax gets applied.
	//*/

		$Input = [
			'PropertyInt:int'          => '1',
			'PropertyFloat:float'      => '1.234',
			'PropertyString:string'    => 42,
			'PropertyBool1:bool'       => 0,
			'PropertyBool2:bool'       => 1,
			'PropertyBool3:bool'       => 'five',
			'PropertyWhat:invalidtype' => 42.42
		];

		$Object = new Object\Mapped($Input);
		$this->AssertTrue($Object->PropertyInt === 1);
		$this->AssertTrue($Object->PropertyFloat === 1.234);
		$this->AssertTrue($Object->PropertyString === '42');
		$this->AssertTrue($Object->PropertyBool1 === false);
		$this->AssertTrue($Object->PropertyBool2 === true);
		$this->AssertTrue($Object->PropertyBool3 === true);
		$this->AssertTrue($Object->PropertyWhat === 42.42);

		return;
	}

	/** @test */
	public function
	TestDefaultsWithTypecasting() {
	/*//
	check that the typecasting worked on defaults as well.
	//*/

		$Input = [];
		$Default = [
			'PropertyFloat:float' => '9000.1'
		];

		$Object = new Object\Mapped($Input,$Default);
		$this->AssertTrue($Object->PropertyFloat === 9000.1);

		return;
	}

}