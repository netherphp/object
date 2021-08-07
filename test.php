<?php

require('vendor/autoload.php');

$RowFromDB = [
	'user_id'    => '1',
	'user_name'  => 'bob',
	'user_email' => 'bmajdak-at-php-dot-net',
	'user_title' => 'Chief Iconoclast'
];

$Defaults = [
	'Status' => 'Probably Cool'
];

class User1
extends Nether\Object\Prototype {

}

class User2
extends Nether\Object\Prototype {

	public int $user_id;
	public string $user_name;
	public string $user_email;
	public string $user_title;

}

class User3
extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int $ID;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public string $Name;

	#[Nether\Object\Meta\PropertySource('user_email')]
	public string $Email;

	#[Nether\Object\Meta\PropertySource('user_title')]
	public string $Title;

}

class User4
extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int $ID;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public string $Name;

	#[Nether\Object\Meta\PropertySource('user_email')]
	public string $Email;

	#[Nether\Object\Meta\PropertySource('user_title')]
	public string $Title;

	#[Nether\Object\Meta\PropertySource('user_status')]
	public string $Status;

}

class User5
extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int $ID;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public string $Name;

}

var_dump(new User1($RowFromDB));
var_dump(new User2($RowFromDB));
var_dump(new User3($RowFromDB));
var_dump(new User4($RowFromDB));
var_dump(new User4($RowFromDB,$Defaults));
var_dump(new User5($RowFromDB));
var_dump(new User5($RowFromDB,NULL,Nether\Object\PrototypeFlags::StrictInput));
