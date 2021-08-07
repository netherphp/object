<?php

namespace Nether\Object;
use Nether;

use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;

use Nether\Object\PropertyMap;
use Nether\Object\PrototypeFlags;
use Nether\Object\Meta\PropertySource;

class Prototype {
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
			if(($Flags & PrototypeFlags::StrictDefault) !== 0)
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

			if(($Flags & PrototypeFlags::CullUsingDefault) !== 0)
			if(is_array($Default) && !array_key_exists($Src,$Default))
			continue;

			if(($Flags & PrototypeFlags::StrictInput) !== 0)
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
		$Name = NULL;
		$Castable = FALSE;
		$Attribs = NULL;
		$Attrib = NULL;
		$Inst = NULL;
		$Map = [];

		////////

		$RefClass = new ReflectionClass(static::class);

		$Props = $RefClass->GetProperties(
			ReflectionProperty::IS_PUBLIC |
			ReflectionProperty::IS_PROTECTED |
			ReflectionProperty::IS_PRIVATE
		);

		foreach($Props as $Prop) {
			$Name = $Prop->GetName();
			$Type = $Prop->GetType();
			$Attribs = $Prop->GetAttributes();
			$Castable = (
				$Type instanceof ReflectionNamedType &&
				$Type->IsBuiltIn() &&
				$Type->GetName() !== 'mixed'
			);

			// add this property to the map if there are no attributes.

			if(!count($Attribs)) {
				$Map[$Name] = [
					PropertyMap::Name => $Name,
					PropertyMap::Type => $Castable ? $Type->GetName() : NULL
				];
				continue;
			}

			foreach($Attribs as $Attrib) {
				$Inst = $Attrib->NewInstance();

				if($Inst instanceof PropertySource) {
					$Map[$Inst->Name] = [
						PropertyMap::Name => $Prop->GetName(),
						PropertyMap::Type => NULL
					];

					if($Type instanceof ReflectionNamedType)
					if($Type->IsBuiltin())
					$Map[$Inst->Name][PropertyMap::Type] = $Type->GetName();

					continue;
				}
			}
		}

		return PropertyMap::Set(static::class, $Map);
	}

}
