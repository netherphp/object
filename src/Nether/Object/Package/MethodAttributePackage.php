<?php

namespace Nether\Object\Package;

use ReflectionClass;
use Nether\Object\Prototype\SmartAttribute;
use Nether\Object\Prototype\MethodInfo;
use Nether\Object\Prototype\MethodCache;

trait MethodAttributePackage {

	static public function
	FetchMethodIndex():
	array {
	/*//
	@date 2022-08-07
	return a list of all the methods on this class.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefMethod = NULL;
		$Output = [];

		foreach($RefClass->GetMethods() as $RefMethod)
		$Output[$RefMethod->GetName()] = new MethodInfo($RefMethod);

		return $Output;
	}

	static public function
	GetMethodIndex():
	array {
	/*//
	@date 2022-08-08
	return a list of all the methods on this class using an inline cache
	system if you intend to be asking a lot of meta programming questions.
	this is the preferred method to use in your userland code.
	//*/

		if(MethodCache::Has(static::class))
		return MethodCache::Get(static::class);

		return MethodCache::Set(
			static::class,
			static::FetchMethodIndex()
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FetchMethodsWithAttribute(string $AttribName):
	array {
	/*//
	@date 2022-08-06
	return a list of methods on this class that are tagged with the specified
	attribute name.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefMethod = NULL;
		$RefAttrib = NULL;
		$Output = [];

		foreach($RefClass->GetMethods() as $RefMethod) {
			foreach($RefMethod->GetAttributes() as $RefAttrib) {
				if($RefAttrib->GetName() === $AttribName)
				$Output[$RefMethod->GetName()] = new MethodInfo(
					$RefMethod
				);
			}
		}

		return $Output;
	}

	static public function
	GetMethodsWithAttribute(string $AttribName):
	array {
	/*//
	@date 2022-08-08
	return a list of methods on this class that are tagged with the specified
	attribute name using the inline cache system if you intend to be asking
	a lot of meta programming questions. this is the preferred method to use
	in userland code.
	//*/

		$MethodMap = NULL;

		////////

		if(MethodCache::Has(static::class))
		$MethodMap = MethodCache::Get(static::class);

		else
		$MethodMap = static::GetMethodIndex();

		////////

		return array_filter(
			$MethodMap,
			function(MethodInfo $Method) use($AttribName) {
				$Inst = NULL;

				foreach($Method->Attributes as $Inst) {
					if($Inst instanceof $AttribName)
					return TRUE;
				}

				return FALSE;
			}
		);
	}

}
