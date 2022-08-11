<?php

namespace Nether\Object\Prototype;

use ReflectionAttribute;

class SmartAttribute {

	protected mixed
	$RefObject = NULL;

	protected mixed
	$RefAttrib = NULL;

	public function
	LearnAboutYourselfFFS(mixed $RefObject, ?ReflectionAttribute $RefAttrib):
	void {

		$this->RefObject = $RefObject;
		$this->RefAttrib = $RefAttrib;

		return;
	}

}
