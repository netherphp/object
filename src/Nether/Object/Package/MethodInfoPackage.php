<?php

namespace Nether\Object\Package;

use Nether\Object\Prototype\MethodInfo;
use Nether\Object\Prototype\MethodInfoCache;
use ReflectionClass;

trait MethodInfoPackage {

	static public function
	FetchMethodInfo(string $MethodName):
	?MethodInfo {

		$Methods = static::FetchMethodIndex();

		if(isset($Methods[$MethodName]))
		return $Methods[$MethodName];

		return NULL;
	}

	static public function
	GetMethodInfo(string $MethodName):
	?MethodInfo {

		$Methods = static::GetMethodIndex();

		if(isset($Methods[$MethodName]))
		return $Methods[$MethodName];

		return NULL;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FetchMethodIndex():
	array {
	/*//
	@date 2022-08-07
	return a list of all the methods on this class.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefMethod = NULL;
		$Info = NULL;
		$Output = [];

		foreach($RefClass->GetMethods() as $RefMethod) {
			$Info = new MethodInfo($RefMethod);
			$Output[$Info->Name] = $Info;
		}

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

		if(MethodInfoCache::Has(static::class))
		return MethodInfoCache::Get(static::class);

		return MethodInfoCache::Set(
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
		$Info = NULL;
		$Output = [];

		foreach($RefClass->GetMethods() as $RefMethod) {
			$Info = new MethodInfo($RefMethod);

			foreach($Info->Attributes as $Inst) {
				if($Inst instanceof $AttribName) {
					$Output[$Info->Name] = $Info;
					break;
				}
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

		$MethodMap = static::GetMethodIndex();

		////////

		return array_filter(
			$MethodMap,
			function(MethodInfo $Method) use($AttribName):
			bool {
				$Inst = NULL;
				$IInst = NULL;

				foreach($Method->Attributes as $Inst) {
					if(is_object($Inst)) {
						if($Inst instanceof $AttribName)
						return TRUE;
					}

					if(is_array($Inst)) {
						foreach($Inst as $IInst)
						if($IInst instanceof $AttribName)
						return TRUE;
					}
				}

				return FALSE;
			}
		);
	}

}
