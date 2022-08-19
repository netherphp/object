<?php

namespace Nether\Object\Prototype;

use Attribute;
use ReflectionProperty;
use ReflectionNamedType;
use Nether\Object\Meta\PropertyObjectify;
use Nether\Object\Prototype\PropertyInfoInterface;

class PropertyInfo {
/*//
@date 2021-08-09
this class defines everything via pre-processing about a class property that
the prototype system will want to know about.
//*/

	public string
	$Class;

	public string
	$Name;

	public string
	$Type;

	public string
	$Origin;

	public bool
	$Castable;

	public string
	$Access;

	public bool
	$Static;

	public bool
	$Nullable;

	public ?PropertyObjectify
	$Objectify = NULL;

	public array
	$Attributes = [];

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
		$AttribName = NULL;
		$Inst = NULL;
		$StrType = 'mixed';
		$Nullable = TRUE;

		// get some various info.

		if($Type instanceof ReflectionNamedType) {
			$StrType = $Type->GetName();
			$Nullable = $Type->AllowsNull();
		}

		$this->Class = $Prop->GetDeclaringClass()->GetName();
		$this->Name = $Prop->GetName();
		$this->Type = $StrType;
		$this->Nullable = $Nullable;
		$this->Origin = $this->Name;
		$this->Static = $Prop->IsStatic();
		$this->Access = match(TRUE) {
			($Prop->IsProtected())
			=> 'protected',

			($Prop->IsPrivate())
			=> 'private',

			default
			=> 'public'
		};

		// determine if it can be progamatically typecast.

		$this->Castable = (
			$Type instanceof ReflectionNamedType
			&& $Type->IsBuiltIn()
			&& $StrType !== 'mixed'
		);

		foreach($Prop->GetAttributes() as $Attrib) {
			$AttribName = $Attrib->GetName();
			$Inst = $Attrib->NewInstance();

			////////

			if($Inst instanceof PropertyInfoInterface)
			$Inst->OnPropertyInfo($this, $Prop, $Attrib);

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
