<?php

namespace Nether\Object;

use ReflectionClass;
use Nether\Object\Prototype\Flags;
use Nether\Object\Prototype\ConstructArgs;

class Prototype {
/*//
@date 2021-08-05
provides a self-sealing stem object to build from where you can trust that the
properties you need will exist, prefilled with a default value if needed. this
class and its supports have been micro-optimized to have the most minimal
impact i can find while packing in as many features as possible.
//*/

	public function
	__Construct(array|object|NULL $Raw=NULL, array|object|NULL $Defaults=NULL, int $Flags=0) {
	/*//
	@date 2021-08-05
	@mopt busyunit, isset, avoid-obj-prop-rw
	//*/

		// this constructor is going to do the bulk of the work to avoid
		// polluting the base object with excess properties and methods
		// that could get sucked up by documentation systems while being
		// pointless.

		// there has also been a lot of micro-optimizations made in this
		// entire flow in regards to the read and write speed of object
		// properties and checking if a key exists.

		if(is_object($Raw))
		$Raw = (array)$Raw;

		if(is_object($Defaults))
		$Defaults = (array)$Defaults;

		////////

		$Properties = static::GetPropertyAttributes();
		$StrictDefaults = ($Flags & Flags::StrictDefault) !== 0;
		$CullUsingDefaults = ($Flags & Flags::CullUsingDefault) !== 0;
		$StrictInput = ($Flags & Flags::StrictInput) !== 0;

		$Src = NULL;
		$Val = NULL;
		$Key = NULL;

		// loop over the default data for population.

		if($Defaults !== NULL)
		foreach($Defaults as $Src => $Val) {
			if($StrictDefaults && !property_exists($this,$Src))
			continue;

			$this->{$Src} = $Val;
		}

		// loop over the supplied data for population.

		if($Raw !== NULL)
		foreach($Raw as $Src => $Val) {
			// start off writing to the same property its keyed to by
			// default.

			$Key = $Src;

			if(isset($Properties[$Src])) {
				// if there is an attribute for the source property
				// update the destination property name.

				$Key = $Properties[$Src]->Name;

				// check if the value needs to be typecast.

				if($Properties[$Src]->Castable)
				settype($Val,$Properties[$Src]->Type);
			}

			// if StrictInput then do not assign any properties that
			// are not hardcoded on the class.

			if($StrictInput)
			if(!property_exists($this,$Key))
			continue;

			// if CullUsingDefaults then do not assign any properties
			// that are not also mapped in the defaults. honestly i think
			// this is stupid and might be removed.

			if($CullUsingDefaults)
			if(!array_key_exists($Key,$Defaults))
			continue;

			$this->{$Key} = $Val;
		}

		// apply any follow up attribute demands.

		foreach($Properties as $Src => $Val) {
			if($Val->Objectify)
			$this->{$Val->Name} = new ($Val->Type)(
				...$Val->Objectify->Args
			);
		}

		// as handy as it was to create this ConstructArgs first thing
		// and have it do the sanitization and stuff i had to refactor this
		// to micro-optimize by avoiding to access object members. having
		// this up there makes it too obviously tempting to use it instead
		// of creating local variables.

		$this->OnReady(new ConstructArgs(
			$Raw,
			$Defaults,
			$Flags,
			$Properties
		));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(ConstructArgs $Args):
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
	@mopt isset, direct read, direct write.
	builds a structure indexed by the origin value of the property that
	describes all the various things we need to respect.
	//*/

		if(isset(Prototype\PropertyCache::$Cache[static::class]))
		return Prototype\PropertyCache::$Cache[static::class];

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

		return Prototype\PropertyCache::$Cache[static::class] = $Output;
	}

}
