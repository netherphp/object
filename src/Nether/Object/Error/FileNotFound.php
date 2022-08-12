<?php

namespace Nether\Object\Error;

use Exception;

class FileNotFound
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct("file {$Filename} not found");
		return;
	}

}
