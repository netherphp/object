<?php

namespace Nether\Object\Prototype;

use Nether\Object\Prototype\MethodInfoInterface;
use ReflectionClass;
use ReflectionNamedType;

class ClassInfo {

	public string
	$Namespace;

	public string
	$Name;

	public bool
	$Abstract;

	public array
	$Attributes = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(ReflectionClass $RefClass) {
	/*//
	@date 2022-08-11
	//*/

		$Attrib = NULL;
		$AName = NULL;
		$AMulti = FALSE;

		$this->Namespace = $RefClass->GetNamespaceName();
		$this->Name = $RefClass->GetShortName();
		$this->Abstract = $RefClass->IsAbstract();

		foreach($RefClass->GetAttributes() as $Attrib) {
			$AName = $Attrib->GetName();
			$AMulti = $Attrib->IsRepeated();
			$Inst = $Attrib->NewInstance();

			////////

			if($Inst instanceof ClassInfoInterface)
			$Inst->OnClassInfo($this, $RefClass, $Attrib);

			////////

			if($AMulti) {
				if(!isset($this->Attributes[$AName]))
				$this->Attributes[$AName] = [];

				$this->Attributes[$AName][] = $Inst;
			}

			else {
				$this->Attributes[$AName] = $Inst;
			}
		}

		return;
	}

	public function
	HasAttribute(string $Type):
	bool {

		return isset($this->Attributes[$Type]);
	}

	public function
	GetAttribute(string $Type):
	mixed {

		if(isset($this->Attributes[$Type]))
		return $this->Attributes[$Type];

		return NULL;
	}

	public function
	GetAttributes(?string $Type=NULL):
	array {

		if($Type === NULL)
		return $this->Attributes;

		////////

		$Output = $this->GetAttribute($Type);

		if(is_array($Output))
		return $Output;

		if($Output)
		return [ $Output ];

		return [ ];
	}

}
