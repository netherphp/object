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

	public function __construct($input=null,$defaults=null) {
		if(is_array($input)) $input = (object)$input;

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
		if(is_array($defaults)) $defaults = (object)$defaults;
		if(is_object($defaults)) {
			$this->__apply_property_defaults($defaults,false);
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

			// allow typecasting by means of new:type in the PropertyMap
			// value for that property.
			if(strpos($new,':')) {
				list($new,$typecast) = explode(':',$new);
			} else {
				$typecast = 'none';
			}

			if(property_exists($input,$old)) {
				switch($typecast) {
					case 'bool': { }
					case 'boolean': {
						$this->{$new} = (bool)$input->{$old};
						break;
					}

					case 'int': { }
					case 'integer': {
						$this->{$new} = (int)$input->{$old};
						break;
					}

					case 'double': { }
					case 'float': {
						$this->{$new} = (double)$input->{$old};
						break;
					}

					case 'str': { }
					case 'string': {
						$this->{$new} = (string)$input->{$old};
						break;
					}

					default: {
						$this->{$new} = $input->{$old};
						break;
					}
				}

//				unset($input->{$old});
			}
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
			if(property_exists($this,$property) && !$overwrite) continue;
			$this->{$property} = $value;
		}

		return;
	}

}
