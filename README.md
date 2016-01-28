Nether Object
=====================================
[![Code Climate](https://codeclimate.com/github/netherphp/object/badges/gpa.svg)](https://codeclimate.com/github/netherphp/object) [![Build Status](https://travis-ci.org/netherphp/object.svg?branch=master)](https://travis-ci.org/netherphp/object)  [![Packagist](https://img.shields.io/packagist/v/netherphp/object.svg)](https://packagist.org/packages/netherphp/object) [![Packagist](https://img.shields.io/packagist/dt/netherphp/object.svg)](https://packagist.org/packages/netherphp/object)

This package provides a self-constructing object translation matrix capacitor.

It's kind of the cornerstone of all the things in Nether. It lets you do things like
translate database schemes that look like this:

	obj_id int
	obj_name string
	obj_date string
	
Into stuff you actually want to type in your code, such as...

	Object->ID
	Object->Name
	Object->Date

It also does a few other things, like it can be used to be sure beyond a doubt that the
random StdClass you are passing around in your app have the bare minimum properties
required, and other various intregrity reasons.

Obviously, not fully documented yet.

Use Case (Basic Function)
-------------------------------------

Here is some ass code that is also ugly...

	if(!$opt || (!is_array($opt) && !is_object($opt))) $opt = [];

	$opt = (object)array_merge(
		[
			'ObjectType' => 'model:',
			'ParentID'   => $this->ID,
			'Limit'      => 1
		],
		(array)$opt
	);

... that we can clean up a little bit.

	$opt = new Nether\Object($opt,[
		'ObjectType' => 'model:',
		'ParentID'   => $this->ID,
		'Limit'      => 1
	]);

It can be a bit more involved. Like if we know ParentID has to be an integer.

	$opt = new Nether\Object($opt,[
		'ObjectType'   => 'model:',
		'ParentID:int' => $this->ID,
		'Limit'        => 1
	]);

Install
-------------------------------------
To use it stand alone, Composer yourself a netherphp/object with a version of 1.*

If you are using any other Nether components you'll most likely already have this.

Running Tests
-------------------------------------
This library uses Codeception from Composer to Unit Test. I think this will install it how I had it.

	$ composer install
	$ php composer.phar install

After that you should be able to run it.

	$ php vendor/bin/codecept run unit
	$ vendor\bin\codecept run unit

That should yield something like this.
	
	Unit Tests (10) --------------------------------------------------------------------------------------------
	Test empty (Nether\Object_Construct_Test::testEmpty) Ok
	Test input (Nether\Object_Construct_Test::testInput) Ok
	Test input defaults (Nether\Object_Construct_Test::testInputDefaults) Ok
	Test input defaults with default culling (Nether\Object_Construct_Test::testInputDefaultsWithDefaultCulling) Ok
	Test input typecasting (Nether\Object_Construct_Test::testInputTypecasting) Ok
	Test input default typecasting (Nether\Object_Construct_Test::testInputDefaultTypecasting) Ok
	Test mapping (Nether\Object_PropertyMapping_Test::testMapping) Ok
	Test mapping drop unmapped (Nether\Object_PropertyMapping_Test::testMappingDropUnmapped) Ok
	Test mapping include unmapped (Nether\Object_PropertyMapping_Test::testMappingIncludeUnmapped) Ok
	Test mapping with typecasting (Nether\Object_PropertyMapping_Test::testMappingWithTypecasting) Ok
	------------------------------------------------------------------------------------------------------------
	Time: 279 ms, Memory: 7.75Mb
	OK (10 tests, 36 assertions)




