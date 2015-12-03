<?php

namespace Nether;
use \Nether;

class RegionTest extends Object {
/*//
this is a test class extending nether object to test the ability for the static
property map to do its job.
//*/

	static $PropertyMap = [
		'country_id'   => 'ID:int',
		'country_code' => 'Code',
		'country_name' => 'Name'
	];
}

class Object_PropertyMapping_Test extends \Codeception\TestCase\Test {

	protected $Input = [
	/*//
	@type array
	pretend this data came from your database. it contains the flat underscore
	scheme that is common with tables, and includes an id which is currently
	a string as php/pdo/whatever will often return all fields as strings
	regardless of their original type in the database.
	//*/

		'country_id'   => '1',
		'country_code' => 'US',
		'country_name' => 'United States',
		'country_king' => 'Barack Obama'
	];

	////////////////
	////////////////

	public function testMapping() {
	/*//
	test the ability for the property map to rename properties.
	//*/


		$obj = new Nether\RegionTest($this->Input);

		foreach(Nether\RegionTest::$PropertyMap as $old => $new) {
			// test that the property from the db is not in this object.
			$this->assertFalse(property_exists($obj,$old));

			// test that the remapped property exists. stripping off (manually)
			// anytypecasting for this test.
			$this->assertTrue(property_exists(
				$obj,
				preg_replace('/:.*?$/','',$new)
			));
		}


		return;
	}

	public function testMappingDropUnmapped() {
	/*//
	test that the object dropped any unmapped properties, which is the default
	behaviour.
	//*/

		$obj = new Nether\RegionTest($this->Input);

		// also test that the extra property that did not have a map was
		// glanced over.
		$this->assertFalse(property_exists($obj,'country_king'));

		return;
	}

	public function testMappingIncludeUnmapped() {
	/*//
	test that the object can include all unmapped properties if requested.
	//*/

		$obj = new Nether\RegionTest($this->Input,null,[
			'MappedKeysOnly' => false
		]);

		// also test that the extra property that did not have a map was
		// glanced over.
		$this->assertTrue(property_exists($obj,'country_king'));

		return;
	}

	public function testMappingWithTypecasting() {
	/*//
	test that the typecasting applied to the property map.
	//*/

		$obj = new Nether\RegionTest($this->Input);
		$this->assertTrue($obj->ID === 1);
	}

}
