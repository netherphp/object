<?php

namespace Nether;
use \Nether;

////////////////
////////////////

class Object {
/*//
this class is designed as a utility to easily create nicely organized objects
from arrays or objects which may be cumbersome to use. it has the ability to
remap properties to new names and make sure that any default data that needs
to be set in properties are set. take for example this object that was fetched
from a database.

	{ "u_id":4, "u_name":"bob", "u_email":"bob@localhost" }

we can define a class called User that extends Object with the following
PropertyMap

	class User extends Object {
		static $PropertyMap = [
			'u_id'    => 'ID',
			'u_name'  => 'Name',
			'u_email' => 'Email',
			'u_admin' => 'Admin'
		];

		// user methods...

	}

we can easily convert that database row into a nice object.

	$user = new User($row);
	var_dump($user);

we can also make sure that any missing data is automatically filled in. in this
example our database row did not have a field called u_admin but we did define
one in the property map. because it did not exist the property Admin never would
have been created. to make sure it exists we can specify an object or array that
contains default values that need to be there.

	$user = new User(
		$row,
		['Admin'=>false]
	);
	var_dump($user);

in the very least we can now trust that the property Admin will exist if it did
not get populated by the database and will default to false.
//*/


	static $PropertyMap = array();
	/*//
	@type array
	holds a mapping index that is used to convert objects or arrays into nice
	looking objects via late-static binding.
	//*/

	////////////////
	////////////////

	public function __construct($input=null,$defaults=null,$opt=null) {
		if(is_array($input)) $input = (object)$input;
		if(is_array($defaults)) $defaults = (object)$defaults;

		// we can't use our self here to make this cleanlike or else we will
		// recurisve forever. ^_^
		if(!is_array($opt) && !is_object($opt)) $opt = [];
		if(!array_key_exists('DefaultKeysOnly',$opt)) $opt['DefaultKeysOnly'] = false;
		if(!array_key_exists('ApplyDefaultTypes',$opt)) $opt['ApplyDefaultTypes'] = true;

		// initialize the object with the input data, running the input
		// by the property map first if need be.
		if(is_object($input)) {
			if(is_array(static::$PropertyMap) && count(static::$PropertyMap)) {
				// if we have an input map then we will only map what it says.
				$this->__apply_property_map($input);
			} else {
				// if we do not have an input map then we will one-for-one it.
				$this->__apply_property_defaults($input,true);
			}
		}

		// set any default properties that may have been missing from
		// the original input data.

		if(is_object($defaults)) {
			$this->__apply_property_defaults($defaults,false);

			// optionally stripping out any properties not defined by the
			// default map.
			if($opt['DefaultKeysOnly'])
			$this->__apply_property_defaults_stripping($defaults);

			// optionally forcing any types defined by the defaults onto any
			// data that exists in the properties.
			if($opt['ApplyDefaultTypes'])
			$this->__apply_property_defaults_types($defaults);
		}

		// an experimental idea, allow this object to self-ready itself if
		// a ready psuedomagical method was defined.
		if(method_exists($this,'__ready')) $this->__ready();

		return;
	}

	////////////////
	////////////////

	protected function __apply_property_map($input) {
	/*//
	@argv array Input
	uses the PropertyMap to remap the input data to the specified properties
	in this object. also allows for typecasting the data. to typecast add a
	:type to the right half of the map.
	//*/

		foreach(static::$PropertyMap as $old => $new) {
			if(property_exists($input,$old))
			$this->__apply_typecasted_property($new,$input->{$old});
		}

		return;
	}

	protected function __apply_property_defaults($source,$overwrite=false) {
	/*//
	@argv array Source, boolean Overwrite default false
	insures that if a set of default property values were given that those
	properties are set in this object.
	//*/

		foreach($source as $property => $value) {
			if(property_exists($this,self::__get_typecasted_property_name($property)) && $overwrite)
				$this->__apply_typecasted_property($property,$value);
			elseif(!property_exists($this,self::__get_typecasted_property_name($property)))
				$this->__apply_typecasted_property($property,$value);
		}

		return;
	}

	protected function __apply_property_defaults_types($source) {
	/*//
	@argv array Source
	using any typecast notations in the source array, apply those typecasts
	to the properties currently set.
	//*/

		foreach($source as $property => $value) {
			if(property_exists($this,$prop = self::__get_typecasted_property_name($property)))
			$this->__apply_typecasted_property($property,$this->{$prop});
		}

		return;
	}

	protected function __apply_property_defaults_stripping($source) {
	/*//
	@argv array Source
	remove any properties from this object which are not defined by the defaults
	source array.
	//*/

		$keepers = [];

		foreach($source as $property => $value)
		$keepers[] = self::__get_typecasted_property_name($property);

		foreach($this as $property => $value)
		if(!in_array($property,$keepers))
		unset($this->{$property});

		return;
	}

	protected function __apply_typecasted_property($property,$value) {
	/*//
	@argv string Property, mixed Value
	given a property name which may contain typecast notation, apply that value
	to this object with any typecasting.
	//*/

		if(strpos($property,':') !== false) {
			list($property,$typecast) = explode(':',$property);
		} else {
			$typecast = 'none';
		}

		//echo "<pre>";
		//var_dump($property,$typecast,$value);
		//echo "</pre>";

		switch($typecast) {
			case 'none': {
				$this->{$property} = $value;
				break;
			}

			case 'bool': { }
			case 'boolean': {
				$this->{$property} = (bool)$value;
				break;
			}

			case 'int': { }
			case 'integer': {
				$this->{$property} = (int)$value;
				break;
			}

			case 'double': { }
			case 'float': {
				$this->{$property} = (double)$value;
				break;
			}

			case 'str': { }
			case 'string': {
				$this->{$property} = (string)$value;
				break;
			}

			default: {
				$this->{$property} = $value;
				break;
			}
		}

		return;
	}

	static function __get_typecasted_property_name($property) {
	/*//
	@argv string Property
	given a property name which may contain typecasting, return only the valid
	property name portion of the property definition.
	//*/

		return ((strstr($property,':',true))?:($property));
	}

}
