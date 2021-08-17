<?php

namespace Nether\Object\Prototype;

use Attribute;
use ReflectionProperty;
use ReflectionNamedType;
use Nether\Object\Meta\PropertyObjectify;
use Nether\Object\Prototype\AttributeInterface;

class PropertyAttributes {
/*//
@date 2021-08-09
this class defines everything via pre-processing about a class property that
the prototype system will want to know about.
//*/

	public string
	$Name;

	public string
	$Origin;

	public string
	$Type;

	public bool
	$Castable;

	public bool
	$Static;

	public ?PropertyObjectify
	$Objectify = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(ReflectionProperty $Prop) {
	/*//
	@date 2021-08-09
	@mopt busyunit, avoid-obj-prop-rw
	//*/

		$Type = $Prop->GetType();
		$Attrib = NULL;
		$Inst = NULL;
		$StrType = 'mixed';

		// get some various info.

		if($Type !== NULL)
		$StrType = $Type->GetName();

		$this->Name = $this->Origin = $Prop->GetName();
		$this->Type = $StrType;
		$this->Static = $Prop->IsStatic();

		// determine if it can be progamatically typecast.

		$this->Castable = (
			$Type instanceof ReflectionNamedType
			&& $Type->IsBuiltIn()
			&& $StrType !== 'mixed'
		);

		foreach($Prop->GetAttributes() as $Attrib) {
			$Inst = $Attrib->NewInstance();

			if($Inst instanceof AttributeInterface)
			$Inst->OnPropertyAttributes($this);
		}

		return;
	}

}
