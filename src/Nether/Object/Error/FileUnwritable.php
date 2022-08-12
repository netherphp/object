<?php

namespace Nether\Object\Error;

use Exception;

class FileUnwritable
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct("file {$Filename} is unwritable");
		return;
	}

}
