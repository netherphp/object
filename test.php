<?php

require('vendor/autoload.php');

$Object = new class([]) extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertyOrigin('user_id')]
	public int $ID = 0;

	#[Nether\Object\Meta\PropertyObjectify]
	public Nether\Object\Datastore $Data;

};

print_r($Object);
