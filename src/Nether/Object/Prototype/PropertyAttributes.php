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
	$Name = '';

	public ?string
	$Origin = NULL;

	public string
	$Type = 'mixed';

	public bool
	$Castable = FALSE;

	public ?PropertyObjectify
	$Objectify = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(ReflectionProperty $Prop) {

		$Type = $Prop->GetType();
		$Attrib = NULL;
		$Inst = NULL;

		// get some various info.

		$this->Name = $Prop->GetName();
		$this->Origin = $this->Name;
		$this->Type = $Type->GetName();

		// determine if it can be progamatically typecast.

		$this->Castable = (
			$Type instanceof ReflectionNamedType
			&& $Type->IsBuiltIn()
			&& $this->Type !== 'mixed'
		);

		// get all the attributes.

		foreach($Prop->GetAttributes() as $Attrib) {
			$Inst = $Attrib->NewInstance();

			if($Inst instanceof PropertyOrigin) {
				$this->Origin = $Inst->Name;
			}

			elseif($Inst instanceof PropertyObjectify) {
				$this->Objectify = $Inst;
			}
		}

		return;
	}

}
