<?php

namespace Nether\Object;
use Nether;

use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;

use Nether\Object\PropertyMap;
use Nether\Object\Prototype\Flags;
use Nether\Object\Meta\PropertyOrigin;
use Nether\Object\Meta\PropertyObjectify;

class Prototype {
/*//
@date 2021-08-05
provides a self-sealing stem object to build from where you can trust that the
properties you need will exist, prefilled with a default value if needed.
//*/

	public function
	__Construct(array|object|NULL $Raw=NULL, array|object|NULL $Defaults=NULL, int $Flags=0) {
	/*//
	@date 2021-08-05
	//*/

		// this constructor is going to do the bulk of the work to avoid
		// polluting the base object with excess properties and methods
		// that could get sucked up by documentation systems while being
		// pointless.

		$Args = new Prototype\ConstructArgs(
			$Raw,
			$Defaults,
			$Flags,
			static::GetPropertyAttributes()
		);

		$Src = NULL;
		$Val = NULL;
		$Key = NULL;

		// loop over the default data for population.

		if($Args->Defaults !== NULL)
		foreach($Args->Defaults as $Src => $Val) {
			if($Args->StrictDefault && !property_exists($this,$Src))
			continue;

			$this->{$Src} = $Val;
		}

		// loop over the supplied data for population.

		if($Args->Raw !== NULL)
		foreach($Args->Raw as $Src => $Val) {
			$Key = $Src;

			// special cases from attributes.

			if(array_key_exists($Src,$Args->Properties)) {
				$Key = $Args->Properties[$Src]->Name;

				if($Args->Properties[$Src]->Castable)
				settype($Val,$Args->Properties[$Src]->Type);
			}

			// cases for culling.

			if($Args->CullUsingDefault && !array_key_exists($Key,$Args->Defaults))
			continue;

			if($Args->StrictInput && !property_exists($this,$Key))
			continue;

			$this->{$Key} = $Val;
		}

		// apply any follow up attribute demands.

		if($Args->Properties !== NULL)
		foreach($Args->Properties as $Src => $Val) {
			if($Val->Objectify)
			$this->{$Val->Name} = new ($Val->Type)(...$Val->Objectify->Args);
		}

		// release the kraken.

		$this->OnReady($Args);
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Prototype\ConstructArgs $Args):
	void {
	/*//
	@date 2021-08-09
	this can/should overriden by children to add construct-time
	processing.
	//*/

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

		$Output = [];
		$RefClass = NULL;
		$Prop = NULL;
		$Attrib = NULL;

		////////

		$RefClass = new ReflectionClass(static::class);

		foreach($RefClass->GetProperties() as $Prop) {
			$Attrib = new Prototype\PropertyAttributes($Prop);
			$Output[$Attrib->Origin] = $Attrib;
		}

		return Prototype\PropertyCache::Set(static::class, $Output);
	}

}
