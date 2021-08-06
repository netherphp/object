<?php

require('src/Nether/Object/PropertyMapCache.php');
require('src/Nether/Object/Mapped.php');
require('src/Nether/Object/Mapped2.php');
require('src/Nether/Object/Meta/PropertySource.php');

$Which = sprintf('Whatever%d',$_SERVER['argv'][1]);

class Whatever1
extends Nether\Object\Mapped {

	static protected
	$PropertyMap = [
		'user_id'    => 'ID:int',
		'user_name'  => 'Name',
		'user_email' => 'Email',
		'user_title' => 'Title'
	];

	public int
	$ID = 0;

	public ?string
	$Name = NULL;

	public ?string
	$Email = NULL;

	public ?string
	$Title = NULL;

}

class Whatever2
extends Nether\Object\Mapped2 {

	#[Nether\Object\Meta\PropertySource('user_id')]
	public int
	$ID = 0;

	#[Nether\Object\Meta\PropertySource('user_name')]
	public ?string
	$Name = NULL;

	#[Nether\Object\Meta\PropertySource('user_email')]
	public ?string
	$Email = NULL;

	#[Nether\Object\Meta\PropertySource('user_title')]
	public ?string
	$Title = NULL;

}

class Whatever3
extends Whatever2 {

	#[Nether\Object\Meta\PropertySource('user_time')]
	public ?int
	$Time = NULL;

}

class Whatever4
extends Whatever3 {

	#[Nether\Object\Meta\PropertySource('user_banned')]
	public ?int
	$Banned = NULL;

}

$End = 0;
$Iter = 0;
$Obj = NULL;
$Start = microtime(true);

for($Iter = 0; $Iter < 100000; $Iter++)
$Obj = new $Which(
	[ 'user_id'=> 1, 'user_name'=> 'bob', 'user_email'=> 'bob@majdak.net', 'user_time'=> 1234 ],
	[ 'Title'=> 'lame' ]
);

$End = microtime(true);
echo $Which , ' ', $Iter, PHP_EOL;
echo $End - $Start, 'sec', PHP_EOL;
print_r($Obj);

