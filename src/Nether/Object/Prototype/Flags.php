<?php

namespace Nether\Object\Prototype;

class Flags {
/*//
@date 2021-08-05
@todo 2021-08-11 convert to enum in php 8.1
//*/

	const
	StrictDefault    = (1 << 0),
	StrictInput      = (1 << 1),
	CullUsingDefault = (1 << 2);

}
