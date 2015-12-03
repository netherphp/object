<?php

namespace Nether;

class Object_Construct_Test extends \Codeception\TestCase\Test {

	public function testEmpty() {
	/*//
	make sure that on its own or with invalid inputs, that this creates an
	empty object with no properties.
	//*/

		$obj = new Object;
		$this->assertEquals(0,count(get_object_vars($obj)));

		$obj = new Object('donkey');
		$this->assertEquals(0,count(get_object_vars($obj)));

		$obj = new Object('donkey','nuts');
		$this->assertEquals(0,count(get_object_vars($obj)));

		$obj = new Object('donkey','nuts','beans');
		$this->assertEquals(0,count(get_object_vars($obj)));

		return;
	}

	public function testInput() {
	/*//
	test that when given input, that the properties are brought in one to one.
	//*/

		$input = [
			'property_one' => 1,
			'property_two' => 2
		];

		// check that we got two properties now.
		$obj = new Object($input);
		$this->assertEquals(2,count(get_object_vars($obj)));

		// check that they exist under the specified names with the expected
		// values that were given.
		foreach($input as $key => $val) {
			$this->assertTrue(property_exists($obj,$key));
			$this->assertEquals($val,$obj->{$key});
		}

		return;
	}

	public function testInputDefaults() {
	/*//
	test that when given input and defaults, that the input is brought in one to
	one and that the defaults are then applied on top of the input.
	//*/

		$input = [
			'property_one' => 1,
			'property_two' => 2
		];

		$defaults = [
			'property_two'   => 3,
			'property_three' => 3
		];

		// check that we have 3 properties now.
		$obj = new Object($input,$defaults);
		$this->assertEquals(3,count(get_object_vars($obj)));

		// make sure the properties equal what they should have. one and two
		// were created by the input, three was created by the defaults.
		// defaults also specified two, but since two was an input it should
		// not have been overwritten by defaults.
		$this->assertTrue($obj->property_one === 1);
		$this->assertTrue($obj->property_two === 2);
		$this->assertTrue($obj->property_three === 3);


		return;
	}

	public function testInputDefaultsWithDefaultCulling() {
	/*//
	test that when given input, defaults, and the option DefaultKeysOnly, that
	the input will be brought in one to one, but only the input which also has
	a default key. you can use this feature to skip unwanted data if passing
	a large array around to create multiple types of objects.
	//*/

		$input = [
			'property_one' => 1,
			'property_two' => 2
		];

		$defaults = [
			'property_two'   => 3,
			'property_three' => 3
		];

		// check that we only have two of the three properties.
		$obj = new Object($input,$defaults,[
			'DefaultKeysOnly' => true
		]);
		$this->assertEquals(2,count(get_object_vars($obj)));

		// check that we have only the properties we wanted.
		$this->assertFalse(property_exists($obj,'property_one'));
		$this->assertTrue(property_exists($obj,'property_two'));
		$this->assertTrue(property_exists($obj,'property_three'));

		// and that the input vs default stacking again lined up.
		$this->assertEquals(2,$obj->property_two);
		$this->assertEquals(3,$obj->property_three);

		return;
	}

	public function testInputTypecasting() {
	/*//
	test that we applied the typecasts to the input data if it was noted in the
	data name. we will give it a string and expect that to end up as an int.
	//*/

		$input = [
			'property_int:int'     => '1',
			'property_float:float' => '1.234',
			'property_str:string'  => 42,
			'property_bool1:bool'  => 0,
			'property_bool2:bool'  => 1,
			'property_bool3:bool'  => 'five',
			'property_whatever:invalidtype' => 42.42
		];

		$obj = new Object($input);
		$this->assertTrue($obj->property_int === 1);
		$this->assertTrue($obj->property_float === 1.234);
		$this->assertTrue($obj->property_str === '42');
		$this->assertTrue($obj->property_bool1 === false);
		$this->assertTrue($obj->property_bool2 === true);
		$this->assertTrue($obj->property_bool3 === true);
		$this->assertTrue($obj->property_whatever === 42.42);

		return;
	}

	public function testInputDefaultTypecasting() {
	/*//
	test that we applied the typecasts to the input default data as well.
	//*/

		$input = [];
		$defaults = [
			'property_float2:float' => '9000.1'
		];

		$obj = new Object($input,$defaults);
		$this->assertTrue($obj->property_float2 === 9000.1);

		return;
	}

}