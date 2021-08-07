<?php

namespace Nether;

class MappedConstructTest
extends \PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestEmpty() {
	/*//
	check that when used on its own or with invalid input that we have created
	an object which has no properties.
	//*/

		$Obj = new Object\Prototype;
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

		$Key = NULL;
		$Value = NULL;

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Object = new Object\Prototype($Input);

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

		$Key = NULL;
		$Value = NULL;

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

		$Object = new Object\Prototype($Input,$Default);
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

		$Key = NULL;
		$Value = NULL;

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

		$Object = new Object\Prototype(
			$Input,
			$Default,
			Object\PrototypeFlags::CullUsingDefault
		);

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
			'PropertyInt'    => '1',
			'PropertyFloat'  => '1.234',
			'PropertyString' => 42,
			'PropertyBool1'  => 0,
			'PropertyBool2'  => 1,
			'PropertyBool3'  => 'five',
			'PropertyWhat'   => '42.42'
		];

		$Object = new class($Input) extends Object\Prototype {
			public int $PropertyInt;
			public float $PropertyFloat;
			public string $PropertyString;
			public bool $PropertyBool1;
			public bool $PropertyBool2;
			public bool $PropertyBool3;
			public mixed $PropertyWhat;
		};

		//var_dump($Object::GetPropertyMap());

		//$Object = new Object\Prototype($Input);
		$this->AssertTrue($Object->PropertyInt === 1);
		$this->AssertTrue($Object->PropertyFloat === 1.234);
		$this->AssertTrue($Object->PropertyString === '42');
		$this->AssertTrue($Object->PropertyBool1 === FALSE);
		$this->AssertTrue($Object->PropertyBool2 === TRUE);
		$this->AssertTrue($Object->PropertyBool3 === TRUE);
		$this->AssertTrue($Object->PropertyWhat === '42.42');

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
			'PropertyFloat' => '9000.1'
		];

		$Object = new class($Input,$Default) extends Object\Prototype {
			public float $PropertyFloat;
		};

		$this->AssertTrue($Object->PropertyFloat === 9000.1);
		return;
	}

}