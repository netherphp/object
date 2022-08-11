<?php

namespace Nether\Object\Package;

use Nether\Object\Prototype\PropertyCache;
use Nether\Object\Prototype\PropertyAttributes;
use Nether\Object\Prototype\PropertyInfo;
use Nether\Object\Datastore;
use ReflectionClass;

trait PropertyAttributePackage {

	static public function
	FetchPropertyIndex():
	array {
	/*//
	@date 2022-08-08
	return a list of all the properties on this class.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefProp = NULL;
		$Output = [];

		foreach($RefClass->GetProperties() as $RefProp)
		$Output[$RefProp->GetName()] = new PropertyInfo($RefProp);

		return $Output;
	}

	static public function
	GetPropertyIndex():
	array {
	/*//
	@date 2022-08-08
	return a list of all the properties on this class using an inline cache
	system if you intend to be asking a lot of meta programming questions.
	this is the preferred method to use in your userland code.
	//*/

		if(PropertyCache::Has(static::class))
		return PropertyCache::Get(static::class);

		return PropertyCache::Set(
			static::class,
			static::FetchPropertyIndex()
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FetchPropertiesWithAttribute(string $AttribName):
	array {
	/*//
	@date 2022-08-08
	return a list of properties on this class that are tagged with the
	specified attribute name.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefProp = NULL;
		$RefAttrib = NULL;
		$Output = [];

		foreach($RefClass->GetProperties() as $RefProp) {
			foreach($RefProp->GetAttributes() as $RefAttrib) {
				if($RefAttrib->GetName() === $AttribName)
				$Output[$RefProp->GetName()] = new PropertyInfo(
					$RefProp
				);
			}
		}

		return $Output;
	}

	static public function
	GetPropertiesWithAttribute(string $AttribName):
	array {
	/*//
	@date 2022-08-08
	return a list of properties on this class that are tagged with the
	specified attribute name using the inline cache system if you intend to
	be asking a lot of meta programming questions. this is the preferred
	method to use in userland code.
	//*/

		$PropertyMap = NULL;

		////////

		if(PropertyCache::Has(static::class))
		$PropertyMap = PropertyCache::Get(static::class);

		else
		$PropertyMap = static::GetMethodIndex();

		////////

		return array_filter(
			$PropertyMap,
			function(PropertyInfo $Property) use($AttribName) {
				$Inst = NULL;

				foreach($Property->Attributes as $Inst) {
					if($Inst instanceof $AttribName)
					return TRUE;
				}

				return FALSE;
			}
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FetchPropertyAttributes(?string $Property=NULL, bool $Init=TRUE):
	array {
	/*//
	@date 2021-08-24
	if a property is specified returns a list of the attributes upon it.
	else it returns a list of all the properties on this class each
	containing their list of attributes.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefProp = NULL;
		$Name = NULL;
		$Attrib = NULL;
		$Output = [];

		// return a list of the attributes for the specified property.

		if($Property !== NULL) {
			if($RefProp = $RefClass->GetProperty($Property))
			foreach($RefProp->GetAttributes() as $Attrib)
			$Output[] = $Init ? $Attrib->NewInstance() : $Attrib;

			return $Output;
		}

		// else return a list of all the properties in this class and
		// their attributes.

		foreach($RefClass->GetProperties() as $RefProp) {
			$Output[($Name = $RefProp->GetName())] = [];

			foreach($RefProp->GetAttributes() as $Attrib)
			$Output[$Name][] = $Init ? $Attrib->NewInstance() : $Attrib;
		}

		return $Output;
	}

	static public function
	GetPropertyAttributes():
	array {
	/*//
	@date 2021-08-05
	@mopt isset, direct read, direct write.
	returns an array of all the properties on this class keyed to their
	data origin name.
	//*/

		if(isset(PropertyCache::$Cache[static::class]))
		return PropertyCache::$Cache[static::class];

		$Output = [];
		$RefClass = NULL;
		$Prop = NULL;
		$Attrib = NULL;

		////////

		$RefClass = new ReflectionClass(static::class);

		foreach($RefClass->GetProperties() as $Prop) {
			$Attrib = new PropertyAttributes($Prop);
			$Output[$Attrib->Origin] = $Attrib;
		}

		return PropertyCache::$Cache[static::class] = $Output;
	}

	static public function
	GetPropertyMap():
	array {
	/*//
	@date 2021-08-16
	returns an assoc array keyed with a data source name and values of the
	data destination name.
	//*/

		$Output = array_map(
			(fn($Val)=> $Val->Name),
			array_filter(
				static::GetPropertyAttributes(),
				(fn($Val)=> !$Val->Static)
			)
		);

		return $Output;
	}

	static public function
	GetPropertyDatastore():
	Datastore {
	/*//
	@date 2022-08-06
	returns a datastore object keyed with a data source name and values of the
	data destination name.
	//*/

		$Output = array_map(
			(fn($Val)=> $Val->Name),
			array_filter(
				static::GetPropertyAttributes(),
				(fn($Val)=> !$Val->Static)
			)
		);

		return new Datastore($Output);
	}

}
