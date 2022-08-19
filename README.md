# **Nether Object (netherphp/object)**

[![Packagist](https://img.shields.io/packagist/v/netherphp/object.svg)](https://packagist.org/packages/netherphp/object) [![Packagist](https://img.shields.io/packagist/dt/netherphp/object.svg)](https://packagist.org/packages/netherphp/object) [![Build Status](https://travis-ci.org/netherphp/object.svg?branch=master)](https://travis-ci.org/netherphp/object) [![codecov](https://codecov.io/gh/netherphp/object/branch/redux/graph/badge.svg?token=6OLA0S797J)](https://codecov.io/gh/netherphp/object)

This package provides some utilities to aid with some low level work.

---

## **Class Overview**

### `Nether\Object\Prototype`

The Prototype Object is a self-sealing stem object capable of remapping schemas
and ensuring that properties exist with default values if needed. Using this
class as the parent enables the attribute based functionality all the way down.

Extending this class automatically grants a constructor designed to handle
being given a pile of data, generally from something like a row of data from
the database. It will look at what the class is expecting for the properties
and make sure the data gets typecast prior to assignment to help avoid typing
errors.

*Simple Example:*
```php
class BaseObject
extends Nether\Object\Prototype {
	public int $ID;
	public string $Name;
}

$RowFromDB = [ 'ID'=> 1, 'Name'=> 'Bob' ];
$Obj = new BaseObject($RowFromDB);
```

* More Documentation https://github.com/netherphp/object/wiki/Class:-Nether%5CObject%5CPrototype

---

### `Nether\Object\Datastore`

Provides an array-like object so that items can be stored like arrays but
manipulated with chainable methods. It implements `Iterable`, `ArrayAccess`,
and `Countable` as well as having a bunch of methods for working on the data
as a single collection.

*Simple Example:*
```php
$Data = new Nether\Object\Datastore([
	1, 2, 3,
	4, 5, 6
]);

// strip out odd numbers
// then sort it big to small
// then show me what we got.

print_r(
	$Data
	->Filter(fn(int $Val)=> ($Val % 2) == 0)
	->Sort(fn(int $A, int $B)=> $B <=> $A)
	->Values()
);

// Array
// (
//    [0] => 6
//    [1] => 4
//    [2] => 2
// )
```

* More Documentation:
  https://github.com/netherphp/object/wiki/Class:-Nether%5CObject%5CDatastore

---

## **Trait Overview**

### Class/Method/Property Info Packages.

These traits when bolted onto any class provided static helper methods for
reading all sorts of information about that class and its members, including
the PHP 8 attributes, to aid in metaprogramming tasks. Classes which extend `Prototype` already have these applied.

### `Nether\Object\Package\ClassInfoPackage`

*Simple Example:*
```php
class MyClass {

	use
	Nether\Object\Package\ClassInfoPackage;

}

$ClassInfo = MyClass::GetClassInfo();

print_r($ClassInfo);
```

* Documentation:
  uri-to-wikipage

### `Nether\Object\Package\MethodInfoPackage`

*Simple Example:*
```php
class MyClass {

	use
	Nether\Object\Package\MethodInfoPackage;

	public function
	GetID():
	int {

		return 0;
	}

}

$Methods = MyClass::GetMethodIndex();

foreach($Methods as $Method) {
	print_r($Method);
}
```

* Documentation:
  uri-to-wikipage

### `Nether\Object\Package\PropertyInfoPackage`

*Simple Example:*
```php
class MyClass {

	use
	Nether\Object\Package\PropertyInfoPackage;

	public int
	$ID = 0;

}

$Props = MyClass::GetPropertyIndex();

foreach($Props as $Prop) {
	print_r($Prop);
}
```

* Documentation:
  uri-to-wikipage



