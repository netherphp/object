<?php

require('vendor/autoload.php');

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

$IterMax = 100000;

$ClassName = sprintf(
	'User%d',
	$_SERVER['argv'][1]
);

$Input = [
	'user_id'    => 42,
	'user_name'  => 'bobmagicii',
	'user_email' => 'bmajdak@php.net',
	'user_title' => 'Professional Iconoclast'
];

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class User1
extends Nether\Object\Mapped {

	protected static
	$PropertyMap = [
		'user_id'    => 'ID:int',
		'user_name'  => 'Name',
		'user_email' => 'Email',
		'user_title' => 'Title'
	];

};

class User2
extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int $ID;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public string $Name;

	#[Nether\Object\Meta\PropertySource('user_email')]
	public string $Email;

	#[Nether\Object\Meta\PropertySource('user_title')]
	public string $Title;

};

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

$Iter = 0;
$Stop = 0;
$Start = 0;

printf(
	'Class: %s, Count: %s... ',
	$ClassName,
	$IterMax
);

$Start = microtime(TRUE);
	for($Iter = 0; $Iter < $IterMax; $Iter++)
	new $ClassName($Input);
$Stop = microtime(TRUE);

printf(
	'[%.6fs]%s',
	($Stop - $Start),
	PHP_EOL
);
