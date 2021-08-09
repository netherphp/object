<?php

namespace Nether\Object\Prototype;

use ReflectionProperty;
use ReflectionNamedType;
use Nether\Object\Meta\PropertyObjectify;
use Nether\Object\Meta\PropertySource;

class PropertyAttributes {

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

	public ?array
	$Meta = NULL;

	public function
	__Construct(ReflectionProperty $Prop, bool $KeepMetaObjects=FALSE) {

		$Attrib = NULL;
		$Inst = NULL;
		$Type = $Prop->GetType();

		// get some various info.

		$this->Name = $this->Origin = $Prop->GetName();
		$this->Type = $Type->GetName();
		$this->Meta = $KeepMetaObjects ? [] : NULL;

		// determine if it can be progamatically typecast.

		$this->Castable = (
			$Type instanceof ReflectionNamedType &&
			$Type->IsBuiltIn() &&
			$Type->GetName() !== 'mixed'
		);

		// get all the attributes.

		foreach($Prop->GetAttributes() as $Attrib) {
			$Inst = $Attrib->NewInstance();

			if($Inst instanceof PropertySource)
			$this->Origin = $Inst->Name;

			if($Inst instanceof PropertyObjectify) {
				$this->Objectify = $Inst;
			}

			if($KeepMetaObjects)
			$this->Meta[] = $Inst;
		}

		return;
	}

}
