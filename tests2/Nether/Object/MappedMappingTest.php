<?php

namespace Nether;

use \PHPUnit as PHPUnit;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class RegionTest
extends Object\Mapped {
/*//
this is a test class to demonstrate the ability of the static property map
to do its job. it is designed to emulate the mutation of an ugly data set
like from the database into properties you actually want to type.
//*/

	static protected
	$PropertyMap = [
		'country_id'   => 'ID:int',
		'country_code' => 'Code',
		'country_name' => 'Name'
	];

}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class MappedMappingTest
extends PHPUnit\Framework\TestCase {

	protected $Input = [
	/*//
	@type Array
	pretend this came from your database. it contains flat underscore scheme
	that is common with db tables, and includes an id which is currently a
	string which is also common with database database results.
	//*/

		'country_id'   => '1',
		'country_code' => 'US',
		'country_name' => 'United States',
		'country_king' => 'Bernie Sanders'
	];

	/** @test */
	public function
	TestMapping() {
	/*//
	checks that the property map was able to rename the properties when they
	were created on this new object.
	//*/

		$Object = new RegionTest($this->Input);
		foreach(RegionTest::GetPropertyMap() as $Old => $New) {
			$this->AssertFalse(property_exists($Object,$Old));
			$this->AssertTrue(property_exists(
				$Object,
				Object\Mapped::__GetTypecastedPropertyName($New)
			));
		}

		return;
	}

	/** @test */
	public function
	TestMappingDropUnmapped() {
	/*//
	check that properties which were not mapped were dropped which is also
	the default behaviour of this object.
	//*/

		$Object = new RegionTest($this->Input);
		$this->AssertFalse(property_exists($Object,'country_king'));

		return;
	}

	/** @test */
	public function
	TestMappingIncludeUnmapped() {
	/*//
	check that we were able to include unmapped properties as an option.
	//*/

		$Object = new RegionTest($this->Input,null,[
			'MappedKeysOnly' => false
		]);

		$this->AssertTrue(property_exists($Object,'country_king'));
		$this->AssertEquals($Object->country_king,$this->Input['country_king']);

		return;
	}

	/** @test */
	public function
	TestMappingWithTypecasting() {
	/*//
	test that the typecasting is being applied to the property map.
	//*/

		$Object = new RegionTest($this->Input);
		$this->AssertTrue($Object->ID === (Int)$this->Input['country_id']);

		return;
	}

}
