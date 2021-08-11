<?php

require('vendor/autoload.php');
$Argv = $_SERVER['argv'];

if(count($Argv) < 2) {
	echo PHP_EOL;
	echo "usage: {$_SERVER['SCRIPT_NAME']} <testnum> <count>", PHP_EOL;
	echo "       1 = old mapped object", PHP_EOL;
	echo "       2 = new prototype object (vs #1 (faster))", PHP_EOL;
	echo "       3 = prototype objectify via OnReady", PHP_EOL;
	echo "       4 = prototype objectify via attribute (vs #3 (slower))", PHP_EOL;
	echo PHP_EOL;
	exit(0);
}

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
	'user_title' => 'Chief Iconoclast'
];

if(count($Argv) >= 3 && $Argv[2])
$IterMax = (int)$Argv[2];

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

	#[Nether\Object\Meta\PropertyOrigin('user_id')]
	public int $ID;

	#[Nether\Object\Meta\PropertyOrigin('user_name')]
	public string $Name;

	#[Nether\Object\Meta\PropertyOrigin('user_email')]
	public string $Email;

	#[Nether\Object\Meta\PropertyOrigin('user_title')]
	public string $Title;

};

class User3
extends Nether\Object\Prototype {

	public Nether\Object\Datastore
	$Data;

	protected function
	OnReady(Nether\Object\Prototype\ConstructArgs $Args):
	void {

		$this->Data = new Nether\Object\Datastore;
		return;
	}

};

class User4
extends Nether\Object\Prototype {

	#[Nether\Object\Meta\PropertyObjectify]
	public Nether\Object\Datastore
	$Data;

};

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


$Rounds = 4;
$Iter = 0;

$Round = 0;
$Total = 0;
$Current = 0;
$Stop = 0;
$Start = 0;

printf(
	'Class: %s, Count: %s... ',
	$ClassName,
	number_format($IterMax),
	PHP_EOL
);

$Round = $Rounds;
while($Round--) {
	$Start = microtime(TRUE);
		for($Iter = 0; $Iter < $IterMax; $Iter++)
		new $ClassName($Input);
	$Stop = microtime(TRUE);

	printf(
		'[%.6fs] ',
		($Current = $Stop - $Start)
	);

	$Total += $Current;
}

printf(
	'%sAverage: %.6fs %s%s',
	PHP_EOL,
	($Total / $Rounds),
	PHP_EOL,
	PHP_EOL
);
