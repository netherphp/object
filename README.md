# **Nether Object (netherphp/object)**

[![Build Status](https://travis-ci.org/netherphp/object.svg?branch=master)](https://travis-ci.org/netherphp/object)  [![Packagist](https://img.shields.io/packagist/v/netherphp/object.svg)](https://packagist.org/packages/netherphp/object) [![Packagist](https://img.shields.io/packagist/dt/netherphp/object.svg)](https://packagist.org/packages/netherphp/object)

This package provides base classes with basic utility.



# **Class Overview**

## Nether\Object\Prototype

The Prototype Object is a self-sealing stem object capable of remapping schemas
and ensuring that properties exist with default values if needed. Using this
class as the parent enables the attribute based functionality all the way down.

* Documentation
  https://github.com/netherphp/object/wiki/Class:-Nether%5CObject%5CPrototype

## Nether\Object\Datastore

Provides an array-like object so that items can be stored like arrays but
manipulated with chainable methods.

* Documentation:
  https://github.com/netherphp/object/wiki/Class:-Nether%5CObject%5CDatastore



# **Trait Overview**

## Nether\Object\Package\MethodInfoPackage

This trait can be bolted into any class to apply static methods for fetching
a list of all the methods it has as well as process their attributes to perform
metaprogramming tasks. Classes extending Nether\Object\Prototype already have
this trait applied.

* `{Class}::FetchMethodIndex(): array`

  Returns an array of MethodInfo objects that describe the methods and their
  attributes.

* `{Class}::FetchMethodsWithAttribute(string Name): array`

  Returns an array of MethodInfo objects that were tagged with the specified
  attribute.

* `{Class}::GetMethodIndex(): array`

  The same as `FetchMethodIndex()` except it uses an inline cache in case you
  plan to be asking a lot of questions over time. This is the perferred way to
  get the method list from userspace as it will only do the processing of
  the methods and their attributes the first time you ask.

* `{Class}::GetMethodsWithAttribute(string Name): array`

  The same as `FetchMethodsWithAttribute()` except using the inline cache
  mentioned above. This is the preferred way to get the method list
  from userspace.
