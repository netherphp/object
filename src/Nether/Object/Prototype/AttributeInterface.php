<?php

namespace Nether\Object\Prototype;
use Nether\Object\Prototype\PropertyAttributes;

interface AttributeInterface {
/*//
@date 2021-08-11
currently the only good way to filter attributes our framework owns is to give
them interfaces and to check that after asking reflection for the list.
//*/

	public function
	OnPropertyAttributes(PropertyAttributes|PropertyInfo $Attrib);
	/*//
	@date 2021-08-11
	implement this method in your attribute to make it do something when
	this attribute is indexed by the prototype system. example: setting
	a property on the PropertyAttribute object passed as an argument from
	data calculated by this attribute.
	//*/

}
