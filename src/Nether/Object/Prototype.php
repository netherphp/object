<?php

namespace Nether\Object;
use Nether;

use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;

use Nether\Object\PropertyMap;
use Nether\Object\Prototype\Flags;
use Nether\Object\Meta\PropertySource;
use Nether\Object\Meta\PropertyObjectify;

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

		$Attributes = static::GetPropertyAttributes();
		$Raw ??= [];
		$Src = NULL;
		$Val = NULL;
		$Key = NULL;

		// apply any default values that were supplied.

		if($Default !== NULL)
		foreach($Default as $Src => $Val) {
			if(($Flags & Prototype\Flags::StrictDefault) !== 0)
			if(!property_exists($this,$Src))
			continue;

			$this->{$Src} = $Val;
		}

		// apply any mapped property values.

		foreach($Raw as $Src => $Val) {
			$Key = $Src;

			if(array_key_exists($Src,$Attributes)) {
				$Key = $Attributes[$Src]->Name;

				if($Attributes[$Src]->Castable)
				settype($Val,$Attributes[$Src]->Type);
			}

			if(($Flags & Prototype\Flags::CullUsingDefault) !== 0)
			if(is_array($Default) && !array_key_exists($Key,$Default))
			continue;

			if(($Flags & Prototype\Flags::StrictInput) !== 0)
			if(!property_exists($this,$Key))
			continue;

			$this->{$Key} = $Val;
		}

		foreach($Attributes as $Src => $Val) {
			if($Val->Objectify)
			$this->{$Val->Name} = new ($Val->Type);
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
	GetPropertyAttributes():
	array {
	/*//
	@date 2021-08-05
	builds a structure indexed by the origin value of the property that
	describes all the various things we need to respect.
	//*/

		if(Prototype\PropertyCache::Has(static::class))
		return Prototype\PropertyCache::Get(static::class);

		$RefClass = NULL;
		$Prop = NULL;
		$Attrib = NULL;
		$Output = [];

		////////

		$RefClass = new ReflectionClass(static::class);
		$PropertyFilter = (
			ReflectionProperty::IS_PUBLIC |
			ReflectionProperty::IS_PROTECTED |
			ReflectionProperty::IS_PRIVATE
		);

		foreach($RefClass->GetProperties($PropertyFilter) as $Prop) {
			$Attrib = new Prototype\PropertyAttributes($Prop);
			$Output[$Attrib->Origin] = $Attrib;
		}

		return Prototype\PropertyCache::Set(static::class, $Output);
	}

}
