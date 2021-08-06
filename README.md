# Nether Object

[![Build Status](https://travis-ci.org/netherphp/object.svg?branch=master)](https://travis-ci.org/netherphp/object)  [![Packagist](https://img.shields.io/packagist/v/netherphp/object.svg)](https://packagist.org/packages/netherphp/object) [![Packagist](https://img.shields.io/packagist/dt/netherphp/object.svg)](https://packagist.org/packages/netherphp/object)

This package provides a self-sealing stem object capable of translating schemas and ensuring that properties exist and have default values assigned that you may want different than ones hard coded in your class definition.

# Usage Without Attributes

Without any attributes or additional arguments you get an object back with the properties you asked it to have. The class does not need to have the properties defined, but if it does it will do basic type casting to make sure the data works in the field it was asked to be in.

```php
class User
extends Nether\Object\Mapped2 {

	public int
	$ID = 0;

	public ?string
	$Name = NULL;

	public ?string
	$Email = NULL;

	public ?string
	$Title = NULL;

}

$User = new User([
	'ID'    => 1,
	'Name'  => 'bob',
	'Email' => 'bmajdak@php.net',
	'Title' => 'Chief Engineer'
]);
```

# Usage With Attributes

With attributes you can add schema translation. Imagine your crappy database structure that you never want to actually read and write. Dump it on your constructor and any properties with the PropertySource attribute will get filled by translation.

```php
class User
extends Nether\Object\Mapped2 {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int
	$ID = 0;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public ?string
	$Name = NULL;

	#[Nether\Object\Meta\PropertySource('user_email')]
	public ?string
	$Email = NULL;

	#[Nether\Object\Meta\PropertySource('user_title')]
	public ?string
	$Title = NULL;

}

$User = new User([
	'user_id'    => 1,
	'user_name'  => 'bob',
	'user_email' => 'bmajdak@php.net',
	'user_title' => 'Chief Engineer'
]);
```

# Usage With Default Values

The second argument to the constructor is an array of default values that should be filled into the object if the data source was missing something.

```php
$User = new User($RowFromDB,[
	'Title' => 'Generic Worker Person'
]);
```

# Usage With Additional Flags

The third argument to the constructor is a flagset that can change some of the behaviours during construction.

```php
// our $RowFromDB has a bunch of fields we do not want in this object.
// using the strict input flag to have it ignore any properties not
// specifically defined by the class. otherwise anything not defined
// will be automatically created as a public property on the fly.

$User = new User(
	$RowFromDB,
	$Defaults,
	Nether\Object\ObjectFlags::StrictInput
);

// there is a flag for if your $Defaults array contains extra data as well.

$User = new User(
	$RowFromDB,
	$Defaults,
	Nether\Object\ObjectFlags::StrictDefault
);
```