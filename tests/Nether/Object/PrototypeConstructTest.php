<?php

namespace Nether;

use PHPUnit;

use Nether\Object\Prototype\PropertyInfo;
use Nether\Object\Prototype\MethodInfo;
use Throwable;

class LocalTest2
extends Object\Prototype {

	#[Object\Meta\PropertyOrigin('number_one')]
	public int
	$One;

	#[Object\Meta\PropertyOrigin('number_two')]
	public int
	$Two;

}

class LocalTest3
extends Object\Prototype {

	public int
	$TypedProperty;

	public
	$UntypedProperty;

	public function
	TypedMethod():
	int {

		return 1;
	}

	public function
	UntypedMethod() {

		return;
	}

}

class PrototypeConstructTest
extends PHPUnit\Framework\TestCase {

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
			Object\Prototype\Flags::CullUsingDefault
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

	/** @test */
	public function
	TestPropertyObjectify():
	void {
	/*//
	check that methods attributed with PropertyObjectify create new instances
	where wanted.
	//*/

		$Object = new class() extends Object\Prototype {
			#[Object\Meta\PropertyObjectify]
			public Object\Datastore $Data;
		};

		$this->AssertInstanceOf(
			'Nether\\Object\\Datastore',
			$Object->Data
		);

		return;
	}

	/** @test */
	public function
	TestGetPropertyMap() {

		$Map = LocalTest2::GetPropertyMap();

		$this->AssertTrue(is_array($Map));
		$this->AssertTrue(count($Map) === 2);
		$this->AssertTrue(array_key_exists('number_one',$Map));
		$this->AssertTrue($Map['number_one'] === 'One');
		$this->AssertTrue(array_key_exists('number_two',$Map));
		$this->AssertTrue($Map['number_two'] === 'Two');

		return;
	}

	/** @test */
	public function
	TestNamedPropertyConstruct() {

		$Test1 = LocalTest2::New(
			One: 1,
			Two: 2,
			Three: 3
		);

		$Test2 = LocalTest2::NewRelaxed(
			One: 1,
			Two: 2,
			Three: 3
		);

		$this->AssertObjectHasAttribute('One',$Test1);
		$this->AssertObjectHasAttribute('Two',$Test1);
		$this->AssertObjectNotHasAttribute('Three',$Test1);

		$this->AssertObjectHasAttribute('One',$Test2);
		$this->AssertObjectHasAttribute('Two',$Test2);
		$this->AssertObjectHasAttribute('Three',$Test2);

		return;
	}

	/** @test */
	public function
	TestThatAllowNullOnNullCallLogicFailWithUntypedProps() {

		// tests a failure in my logic that went unnoticed for a while
		// since i tend to strict type everything, that we ran into at
		// work like a month after the release. it was calling a method
		// a reflection type when there was no type defined gg.

		try {
			$Props = LocalTest3::GetPropertyIndex();
			$Prop = $Props['UntypedProperty'];
		}

		catch(Throwable $E) {
			$this->AssertFalse(TRUE, 'the problem exists.');
		}

		$this->AssertTrue($Prop instanceof PropertyInfo);
		$this->AssertEquals($Prop->Type, 'mixed');
		$this->AssertTrue($Prop->Nullable);

		return;
	}

	/** @test */
	public function
	TestThatAllowNullOnNullCallLogicFailWithUntypedMethods() {

		// tests a failure in my logic that went unnoticed for a while
		// since i tend to strict type everything, that we ran into at
		// work like a month after the release. it was calling a method
		// a reflection type when there was no type defined gg.

		try {
			$Methods = LocalTest3::GetMethodIndex();
			$Method = $Methods['UntypedMethod'];
		}

		catch(Throwable $E) {
			$this->AssertFalse(TRUE, 'the problem exists.');
		}

		$this->AssertTrue($Method instanceof MethodInfo);
		$this->AssertEquals($Method->Type, 'mixed');

		return;
	}

}