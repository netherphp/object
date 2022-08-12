<?php

namespace Nether\Object\Error;

use Exception;

class FileUnreadable
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct("file {$Filename} is unreadable");
		return;
	}

}
