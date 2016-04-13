# Nether Object

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
required for various intregrity reasons.

Obviously, not fully documented yet.



## Use Case: Transforming an Ugly DB Result

Most of our database schemas are probably too annoying to type. Via the Object
PropertyMapping ability we can transform results easiliy.

```php
<?php

class User
extends Nether\Object {

	static public
	$PropertyMap = [
		'u_id'    => 'ID:int',
		'u_email' => 'Email',
		'u_alias' => 'Alias',
		'u_fname' => 'FirstName',
		'u_lname' => 'Surname'
	];
	
	static public
	GetByID(Int $UserID):
	Self {
	/*//
	fetch a user from the database by the primary key aka user id.
	//*/
	
		$Result = Nether\Database::Get()->Query(
			'SELECT * FROM users WHERE u_id=:UserID LIMIT 1;',
			[ 'UserID' => $UserID ]
		);

		if(!$Result->OK)
		throw new Exception('DB Query Failure');
		
		if(!$Result->Count)
		throw new UserNotFoundException($UserID);

		return new self($Result->Next());	
	}
	
	protected function
	__ready() {
	/*//
	to allow the property map to do its job, we want to avoid overwriting the
	normal __construct method for this class. if you define a __ready method
	then that will be called after Nether\Object has done its work and the
	object is ready for use.
	//*/
				
		$this->FullName = "{$this->FirstName} {$this->Surname}";
		$this->EmailName = "{$this->FullName} <{$this->Email}>";			
		return;
	}
	
}
```


## Use Case: Builing Message Objects with Default Properties

We have an object with properties which define a query we want to run. We want
to make sure that it has the bare minimum properties required so that we do
not attempt to access undefined properties later on. The end result is we want
to have an object that is promised to have the properties we need with default
values if they had not yet been defined.

Here is what you would have to write to kinda pull that off.

```php
<?php

function
GetChildrenForObject($Opts):
Array {
/*//
@argv Array $Options
@argv Object $Options

query the database for objects which are children of the specified object.
- ParentID - the parent object.
- Limit - how many results you want.
//*/

	// if input options arg invalid then force it.
	if(!$Opts || (!is_array($Opts) && !is_object($Opts)))
	$Opts = [];

	// force default values for any missing options.
	$Opts = (Object)array_merge([
		'ParentID' => false,
		'Limit'    => 10
	],(Array)$Opts);

	////////
	
	if(!$Opts->ParentID)
	throw new Exception('No ParentID specified');

	$Result = Nether\Database::Get()->Query(
		'SELECT * FROM objects WHERE parent_id=:ParentID LIMIT :Limit;',
		$Opts
	);
	
	if(!$Result->OK)
	throw new Exception('DB Query Failure');
	
	return $Result->Glomp();
}
```

With the Nether\Object class we can clean up the first half of that function a bit.

```php
<?php

function GetChildrenForObject($Opts): Array {
/*//
@argv Array $Options
@argv Object $Options

query the database for objects which are children of the specified object.
- ParentID - the parent object.
- Limit - how many results you want.
//*/

	$Opts = new Nether\Object($Opts,[
		'ParentID' => 0,
		'Limit'    => 10
	]);

	////////
	
	if(!$Opts->ParentID)
	throw new Exception('No ParentID specified');

	$Result = Nether\Database::Get()->Query(
		'SELECT * FROM objects WHERE parent_id=:ParentID LIMIT :Limit;',
		$Opts
	);
	
	if(!$Result->OK)
	throw new Exception('DB Query Failed');
	
	return $Result->Glomp();
}
```

It can be a bit more involved. Like if we know an we want any input to be
coerced into literal integers automatically.

```php
<?php

$Opts = new Nether\Object($Opts,[
	'ParentID:int' => 0,
	'Limit:int'    => 10
]);
```


# Constructor

```php
<?php

// constructor prototype
Nether\Object::__construct(
	Object|Array $InputData,
	Object|Array $DefaultData default NULL,
	Object|Array $Options default NULL
);

// default options if omitted
$Options = [
	'MappedKeysOnly' => TRUE,
	// if there is a PropertyMap and it is not empty, then anything defined
	// in the input which is not defined in the map will be ignored. setting
	// this to FALSE will include any unmapped properties as they were given.
	// this is mainly only for classes which extend Object.
	
	'DefaultKeysOnly' => FALSE,
	// when using Object to ensure message objects contain all the properties
	// they need to have and with default values, by default all properties
	// from the input will be included. if this is set to TRUE then the
	// properties must exist in both Input and Defaults to get included into
	// the final object.
	
	'ForceDefaultValues' => TRUE
	// in the event that for some reason you want the Defaults list to
	// overwrite the Input data. perhaps you are only defaulting things you
	// need for sure, and allowing input to fill in around it. probably only
	// going to be useful in that way with DefaultKeysOnly left to FALSE.
];

// as you would see it in userland:
$Object = new Nether\Object($Inputs, $Defaults, $Options);
```

# Install

To use it stand alone, Composer yourself a netherphp/object with a version of 2.*

If you are using any other Nether components you'll most likely already have this.



# Running Tests

This library uses PHPUnit to test.

	> composer install

After that you should be able to run it.

	> phpunit tests --bootstrap vendor\autoload.php

That should yield something like this.
	
	> phpunit tests --bootstrap vendor\autoload.php
	
	PHPUnit 5.3.2 by Sebastian Bergmann and contributors.
	..................... 21 / 21 (100%)
	Time: 157 ms, Memory: 8.00Mb
	OK (21 tests, 165 assertions)




