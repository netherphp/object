# Nether Object

[![Build Status](https://travis-ci.org/netherphp/object.svg?branch=AttributeBased)](https://travis-ci.org/netherphp/object)  [![Packagist](https://img.shields.io/packagist/v/netherphp/object.svg)](https://packagist.org/packages/netherphp/object) [![Packagist](https://img.shields.io/packagist/dt/netherphp/object.svg)](https://packagist.org/packages/netherphp/object)

This package provides some classes for utility and using as object bases.



# Nether\Object\Prototype

The Prototype Object a self-sealing stem object capable of translating schemas
and ensuring that properties exist with default values if needed. Using this
class as the parent enables the attribute based functionality all the way down.

```php
$Object = new Nether\Object\Prototype(
	array|object|null $Input,
	array|object|null $Defaults,
	int|null $Flags
);
```

## Usage Without Anything

Without any properties, attributes, or additional arguments, you get an object
back with the properties you asked it to have. Here you can see MySQL gave us
back a number as string and it will continue to be so after this.

```php
$RowFromDB = [
	'user_id'    => '1',
	'user_name'  => 'bob',
	'user_email' => 'bmajdak-at-php-dot-net',
	'user_title' => 'Chief Iconoclast'
];

class User
extends Nether\Object\Prototype {

}

var_dump(new User($RowFromDB));
```

```
object(User)#1 (4) {
	["user_id"] => string(1) "1"
	["user_name"] => string(3) "bob"
	["user_email"] => string(22) "bmajdak-at-php-dot-net"
	["user_title"] => string(16) "Chief Iconoclast"
}
```



## Usage With Typed Properties

Changing nothing except adding typed properties to the class will cause it
to typecast the value for you. In the example where MySQL gave us back a string
numeral '1' which will then be casted to `(int)'1'` for you. Only the core PHP
types can be casted - `mixed` and classes/interfaces currently cannot be.

```php
class User
extends Nether\Object\Prototype {

	public int $user_id;
	public string $user_name;
	public string $user_email;
	public string $user_title;

}

var_dump(new User($RowFromDB));
```

```
object(User)#2 (4) {
	["user_id"] => int(1)
	["user_name"] => string(3) "bob"
	["user_email"] => string(22) "bmajdak-at-php-dot-net"
	["user_title"] => string(16) "Chief Iconoclast"
}
```



## Usage With Mapped Input

We all know that Dave asked absolutely nobody when he designed that database
table, and now you are stuck having to type that snake-case crap the rest
of your life. Using attributes the input data can be remapped to an API that
will not damage your calm. Technically this makes it half of an ORM.

```php
class User
extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int $ID;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public string $Name;

	#[Nether\Object\Meta\PropertySource('user_email')]
	public string $Email;

	#[Nether\Object\Meta\PropertySource('user_title')]
	public string $Title;

}

var_dump(new User($RowFromDB));
```

```
object(User)#3 (4) {
	["ID"] => int(1)
	["Name"] => string(3) "bob"
	["Email"] => string(22) "bmajdak-at-php-dot-net"
	["Title"] => string(16) "Chief Iconoclast"
}
```



## Usage With Default Values

The second argument to the constructor is an array of default values that
should be filled into the object if the data source was missing something.
In this example our MySQL data had no user_status field.

```php
class User
extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int $ID;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public string $Name;

	#[Nether\Object\Meta\PropertySource('user_email')]
	public string $Email;

	#[Nether\Object\Meta\PropertySource('user_title')]
	public string $Title;

	#[Nether\Object\Meta\PropertySource('user_status')]
	public string $Status;

}

$Defaults = [
	'Status' => 'Probably Cool'
];

var_dump(new User($RowFromDB));
var_dump(new User($RowFromDB, $Defaults));
```

```
object(User)#4 (4) {
	["ID"] => int(1)
	["Name"] => string(3) "bob"
	["Email"] => string(22) "bmajdak-at-php-dot-net"
	["Title"] => string(16) "Chief Iconoclast"
	["Status"] => uninitialized(string)
}

object(User)#5 (5) {
	["ID"] => int(1)
	["Name"] => string(3) "bob"
	["Email"] => string(22) "bmajdak-at-php-dot-net"
	["Title"] => string(16) "Chief Iconoclast"
	["Status"] => string(13) "Probably Cool"
}
```



## Usage With Additional Flags

The third argument to the constructor is a flagset that can change some of the
behaviours during construction. By default if properties have not been directly
mapped in the class the additional ones will be added on the fly, which makes
them both public and mixed type. Using the Strict flags you can have it only
include the properties that are hardcoded into the class definition.

```php
use Nether\Object\Prototype;
use Nether\Object\Prototype\Flags;
use Nether\Object\Meta\PropertySource;

class User
extends Prototype {

	#[PropertySource('user_id')]
	public int $ID;

	#[PropertySource('user_name')]
	public string $Name;

}

var_dump(new User(
	$RowFromDB,
	$Defaults
));

var_dump(new User(
	$RowFromDB,
	$Defaults,
	(Prototype\Flags::StrictInput | Prototype\Flags::StrictDefault)
));

```

```
object(User)#6 (4) {
	["ID"]         => int(1)
	["Name"]       => string(3) "bob"
	["Status"]     => string(13) "Probably Cool"
	["user_email"] => string(22) "bmajdak-at-php-dot-net"
	["user_title"] => string(16) "Chief Iconoclast"
}

object(User)#7 (2) {
	["ID"]   => int(1)
	["Name"] => string(3) "bob"
}
```



# Nether\Object\Datastore

Provides a collection store so you can do array like things in an object
oriented manner.

;; TODO 2021-08-06