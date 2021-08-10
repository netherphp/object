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

		// there has also been a lot of micro-optimizations made in this
		// entire flow in regards to the read and write speed of object
		// properties.

		$StrictDefaults = ($Flags & Flags::StrictDefault) !== 0;
		$CullUsingDefaults = ($Flags & Flags::CullUsingDefault) !== 0;
		$StrictInput = ($Flags & Flags::StrictInput) !== 0;
		$Properties = NULL;

		$Args = new Prototype\ConstructArgs(
			$Raw,
			$Defaults,
			$Flags,
			$Properties = static::GetPropertyAttributes()
		);

		$Src = NULL;
		$Val = NULL;
		$Key = NULL;

		// loop over the default data for population.

		if($Args->Defaults !== NULL)
		foreach($Args->Defaults as $Src => $Val) {
			if($StrictDefaults && !property_exists($this,$Src))
			continue;

			$this->{$Src} = $Val;
		}

		// loop over the supplied data for population.

		if($Args->Input !== NULL)
		foreach($Args->Input as $Src => $Val) {
			$Key = $Src;

			// special cases from attributes.

			if(array_key_exists($Src,$Properties)) {
				$Key = $Properties[$Src]->Name;

				if($Properties[$Src]->Castable)
				settype($Val,$Properties[$Src]->Type);
			}

			// cases for culling.

			if($CullUsingDefaults)
			if(!array_key_exists($Key,$Args->Defaults))
			continue;

			if($StrictInput)
			if(!property_exists($this,$Key))
			continue;

			$this->{$Key} = $Val;
		}

		// apply any follow up attribute demands.

		if($Properties !== NULL)
		foreach($Properties as $Src => $Val) {
			if($Val->Objectify)
			$this->{$Val->Name} = new ($Val->Type)(
				...$Val->Objectify->Args
			);
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
