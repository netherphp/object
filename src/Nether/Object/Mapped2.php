<?php

namespace Nether\Object;
use Nether;

use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;

use Nether\Object\PropertyMap;
use Nether\Object\ObjectFlags;
use Nether\Object\Meta\PropertySource;

class Mapped2 {
/*//
@date 2021-08-05
provides a self-sealing stem object to build from where you can trust that the
properties you need will exist, prefilled with a default value if needed.
//*/

	public function
	__Construct(?array $Raw=NULL, ?array $Default=NULL, int $Flags=0) {
	/*//
	@date 2021-08-05
	//*/

		// this constructor is going to do the bulk of the work to avoid
		// polluting the base object with excess properties and methods
		// that could get sucked up by documentation systems while being
		// pointless.

		$PropertyMap = static::GetPropertyMap();
		$Raw ??= [];
		$Src = NULL;
		$Val = NULL;

		// apply any default values that were supplied.

		if($Default !== NULL)
		foreach($Default as $Src => $Val) {
			if(($Flags & ObjectFlags::StrictDefault) !== 0)
			if(!property_exists($this,$Src))
			continue;

			$this->{$Src} = $Val;
		}

		// apply any mapped property values.

		foreach($Raw as $Src => $Val) {
			if(array_key_exists($Src,$PropertyMap)) {
				if($PropertyMap[$Src][PropertyMap::Type])
				settype($Val,$PropertyMap[$Src][PropertyMap::Type]);

				$this->{$PropertyMap[$Src][PropertyMap::Name]} = $Val;
				continue;
			}

			if(($Flags & ObjectFlags::CullUsingDefault) !== 0)
			if(is_array($Default) && !array_key_exists($Src,$Default))
			continue;

			if(($Flags & ObjectFlags::StrictInput) !== 0)
			if(!property_exists($this,$Src))
			continue;

			$this->{$Src} = $Val;
		}

		$this->OnReady($Raw,$Default,$Flags);
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(?array $Raw=NULL, ?array $Default=NULL, int $Flags=0):
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

		if(PropertyMap::Has(static::class))
		return PropertyMap::Get(static::class);

		////////

		$RefClass = NULL;
		$Props = NULL;
		$Prop = NULL;
		$Type = NULL;
		$Attribs = NULL;
		$Attrib = NULL;
		$Inst = NULL;
		$Map = NULL;

		////////

		$RefClass = new ReflectionClass(static::class);
		$Map = [];

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
				$Map[$Inst->Name] = [
					PropertyMap::Name => $Prop->GetName(),
					PropertyMap::Type => NULL
				];

				if($Type instanceof ReflectionNamedType)
				if($Type->IsBuiltin())
				$Map[$Inst->Name][PropertyMap::Type] = $Type->GetName();
			}
		}

		return PropertyMap::Set(static::class, $Map);
	}

}
