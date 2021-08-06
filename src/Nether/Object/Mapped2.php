<?php

namespace Nether\Object;

use Nether;

use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;
use Nether\Object\PropertyMapCache;
use Nether\Object\Meta\PropertySource;

class Mapped2 {

	const
	PMapName = 0,
	PMapType = 1;

	public function
	__Construct(array $Raw, ?array $Default=NULL) {
	/*//
	@date 2021-08-05
	//*/

		$PropertyMap = $this::GetPropertyMap();
		$Src = NULL;
		$Dest = NULL;
		$Val = NULL;

		// apply any default values that were supplied.

		if(is_array($Default) || is_object($Default))
		foreach($Default as $Src => $Dest)
		$this->{$Src} = $Dest;

		// apply any mapped values that were supplied.

		if(is_array($PropertyMap))
		foreach($PropertyMap as $Src => $Dest) {
			if(!property_exists($this,$Dest[$this::PMapName]))
			continue;

			if(!array_key_exists($Src,$Raw))
			continue;

			$Val = $Raw[$Src];

			if($Dest[$this::PMapType])
			settype($Val,$Dest[$this::PMapType]);

			$this->{$Dest[$this::PMapName]} = $Val;
		}

		$this->OnReady();
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady():
	void {

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	GetPropertyMap():
	array {
	/*//
	@date 2021-08-05
	//*/

		if(PropertyMapCache::Has(static::class))
		return PropertyMapCache::Get(static::class);

		////////

		$RefClass = NULL;
		$Props = NULL;
		$Prop = NULL;
		$Type = NULL;
		$Attribs = NULL;
		$Attrib = NULL;
		$Inst = NULL;
		$Output = [];

		////////

		$RefClass = new ReflectionClass(static::class);

		$Props = $RefClass->GetProperties(
			ReflectionProperty::IS_PUBLIC |
			ReflectionProperty::IS_PROTECTED |
			ReflectionProperty::IS_PRIVATE
		);

		foreach($Props as $Prop) {
			$Type = $Prop->GetType();
			$Attribs = $Prop->GetAttributes(PropertySource::class);

			foreach($Attribs as $Attrib) {
				$Inst = $Attrib->NewInstance();
				$Output[$Inst->Name] = [ $Prop->GetName(), NULL ];

				if($Type instanceof ReflectionNamedType)
				if($Type->IsBuiltin())
				$Output[$Inst->Name][1] = $Type->GetName();
			}
		}

		return PropertyMapCache::Set(static::class,$Output);
	}

}
