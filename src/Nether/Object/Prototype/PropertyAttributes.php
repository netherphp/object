<?php

namespace Nether\Object\Prototype;

use ReflectionProperty;
use ReflectionNamedType;
use Nether\Object\Meta\PropertyObjectify;
use Nether\Object\Meta\PropertyOrigin;

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
		//$HasOrigin = FALSE;
		//$StrName = NULL;
		$StrType = NULL;

		// get some various info.

		$this->Type = $StrType = $Type->GetName();
		$this->Name = $this->Origin = $Prop->GetName();

		// determine if it can be progamatically typecast.

		$this->Castable = (
			$Type instanceof ReflectionNamedType
			&& $Type->IsBuiltIn()
			&& $StrType !== 'mixed'
		);

		foreach($Prop->GetAttributes() as $Attrib) {
			$Inst = $Attrib->NewInstance($this);

			if($Inst instanceof PropertyOrigin)
			$this->Origin = $Inst->Name;

			elseif($Inst instanceof PropertyObjectify)
			$this->Objectify = $Inst;
		}

		return;
	}

}
