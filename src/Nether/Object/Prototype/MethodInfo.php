<?php

namespace Nether\Object\Prototype;

use Nether\Object\Prototype\MethodInfoInterface;
use ReflectionMethod;
use ReflectionNamedType;

class MethodInfo {

	public string
	$Class;

	public string
	$Name;

	public string
	$Type;

	public array
	$Args;

	public bool
	$Static;

	public array
	$Attributes = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(ReflectionMethod $Method) {
	/*//
	@date 2022-08-10
	//*/

		$Type = $Method->GetReturnType();
		$StrType = 'mixed';
		$Attrib = NULL;
		$AttribName = NULL;
		$Inst = NULL;

		// get some various info.

		if($Type instanceof ReflectionNamedType) {
			$StrType = $Type->GetName();
		}

		$this->Class = $Method->GetDeclaringClass()->GetName();
		$this->Name = $Method->GetName();
		$this->Type = $StrType;
		$this->Static = $Method->IsStatic();

		foreach($Method->GetAttributes() as $Attrib) {
			$AttribName = $Attrib->GetName();
			$Inst = $Attrib->NewInstance();

			////////

			if($Inst instanceof MethodInfoInterface)
			$Inst->OnMethodInfo($this, $Method, $Attrib);

			////////

			if($Attrib->IsRepeated()) {
				if(!isset($this->Attributes[$AttribName]))
				$this->Attributes[$AttribName] = [];

				$this->Attributes[$AttribName][] = $Inst;
			}

			else {
				$this->Attributes[$AttribName] = $Inst;
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

}
