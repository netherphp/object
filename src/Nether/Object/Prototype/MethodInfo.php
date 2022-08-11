<?php

namespace Nether\Object\Prototype;

use Attribute;
use ReflectionMethod;
use ReflectionNamedType;
use Nether\Object\Prototype\AttributeInterface;

class MethodInfo {

	public string
	$Name;

	public string
	$ReturnType;

	public array
	$Args;

	public bool
	$Static;

	public array
	$Attributes;

	public string
	$Source;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(ReflectionMethod $Method) {
	/*//
	@date 2021-08-09
	@mopt busyunit, avoid-obj-prop-rw
	//*/

		$Type = $Method->GetReturnType();
		$StrType = 'mixed';
		$Attrib = NULL;
		$Inst = NULL;

		// get some various info.

		if($Type !== NULL)
		$StrType = $Type->GetName();

		$this->Name = $Method->GetName();
		$this->Type = $StrType;
		$this->Static = $Method->IsStatic();
		$this->Source = $Method->GetDeclaringClass()->GetName();
		$this->Attributes = [];

		foreach($Method->GetAttributes() as $Attrib) {
			$Inst = $Attrib->NewInstance();

			//if($Inst instanceof AttributeInterface)
			//$Inst->OnPropertyAttributes($this);

			$this->Attributes[] = $Inst;
		}

		return;
	}

}
