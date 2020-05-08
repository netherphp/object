<?php

namespace
Nether\Object;

use \Nether as Nether;

////////////////
////////////////

class
Mapped {

	static protected
	$PropertyMap = NULL;
	/*//
	@type array
	@date 2017-08-17
	holds a mapping index that is used to convert objects or arrays into nice
	looking objects via late-static binding.
	//*/

	static public function
	GetPropertyMap():
	?Array {
	/*//
	@date 2017-08-17
	@updated 2017-08-17
	allow reading of the property map.
	//*/

		return static::$PropertyMap;
	}

	static public function
	GetMappedPropertyList():
	Array {
	/*//
	@date 2019-07-03
	read the property map, but return like the final results of the right
	side of the map with the types stripped.
	//*/

		$Old = NULL;
		$New = NULL;

		$Output = [];

		if(!is_array(static::$PropertyMap))
		return $Output;

		foreach(static::$PropertyMap as $Old => $New)
		$Output[] = static::__GetTypecastedPropertyName($New);

		return $Output;
	}

	static public function
	MergePropertyMap(Array $More, Bool $Create=FALSE):
	Void {
	/*//
	@date 2017-08-17
	@updated 2017-08-17
	allow merging additional property maps into this class property map.
	//*/

		if(!static::$PropertyMap) {
			if(!$Create)
			return;

			static::$PropertyMap = [];
		}

		static::$PropertyMap = array_merge(
			static::$PropertyMap,
			$More
		);

		return;
	}

	////////////////
	////////////////

	public function
	__Construct($Input=NULL, $Default=NULL, $Opt=NULL) {
	/*//
	@date 2014-03-14
	@updated 2017-08-17
	//*/

		$Key = NULL;
		$Val = NULL;

		if(is_object($Input))
		$Input = (Array)$Input;

		if(is_object($Default))
		$Default = (Array)$Default;

		if(is_object($Opt))
		$Opt = (Array)$Opt;

		////////

		if(!is_array($Opt))
		$Opt = [];

		if(!array_key_exists('MappedKeysOnly',$Opt))
		$Opt['MappedKeysOnly'] = TRUE;

		if(!array_key_exists('DefaultKeysOnly',$Opt))
		$Opt['DefaultKeysOnly'] = FALSE;

		if(!array_key_exists('ApplyDefaultTypes',$Opt))
		$Opt['ApplyDefaultTypes'] = TRUE;

		if(!array_key_exists('ForceDefaultValues',$Opt))
		$Opt['ForceDefaultValues'] = FALSE;

		////////////////
		////////////////

		// apply default values.

		if(is_array($Default)) {
			foreach($Default as $Key => $Val)
			$this->__ApplyTypecastedProperty($Key,$Val);
		}

		// map and apply input values.

		if(is_array($Input)) {
			foreach($Input as $Key => $Val) {
				if(static::$PropertyMap) {
					// transform the old key into the desired key.
					if(array_key_exists($Key,static::$PropertyMap))
					$Key = static::$PropertyMap[$Key];

					// or drop the value if we don't want mapped data.
					elseif($Opt['MappedKeysOnly'])
					continue;
				}

				if($Opt['DefaultKeysOnly'] && $Default) {
					if(!array_key_exists($this->__GetTypecastedPropertyName($Key),$Default))
					continue;
				}

				$this->__ApplyTypecastedProperty($Key,$Val);
			}
		}

		////////////////
		////////////////

		if(method_exists($this,'__ready'))
		$this->__ready($Input,$Default,$Opt);

		if(method_exists($this,'OnReady'))
		$this->OnReady($Input,$Default,$Opt);

		return;
	}

	////////////////
	////////////////

	protected function
	__ApplyTypecastedProperty(String $Property, $Value) {
	/*//
	@date 2014-12-16
	@updated 2017-08-17
	given a property name which may contain typecast notation, apply that value
	to this object with any typecasting.
	//*/

		$Typecast = 'none';

		////////

		if(strpos($Property,':') !== FALSE) {
			$Typecast = strstr($Property,':',FALSE);
			$Property = strstr($Property,':',TRUE);
		}

		////////

		switch(strtolower($Typecast)) {
			case ':none':
				$this->{$Property} = $Value;
			break;

			case ':bool':
			case ':boolean':
				$this->{$Property} = (Bool)$Value;
			break;

			case ':int':
			case ':integer':
				$this->{$Property} = (Int)$Value;
			break;

			case ':double':
			case ':float':
				$this->{$Property} = (Double)$Value;
			break;

			case ':str':
			case ':string':
				$this->{$Property} = (String)$Value;
			break;

			case ':obj':
			case ':object':
				$this->{$Property} = (Object)$Value;
			break;

			case ':arr':
			case ':array':
				$this->{$Property} = (Array)$Value;
			break;

			default:
				$this->{$Property} = $Value;
			break;
		}

		return;
	}

	////////////////
	////////////////

	static public function
	__GetTypecastedPropertyName(String $Property) {
	/*//
	@date 2014-12-16
	@updated 2017-08-17
	given a property name which may contain typecasting, return only the valid
	property name portion of the property definition.
	//*/

		return ((strstr($Property,':',TRUE))?:($Property));
	}

}
